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
	const DEFAULT_LIMIT = 20;

/**
 * 取得件数
 *
 * @var string
 */
	public $limit = self::DEFAULT_LIMIT;

/**
 * 会員一覧で取得する項目
 *
 * @var const
 */
	public static $findFields = array(
		'handlename',
		'name',
		'role_key',
		'room_role_key',
	);

/**
 * 会員一覧の表示する項目
 *
 * @var const
 */
	public static $displaFields = array(
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

			$controller->request->data = Hash::merge($results, $controller->request->data);
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

		//Ajaxの場合、一時セットとする(次へもしくは決定で更新する)
		if ($controller->request->is('ajax') && $controller->params['action'] === 'role_room_user') {
			return $this->__setRoomRoleUser($controller);
		}

		//登録処理
		$result = null;
		if ($controller->request->is('put')) {
			$data = $this->__getRequestData($controller);
			if (! $data['RolesRoomsUser']) {
				//未選択の場合、
				$result = true;
			} else {
				$result = $controller->RolesRoomsUser->saveRolesRoomsUsersForRooms(array(
					'RolesRoomsUser' => $data['RolesRoomsUser']
				));
				$controller->Session->delete('RoomsRolesUsers');
			}
		}

		$controller->UserSearchComp->search(array(
			'fields' => self::$findFields,
			'joins' => array(
				'RolesRoomsUser' => array(
					'conditions' => array(
						'RolesRoomsUser.room_id' => $room['Room']['id'],
					)
				)
			),
			'defaultOrder' => array('room_role_level' => 'desc'),
			'limit' => $this->limit,
			'displayFields' => self::$displaFields,
			'extra' => array(
				'selectedUsers' => $controller->Session->read('RoomsRolesUsers'),
				'plugin' => $controller->params['plugin'],
				'search' => (bool)$controller->request->query
			)
		));

		$controller->request->data = $room;
		$controller->request->data['RolesRoomsUser'] = Hash::combine(
			$controller->viewVars['users'], '{n}.User.id', '{n}.RolesRoomsUser'
		);

		return $result;
	}

/**
 * RoomsRolesUserの登録のアクション(AJAX)
 *
 * @param Controller $controller コントローラ
 * @return null|bool
 */
	private function __setRoomRoleUser(Controller $controller) {
		//ルームデータチェック
		$room = $controller->viewVars['room'];
		$userId = $controller->request->data['RolesRoomsUser']['user_id'];

		$rolesRoomsUserId = $controller->RolesRoomsUser->find('first', array(
			'recursive' => -1,
			'fields' => array('id'),
			'conditions' => array(
				'room_id' => $room['Room']['id'],
				'user_id' => $userId
			)
		));
		$rolesRoomsUser = array(
			'id' => Hash::get($rolesRoomsUserId, 'RolesRoomsUser.id'),
			'room_id' => $room['Room']['id'],
			'user_id' => $userId,
			'role_key' => $controller->request->data['RolesRoomsUser']['role_key'],
		);

		if ($controller->request->data['RolesRoomsUser']['role_key']) {
			$rolesRooms = $controller->Room->getRolesRoomsInDraft(array(
				'Room.id' => $room['Room']['id'],
				'RolesRoom.role_key' => $controller->request->data['RolesRoomsUser']['role_key'],
				//'Room.in_draft' => true
			));

			$rolesRoomsUser['roles_room_id'] = Hash::get($rolesRooms, '0.RolesRoom.id');
			$controller->RolesRoomsUser->set($rolesRoomsUser);
			if (! $controller->RolesRoomsUser->validates()) {
				return false;
			}
		} elseif ($rolesRoomsUserId) {
			$rolesRoomsUser['delete'] = true;
		} else {
			$controller->Session->delete('RoomsRolesUsers.' . $userId);
			return true;
		}

		$controller->Session->write('RoomsRolesUsers.' . $userId, $rolesRoomsUser);

		return true;
	}

/**
 * RoomsRolesUserの登録時のリクエストデータの取得
 *
 * @param Controller $controller コントローラ
 * @return array
 */
	private function __getRequestData(Controller $controller) {
		//ルームデータチェック
		$room = $controller->viewVars['room'];

		$data = $controller->request->data;
		foreach ($data['User']['id'] as $userId => $checked) {
			if (! $checked) {
				unset($data['RolesRoomsUser'][$userId]);
				continue;
			}
			if (! $data['RolesRoomsUser'][$userId]['id']) {
				$rolesRoomsUser = $controller->RolesRoomsUser->find('first', array(
					'recursive' => -1,
					'fields' => array('id'),
					'conditions' => array(
						'room_id' => $room['Room']['id'],
						'user_id' => $userId
					)
				));
				$data['RolesRoomsUser'][$userId]['id'] = Hash::get($rolesRoomsUser, 'RolesRoomsUser.id');
			}
		}

		if ($data['Role']['key'] !== 'delete') {
			$rolesRooms = $controller->Room->getRolesRoomsInDraft(array(
				'Room.id' => $room['Room']['id'],
				'RolesRoom.role_key' => $data['Role']['key']
			));
			$data['RolesRoomsUser'] = Hash::insert(
				$data['RolesRoomsUser'], '{n}.roles_room_id', Hash::get($rolesRooms, '0.RolesRoom.id')
			);
			$data['RolesRoomsUser'] = Hash::remove(
				$data['RolesRoomsUser'], '{n}.delete'
			);
		} elseif ($data['Role']['key'] === 'delete') {
			$data['RolesRoomsUser'] = Hash::insert(
				$data['RolesRoomsUser'], '{n}.delete', true
			);
		}

		$tmpData = $controller->Session->read('RoomsRolesUsers');
		if (! $tmpData) {
			$tmpData = array();
		}

		return Hash::merge(array('RolesRoomsUser' => $tmpData), $data);
	}

}
