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
App::uses('Space', 'Rooms.Model');

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
			$roomRolePerms = Hash::get($controller->request->data, 'RoomRolePermission', array());
			foreach ($roomRolePerms as $permission => $roles) {
				foreach ($roles as $key => $role) {
					if (isset($role['value'])) {
						$role['value'] = (bool)$role['value'];
						$controller->request->data['RoomRolePermission'][$permission][$key] = $role;
					}
				}
			}

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

		//スペースModel
		$activeSpaceId = $controller->viewVars['activeSpaceId'];
		$model = Inflector::camelize(
			$controller->viewVars['spaces'][$activeSpaceId]['Space']['plugin_key']
		);
		$controller->$model = ClassRegistry::init($model . '.' . $model);
		if ($room['Room']['id'] === Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID)) {
			$controller->set('participationFixed', true);
		} else {
			$controller->set('participationFixed', $controller->$model->participationFixed);
		}

		//role_room_userアクションは、一時的にセッションにセットとする(次へもしくは決定で更新する)
		if ($controller->params['action'] === 'role_room_user') {
			return $this->__setRoomRoleUser($controller);
		}

		//登録処理
		$result = null;
		if ($controller->request->is('put')) {
			if (array_key_exists('save', $controller->request->data)) {
				$data = $controller->Session->read('RoomsRolesUsers');
				if (! $data) {
					//未選択の場合、
					$result = true;
				} else {
					$result = $controller->RolesRoomsUser->saveRolesRoomsUsersForRooms(array(
						'RolesRoomsUser' => $data
					));
					$controller->Session->delete('RoomsRolesUsers');
					$controller->Session->delete('paginateConditionsByRoomRoleKey');
				}
			} else {
				$data = $this->__getRequestData($controller);
				$controller->Session->write('RoomsRolesUsers', $data['RolesRoomsUser']);
			}
		}

		$seached = (bool)($controller->request->query || $room['Room']['default_participation']);

		// 'joins' => 'RolesRoomsUser'でユーザーの重複を絞るが、WHERE句のroom_id条件と意味が違ってくるため、別途条件指定。
		// 一度取得してIN句だと、全員参加ルームの場合、データ量が多いのでサブクエリを使用する。
		$queryRoomIdValue = $readableFieldValue = null;
		$subQuery = $this->__getSubQuery($controller);
		if ($subQuery) {
			// @see https://github.com/NetCommons3/Users/blob/3.1.0/Controller/Component/UserSearchCompComponent.php#L98
			$queryRoomIdValue = Hash::get($controller->request->query, ['room_id']);
			$controller->request->query['room_id'] = $subQuery;

			/* @var $UserSearch UserSearch */
			$UserSearch = ClassRegistry::init('Users.UserSearch');
			// @see https://github.com/NetCommons3/Users/blob/3.1.0/Model/UserSearch.php#L332
			$readableFieldValue = $UserSearch->readableFields['room_id'];
			$UserSearch->readableFields['room_id']['field'] = 0;

			// 同じインスタンスを使用するようClassRegistry::addObjectしとく。
			// @see https://github.com/NetCommons3/Users/blob/3.1.0/Controller/Component/UserSearchCompComponent.php#L88
			ClassRegistry::addObject('UserSearch', $UserSearch);
		}

		$controller->UserSearchComp->search(array(
			'fields' => self::$findFields,
			'joins' => array(
				'RolesRoomsUser' => array(
					'conditions' => array(
						'RolesRoomsUser.room_id' => $room['Room']['id'],
					)
				),
			),
			'limit' => $this->limit,
			'displayFields' => self::$displaFields,
			'extra' => array(
				'selectedUsers' => $controller->Session->read('RoomsRolesUsers'),
				'plugin' => $seached ? $controller->params['plugin'] : '',
				'search' => $seached
			)
		));

		// PHPMD.CyclomaticComplexity に引っかかるのでメソッド化
		$this->__restoreValueForSubQuery($controller, $subQuery, $queryRoomIdValue, $readableFieldValue);

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

		//パブリックスペースの時不参加にできない
		if ($controller->viewVars['participationFixed'] &&
				! $controller->request->data['RolesRoomsUser']['role_key']) {
			$controller->throwBadRequest();
			return false;
		}

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

		//パブリックスペースの時不参加にできない
		if ($controller->viewVars['participationFixed'] &&
				$controller->request->data['Role']['key'] === 'delete') {
			$controller->throwBadRequest();
			return false;
		}

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
			$data['RolesRoomsUser'] = Hash::insert(
				$data['RolesRoomsUser'], '{n}.role_key', $data['Role']['key']
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

/**
 * room_id条件のサブクエリ―取得
 *
 * @param Controller $controller コントローラ
 * @return array
 */
	private function __getSubQuery($controller) {
		/* @var $UserSearch UserSearch */
		$UserSearch = ClassRegistry::init('Users.UserSearch');
		// UserSearch::readableFieldsに設定されていないと条件は無視されるっぽい。
		// @see https://github.com/NetCommons3/Users/blob/3.1.0/Model/UserSearch.php#L308
		if (!isset($UserSearch->readableFields['room_id'])) {
			return null;
		}

		$room = $controller->viewVars['room'];
		/* @var $Room Room */
		$Room = ClassRegistry::init('Rooms.Room');
		$parentRoom = $Room->getParentNode($room['Room']['id'], 'id', -1);
		$db = $controller->RolesRoomsUser->getDataSource();
		$subQueries = [];

		if ($parentRoom['Room']['id'] != Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID)) {
			$query = [
				'fields' => ['ParentRolesRoomsUser.id'],
				'table' => $db->fullTableName($controller->RolesRoomsUser),
				'alias' => 'ParentRolesRoomsUser',
				'conditions' => [
					'ParentRolesRoomsUser.room_id' => $parentRoom['Room']['id'],
					'ParentRolesRoomsUser.user_id = User.id',
				],
			];
			$parentRoomSubQuery = $db->buildStatement($query, $controller->RolesRoomsUser);
			$subQueries[] = 'EXISTS (' . $parentRoomSubQuery . ') ';
		}

		$queryRoomIdValue = Hash::get($controller->request->query, ['room_id']);
		if ($queryRoomIdValue) {
			$query = [
				'fields' => ['ConditionRolesRoomsUser.id'],
				'table' => $db->fullTableName($controller->RolesRoomsUser),
				'alias' => 'ConditionRolesRoomsUser',
				'conditions' => [
					'ConditionRolesRoomsUser.room_id' => $queryRoomIdValue,
					'ConditionRolesRoomsUser.user_id = User.id',
				],
			];
			$conditionSubQuery = $db->buildStatement($query, $controller->RolesRoomsUser);
			$subQueries[] = 'EXISTS (' . $conditionSubQuery . ') ';
		}

		if ($subQueries) {
			$subQuery = implode(' AND ', $subQueries);
			return $db->expression($subQuery);
		}

		return null;
	}

/**
 * サブクエリ―設定時に書き換えた値を戻す
 *
 * @param Controller $controller コントローラ
 * @param mix $subQuery Sub query
 * @param string $roomId Room id of Requestry
 * @param string $field UserSearch::readableFieldValue
 * @return void
 */
	private function __restoreValueForSubQuery($controller, $subQuery, $roomId, $field) {
		if ($subQuery) {
			$controller->request->query['room_id'] = $roomId;
			/* @var $UserSearch UserSearch */
			$UserSearch = ClassRegistry::init('Users.UserSearch');
			$UserSearch->readableFields['room_id'] = $field;
			ClassRegistry::removeObject('UserSearch');
		}
	}
}
