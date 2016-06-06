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
		]);

		$conditions = Hash::merge(array(
				'Room.page_id_top NOT' => null,
			), $conditions);

		$query['fields'] = Hash::get($query, 'fields', array(
			$this->alias . '.*',
			$this->RolesRoom->alias . '.*',
			$this->Room->alias . '.*',
		));

		$rolesRoomsUsers = $this->find('all', Hash::merge(array(
			'recursive' => -1,
			'joins' => array(
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
			),
			'conditions' => $conditions,
		), $query));

		return $rolesRoomsUsers;
	}

/**
 * RolesRoomsUserの登録処理
 *
 * @param array $data リクエストデータ
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveRolesRoomsUser($data) {
		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data['RolesRoomsUser']);
		if (! $this->validates()) {
			return false;
		}

		try {
			//Roomデータの登録
			$rolesRoomsUser = $this->save($data['RolesRoomsUser'], false, false);
			if (! $rolesRoomsUser) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return $rolesRoomsUser;
	}

/**
 * RolesRoomsUserの一括登録(ルーム管理用)
 *
 * ### $dataのサンプル１（一括で変更）
 * ```
 * 	array (
 * 		'Room' => array (
 * 			'id' => '11',
 * 		),
 * 		'Role' => array (
 * 			'key' => 'visitor',
 * 		),
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
 * 			),
 * 		),
 * 		'User' => array (
 * 			'id' => array (
 * 				1 => '0',
 * 				2 => '0',
 * 				・・・
 * 				13 => '1',
 * 				14 => '1',
 * 				・・・
 * 			),
 * 		),
 * 		'RolesRoom' => array (
 * 			1 => array (
 * 				'role_key' => 'room_administrator',
 * 			),
 * 			2 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			・・・
 * 			13 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			14 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			・・・
 * 		),
 * 	)
 * ```
 *
 * ### $dataのサンプル２（個別で変更）
 * ```
 * 	array (
 * 		'RolesRoom' => array (
 * 			1 => array (
 * 				'role_key' => 'room_administrator',
 * 			),
 * 			2 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			・・・
 * 			16 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			17 => array (
 * 				'role_key' => 'visitor',
 * 			),
 * 		),
 * 		'RolesRoomsUser' => array (
 * 			17 => array (
 * 				'id' => '71',
 * 				'user_id' => '17',
 * 				'room_id' => '11',
 * 				'roles_room_id' => '35',
 * 			),
 * 		),
 * 		'User' => array (
 * 			'id' => array (
 * 				1 => '0',
 * 				2 => '0',
 * 				・・・
 * 				16 => '0',
 * 				17 => '1',
 * 			),
 * 		),
 * 		'Room' => array (
 * 			'id' => '11',
 * 		),
 * 		'Role' => array (
 * 			'key' => 'visitor',
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

		//バリデーション
		if (! $this->validateMany($data['RolesRoomsUser'])) {
			return false;
		}

		try {
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
 * RolesRoomsUserの削除処理
 *
 * @param array $data リクエストデータ
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function deleteRolesRoomsUser($data) {
		//トランザクションBegin
		$this->begin();

		try {
			//RolesRoomsUserデータの削除
			if (! $this->delete($data['RolesRoomsUser']['id'], false)) {
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
 * RolesRoomsUserの一括削除処理(ルーム管理用)
 *
 * ### $dataサンプル１（一括削除）
 * ```
 * 	array (
 * 		'Room' => array (
 * 			'id' => '11',
 * 		),
 * 		'Role' => array (
 * 			'key' => 'delete',
 * 		),
 * 		'RolesRoomsUser' => array (
 * 			13 => array (
 * 				'id' => '55',
 * 				'user_id' => '13',
 * 				'room_id' => '11',
 * 			),
 * 			14 => array (
 * 				'id' => '59',
 * 				'user_id' => '14',
 * 				'room_id' => '11',
 * 			),
 * 		),
 * 		'User' => array (
 * 			'id' => array (
 * 				1 => '0',
 * 				2 => '0',
 * 				・・・
 * 				13 => '1',
 * 				14 => '1',
 * 			),
 * 		),
 * 		'RolesRoom' => array (
 * 			1 => array (
 * 				'role_key' => 'room_administrator',
 * 			),
 * 			2 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			・・・
 * 			13 => array (
 * 				'role_key' => 'visitor',
 * 			),
 * 			14 => array (
 * 				'role_key' => 'visitor',
 * 			),
 * 		),
 * 	)
 * ```
 *
 * ### $dataサンプル２（個別で削除）
 * ```
 * 	array (
 * 		'RolesRoom' => array (
 * 			1 => array (
 * 				'role_key' => 'room_administrator',
 * 			),
 * 			2 => array (
 * 				'role_key' => 'general_user',
 * 			),
 * 			・・・
 * 			5 => array (
 * 				'role_key' => '',
 * 			),
 * 		),
 * 		'RolesRoomsUser' => array (
 * 			5 => array (
 * 				'id' => '38',
 * 				'user_id' => '5',
 * 				'room_id' => '11',
 * 			),
 * 		),
 * 		'User' => array (
 * 			'id' => array (
 * 				1 => '0',
 * 				2 => '0',
 * 				・・・
 * 				5 => '1',
 * 			),
 * 		),
 * 		'Room' => array (
 * 			'id' => '11',
 * 		),
 * 		'Role' => array (
 * 			'key' => 'delete',
 * 		),
 * 	)
 * ```
 *
 * @param array $data リクエストデータ
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function deleteRolesRoomsUsersForRooms($data) {
		//トランザクションBegin
		$this->begin();

		try {
			//RolesRoomsUserデータの削除
			$ids = Hash::extract($data['RolesRoomsUser'], '{n}.id');
			if (! $this->deleteAll(array($this->alias . '.id' => $ids), false)) {
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

}
