<?php
/**
 * RolesRoomsUser Model
 *
 * @property RolesRoom $RolesRoom
 * @property User $User
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppModel', 'Rooms.Model');
App::uses('Room', 'Rooms.Model');
App::uses('Space', 'Rooms.Model');

/**
 * RolesRoomsUser Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 */
class RolesRoomsUser extends RoomsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'RolesRoom' => array(
			'className' => 'Rooms.RolesRoom',
			'foreignKey' => 'roles_room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'roles_room_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
			'user_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
			'room_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
			'access_count' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => false,
				),
			),
			'last_accessed' => array(
				'datetime' => array(
					'rule' => array('datetime'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => true,
					'required' => false,
				),
			),
			'previous_accessed' => array(
				'datetime' => array(
					'rule' => array('datetime'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => true,
					'required' => false,
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * Return roles_rooms_users
 *
 * @param array $conditions Conditions by Model::find
 * @param array $query Condition以外のクエリ
 * @return array
 */
	public function getRolesRoomsUsers($conditions = array(), $query = array()) {
		$this->loadModels([
			'Room' => 'Rooms.Room',
			'RoomRole' => 'Rooms.RoomRole',
			'RolesRoom' => 'Rooms.RolesRoom',
		]);

		if (! array_key_exists('Room.id', $conditions)) {
			$conditions = Hash::merge(array('Room.page_id_top NOT' => null), $conditions);
		}

		$query['fields'] = Hash::get($query, 'fields', array(
			$this->alias . '.*',
			$this->RolesRoom->alias . '.*',
			$this->Room->alias . '.*',
		));

		$type = Hash::get($query, 'type', 'all');
		$query = Hash::remove($query, 'type');

		//呼ばれる条件に応じて、結合テーブルを切り分ける
		if ($type == 'count') {
			$joins = array(
				array(
					'table' => $this->Room->table,
					'alias' => $this->Room->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->alias . '.room_id' . ' = ' . $this->Room->alias . ' .id',
					),
				),
				array(
					'table' => $this->User->table,
					'alias' => $this->User->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->alias . '.user_id' . ' = ' . $this->User->alias . ' .id',
					),
				),
			);
		} else {
			$joins = array(
				array(
					'table' => $this->RolesRoom->table,
					'alias' => $this->RolesRoom->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->alias . '.roles_room_id' . ' = ' . $this->RolesRoom->alias . ' .id',
					),
				),
				array(
					'table' => $this->RoomRole->table,
					'alias' => $this->RoomRole->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->RoomRole->alias . '.role_key' . ' = ' . $this->RolesRoom->alias . ' .role_key',
					),
				),
				array(
					'table' => $this->Room->table,
					'alias' => $this->Room->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->RolesRoom->alias . '.room_id' . ' = ' . $this->Room->alias . ' .id',
					),
				),
				array(
					'table' => $this->User->table,
					'alias' => $this->User->alias,
					'type' => 'INNER',
					'conditions' => array(
						$this->alias . '.user_id' . ' = ' . $this->User->alias . ' .id',
					),
				),
			);
		}

		$rolesRoomsUsers = $this->find($type, Hash::merge(array(
			'recursive' => -1,
			'joins' => $joins,
			'conditions' => $conditions,
		), $query));

		return $rolesRoomsUsers;
	}

/**
 * RolesRoomsUserの登録処理(ユーザー管理用)
 *
 * ### $dataサンプル
 * ```
 * 	array (
 * 		'User' => array (
 * 			'id' => '14',
 * 		),
 * 		'RolesRoomsUser' => array (
 * 			1 => array (
 * 				'id' => '57',
 * 				'room_id' => '1',
 * 				'user_id' => '14',
 * 				'roles_room_id' => '5',
 * 			),
 * 			9 => array (
 * 				'id' => '58',
 * 				'room_id' => '9',
 * 				'user_id' => '14',
 * 				'roles_room_id' => '0',
 * 			),
 * 			10 => array (
 * 				'id' => '',
 * 				'room_id' => '10',
 * 				'user_id' => '14',
 * 				'roles_room_id' => '0',
 * 			),
 * 			11 => array (
 * 				'id' => '',
 * 				'room_id' => '11',
 * 				'user_id' => '14',
 * 				'roles_room_id' => '35',
 * 			),
 * 		),
 * 	)
 * ```
 *
 * @param array $data リクエストデータ
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveRolesRoomsUsersForUsers($data) {
		//トランザクションBegin
		$this->begin();

		$deleteIds = Hash::extract($data, 'RolesRoomsUser.{n}[roles_room_id=0][id>0].id');
		$data['RolesRoomsUser'] = Hash::remove($data['RolesRoomsUser'], '{n}[roles_room_id=0]');

		//バリデーション
		if (! $this->validateMany($data['RolesRoomsUser'])) {
			return false;
		}

		try {
			if ($deleteIds) {
				if (! $this->deleteAll(array($this->alias . '.id' => $deleteIds), false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}

			//RolesRoomsUserデータの登録
			if (! $this->saveMany($data['RolesRoomsUser'], ['validate' => false])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * RolesRoomsUserの一括登録(ルーム管理用)
 *
 * ### $dataのサンプル１（一括で変更）
 * ```
 * 	array (
 * 		'RolesRoomsUser' => array (
 * 			13 => array (
 * 				'id' => '55',
 * 				'user_id' => '13',
 * 				'room_id' => '11',
 * 				'roles_room_id' => '35',
 * 			),
 * 			14 => array (
 * 				'id' => '59',
 * 				'user_id' => '14',
 * 				'room_id' => '11',
 * 				'roles_room_id' => '35',
 *				'delete' => true
 * 			),
 * 		),
 * 	)
 * ```
 *
 * @param array $data リクエストデータ
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveRolesRoomsUsersForRooms($data) {
		//トランザクションBegin
		$this->begin();

		$spaceRolesRoomIds = $this->getSpaceRolesRoomsUsers();

		try {
			//RolesRoomsUserデータの登録
			foreach ($data['RolesRoomsUser'] as $rolesRoomsUser) {
				if (Hash::get($rolesRoomsUser, 'delete')) {
					if (!$rolesRoomsUser['id']) {
						continue;
					}

					// PHPMD.CyclomaticComplexity に引っかかるのでメソッド化
					if (!$this->__deleteUserForRoom($rolesRoomsUser)) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}

				} else {
					$this->create(false);
					$this->set($rolesRoomsUser);
					//バリデーション
					if (! $this->validates()) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
					if (! $this->save(null, false)) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
					if (! $this->saveSpaceRoomForRooms($rolesRoomsUser, $spaceRolesRoomIds)) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * パブリックスペースの場合、
 * サイト全体のスペース・コミュニティスペース・プライベートスペースのRolesRoomsUserに更新する
 *
 * @param array $rolesRoomsUser リクエストデータ
 * @param array $spaceRolesRoomIds spaceのRolesRoomIdのデータ
 * @param bool $isCreate 新規作成かどうか
 * @return bool
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function saveSpaceRoomForRooms($rolesRoomsUser, $spaceRolesRoomIds, $isCreate = false) {
		if ($rolesRoomsUser['room_id'] !== Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID, 'Room')) {
			return true;
		}

		$roleKey = array_search(
			$rolesRoomsUser['roles_room_id'],
			$spaceRolesRoomIds[$rolesRoomsUser['room_id']],
			true
		);
		if (! $roleKey) {
			//ロールキーが取得できなかったら、何もないこととして処理する。
			return true;
		}

		$roomIds = array(
			Space::getRoomIdRoot(Space::WHOLE_SITE_ID, 'Room'),
			Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID, 'Room'),
			Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID, 'Room')
		);
		foreach ($roomIds as $roomId) {
			$rolesRoomId = Hash::get(
				$spaceRolesRoomIds, $roomId . '.' . $roleKey
			);
			if (! $rolesRoomId) {
				continue;
			}
			if ($isCreate) {
				$this->create(false);
				$result = $this->save(array(
					'roles_room_id' => $rolesRoomId,
					'room_id' => $roomId,
					'user_id' => $rolesRoomsUser['user_id'],
				));
			} else {
				$update = array(
					$this->alias . '.roles_room_id' => $rolesRoomId,
				);
				$conditions = array(
					$this->alias . '.room_id' => $roomId,
					$this->alias . '.user_id' => $rolesRoomsUser['user_id'],
				);
				$count = $this->find('count', ['recursive' => -1, 'conditions' => $conditions]);
				if ($count > 0) {
					$result = $this->updateAll($update, $conditions);
				} else {
					$this->create(false);
					$result = $this->save(array(
						'roles_room_id' => $rolesRoomId,
						'room_id' => $roomId,
						'user_id' => $rolesRoomsUser['user_id'],
					));
				}
			}
			if (! $result) {
				return false;
			}
		}

		return true;
	}

/**
 * パブリックスペースの場合、
 * サイト全体のスペース・コミュニティスペース・プライベートスペースのRolesRoomsUserに更新する
 *
 * @return array
 */
	public function getSpaceRolesRoomsUsers() {
		$this->loadModels([
			'RoomRole' => 'Rooms.RoomRole',
			'RolesRoom' => 'Rooms.RolesRoom',
		]);

		$spaceRolesRoomIds = $this->RolesRoom->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => array(
					Space::getRoomIdRoot(Space::WHOLE_SITE_ID, 'Room'),
					Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID, 'Room'),
					Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID, 'Room'),
					Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID, 'Room')
				),
			),
		));

		$spaceRolesRoomIds = Hash::combine(
			$spaceRolesRoomIds,
			'{n}.RolesRoom.role_key',
			'{n}.RolesRoom.id',
			'{n}.RolesRoom.room_id'
		);

		return $spaceRolesRoomIds;
	}

/**
 * アクセス日時の登録処理
 *
 * @param int $roleRoomUserId ロール・ルーム・ユーザID
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveAccessed($roleRoomUserId) {
		//トランザクションBegin
		$this->begin();

		$db = $this->getDataSource();

		try {
			//登録処理
			$update = array(
				$this->alias . '.access_count' => 'access_count + 1',
				$this->alias . '.previous_accessed' => 'last_accessed',
				$this->alias . '.last_accessed' => $db->value(date('Y-m-d H:i:s'), 'string'),
			);
			$conditions = array($this->alias . '.id' => (int)$roleRoomUserId);
			if (! $this->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			//エラーになったとしても、throwにしない
			CakeLog::error($ex);
			$this->rollback();
		}

		$this->setSlaveDataSource();

		return true;
	}

/**
 * Delete RolesRoomsUser data
 *
 * @param array $rolesRoomsUser RolesRoomsUser data
 * @return bool
 */
	private function __deleteUserForRoom($rolesRoomsUser) {
		if (! $this->delete($rolesRoomsUser['id'])) {
			return false;
		}

		/* @var $Room Room */
		$Room = ClassRegistry::init('Rooms.Room');
		$query = [
			'fields' => [
				'Room.id',
			],
			'conditions' => [
				'Room.space_id' => Space::COMMUNITY_SPACE_ID,
				'Room.parent_id' => $rolesRoomsUser['room_id'],
			],
			'recursive' => -1,
			'callbacks' => false,
		];
		$subRoomIdList = $Room->find('list', $query);

		$conditions = [
			'RolesRoomsUser.room_id' => $subRoomIdList,
			'RolesRoomsUser.user_id' => $rolesRoomsUser['user_id'],
		];
		if (!$this->deleteAll($conditions, false)) {
			return false;
		}

		return true;
	}

}
