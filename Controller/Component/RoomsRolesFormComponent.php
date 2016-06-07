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
 * 会員一覧の表示する項目
 *
 * @var const
 */
	public static $displaFields = array(
		//'room_role_key',
		'handlename',
		'name',
		'role_key',
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
 * @return void
 */
	public function actionRoomsRolesUser(Controller $controller, $outputMessage) {
		//ルームデータチェック
		$room = $controller->viewVars['room'];

		//登録処理
		if ($controller->request->is('put')) {
			foreach ($controller->request->data['User']['id'] as $userId => $checked) {
				if (! $checked) {
					unset($controller->request->data['RolesRoomsUser'][$userId]);
					continue;
				}
			}

			if (! $controller->request->data['RolesRoomsUser']) {
				//未選択の場合、
				$result = null;
			} elseif ($controller->request->data['Role']['key'] !== 'delete') {
				$rolesRooms = $controller->Room->getRolesRooms(array(
					'Room.id' => $room['Room']['id'],
					'RolesRoom.role_key' => $controller->request->data['Role']['key']
				));
				$rolesRoomId = Hash::get($rolesRooms, '0.RolesRoom.id');
				$controller->request->data['RolesRoomsUser'] = Hash::insert(
					$controller->request->data['RolesRoomsUser'],
					'{n}.roles_room_id',
					$rolesRoomId
				);
				$result = $controller->RolesRoomsUser->saveRolesRoomsUsersForRooms($controller->request->data);
			} else {
				$result = $controller->RolesRoomsUser->deleteRolesRoomsUsersForRooms($controller->request->data);
			}

			//登録処理
			if ($result === true && $outputMessage) {
				//正常の場合
				$controller->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
			} elseif ($result === false) {
				$controller->NetCommons->handleValidationError($controller->RolesRoomsUser->validationErrors);
			}
		}

		if (! $controller->request->query) {
			$type = 'INNER';
		} else {
			$type = 'LEFT';
		}

		$controller->UserSearch->search(
			array(),
			array('RolesRoomsUser' => array(
				'type' => $type,
				'conditions' => array(
					'RolesRoomsUser.room_id' => $room['Room']['id'],
				)
			)),
			array('RoomRole.level' => 'desc'),
			self::DEFAULT_LIMIT
		);

		$fields = array_combine(self::$displaFields, self::$displaFields);
		$controller->set('displayFields', $controller->User->cleanSearchFields($fields));

		$controller->request->data = $room;
		$controller->request->data['RolesRoomsUser'] = Hash::combine(
			$controller->viewVars['users'], '{n}.User.id', '{n}.RolesRoomsUser'
		);
	}

}
