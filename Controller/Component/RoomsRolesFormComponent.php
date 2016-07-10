<?php
/**
 * RoomsRolesForm Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * RoomsRolesForm Component
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller
 */
class RoomsRolesFormComponent extends Component {

/**
 * Limit定数
 *
 * @var const
 */
	const DEFAULT_LIMIT = 30;

/**
 * 取得件数
 *
 * @var string
 */
	public $limit = self::DEFAULT_LIMIT;

/**
 * 会員一覧の表示する項目
 *
 * @var const
 */
	public static $displaFields = array(
		'handlename',
		'name',
		'role_key',
		'room_role_key',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Workflow.Workflow'
	);

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param Controller $controller Controller with components to startup
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
 */
	public function startup(Controller $controller) {
		//RequestActionの場合、スキップする
		if (! empty($controller->request->params['requested'])) {
			return;
		}
		$controller->helpers[] = 'Rooms.RoomsRolesForm';

		$this->DefaultRolePermission = ClassRegistry::init('Roles.DefaultRolePermission');
	}

/**
 * Called before the Controller::beforeRender(), and before
 * the view class is loaded, and before Controller::render()
 *
 * @param Controller $controller Controller with components to beforeRender
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRender
 */
	public function beforeRender(Controller $controller) {
		//RequestActionの場合、スキップする
		if (! empty($controller->request->params['requested'])) {
			return;
		}

		//RoomRolePermissionデータセット
		if (isset($this->settings['permissions'])) {
			$roomId = Hash::get($this->settings, 'room_id');
			$type = Hash::get($this->settings, 'type');

			$results = $this->Workflow->getRoomRolePermissions(
				$this->settings['permissions'], $type, $roomId
			);
			$defaultPermissions = Hash::remove($results['DefaultRolePermission'], '{s}.{s}.id');
			$results['RoomRolePermission'] = Hash::merge(
				$defaultPermissions, $results['RoomRolePermission']
			);

			$controller->request->data = Hash::merge($controller->request->data, $results);
			$controller->set('roles', $results['RoomRole']);
		}
	}

/**
 * RoomsRolesUserの登録のアクション
 *
 * @param Controller $controller コントローラ
 * @return null|bool
 */
	public function actionRoomsRolesUser(Controller $controller) {
		//ルームデータチェック
		$room = $controller->viewVars['room'];

		//登録処理
		$result = null;
		if ($controller->request->is('put')) {
			$data = $controller->request->data;
			foreach ($data['User']['id'] as $userId => $checked) {
				if (! $checked) {
					unset($data['RolesRoomsUser'][$userId]);
					continue;
				}
				if (! $data['RolesRoomsUser'][$userId]['id']) {
					$id = $controller->RolesRoomsUser->find('first', array(
						'recursive' => -1,
						'fields' => array('id'),
						'conditions' => array(
							'room_id' => $room['Room']['id'],
							'user_id' => $userId
						)
					));
					$data['RolesRoomsUser'][$userId]['id'] = Hash::get($id, 'RolesRoomsUser.id');
				}
			}

			if (! $data['RolesRoomsUser']) {
				//未選択の場合、
				$result = null;
			} else {
				if ($data['Role']['key'] !== 'delete') {
					$rolesRooms = $controller->Room->getRolesRooms(array(
						'Room.id' => $room['Room']['id'],
						'RolesRoom.role_key' => $data['Role']['key']
					));
					$rolesRoomId = Hash::get($rolesRooms, '0.RolesRoom.id');
					$data['RolesRoomsUser'] = Hash::insert(
						$data['RolesRoomsUser'],
						'{n}.roles_room_id',
						$rolesRoomId
					);

					$result = $controller->RolesRoomsUser->saveRolesRoomsUsersForRooms($data);
				} else {
					$result = $controller->RolesRoomsUser->deleteRolesRoomsUsersForRooms($data);
				}
			}
		}

		if (! $controller->request->query) {
			$type = 'INNER';
		} else {
			$type = 'LEFT';
		}

		$controller->UserSearchComp->search(array(
			'fields' => self::$displaFields,
			'joins' => array(
				'RolesRoomsUser' => array(
					'type' => $type,
					'conditions' => array(
						'RolesRoomsUser.room_id' => $room['Room']['id'],
					)
				)
			),
			'order' => array('RoomRole.level' => 'desc'),
			'limit' => $this->limit
		));

		$controller->request->data = $room;
		$controller->request->data['RolesRoomsUser'] = Hash::combine(
			$controller->viewVars['users'], '{n}.User.id', '{n}.RolesRoomsUser'
		);

		return $result;
	}

}
