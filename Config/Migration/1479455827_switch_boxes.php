<?php
/**
 * ボックスの切り替え機能追加のためのデータ追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * ボックスの切り替え機能追加のためのデータ追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class SwitchBoxes extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'switch_boxes';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * パブリックスペースのrolesRoomId
 *
 * @var array $migration
 */
	protected $_publicRolesRooms = array();

/**
 * Records keyed by model name.
 *
 * @var array $records
 */
	public $records = array(
		'RolesRoom' => array(
			//サイト全体
			array('room_id' => '{WholeSiteRoomId}', 'role_key' => 'room_administrator'),
			array('room_id' => '{WholeSiteRoomId}', 'role_key' => 'chief_editor'),
			array('room_id' => '{WholeSiteRoomId}', 'role_key' => 'editor'),
			array('room_id' => '{WholeSiteRoomId}', 'role_key' => 'general_user'),
			array('room_id' => '{WholeSiteRoomId}', 'role_key' => 'visitor'),
			//プライベートスペース
			array('room_id' => '2', 'role_key' => 'chief_editor'),
			array('room_id' => '2', 'role_key' => 'editor'),
			array('room_id' => '2', 'role_key' => 'general_user'),
			array('room_id' => '2', 'role_key' => 'visitor'),
		),
		'Room' => array(
			//サイト全体
			array(
				'id' => '4',
				'space_id' => '1',
				'page_id_top' => null,
				'root_id' => null,
				'parent_id' => null,
				'active' => '1',
				'default_role_key' => 'visitor',
				'need_approval' => '1',
				'default_participation' => '1',
				'page_layout_permitted' => '0',
				'theme' => null,
			),
		),
		'RoomsLanguage' => array(
			//サイト全体
			//--日本語
			array('language_id' => '2', 'room_id' => '{WholeSiteRoomId}', 'name' => 'サイト全体'),
			//--英語
			array('language_id' => '1', 'room_id' => '{WholeSiteRoomId}', 'name' => 'Whole site'),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
	public function before($direction) {
		$this->loadModels([
			'Room' => 'Rooms.Room',
			'RolesRoom' => 'Rooms.RolesRoom',
			'Space' => 'Rooms.Space',
			'RoomsLanguage' => 'Rooms.RoomsLanguage',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
		]);

		if ($direction === 'down') {
			$this->wholeSiteRoom = $this->Room->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => null
				),
			));
		}

		//$this->Room->begin();

		//Roomテーブルの登録
		if (! $this->__saveRoom($direction)) {
			return false;
		}
		//RoomsLanguageテーブルの登録
		if (! $this->__saveRoomsLanguage($direction)) {
			return false;
		}
		//RolesRoomテーブルの登録
		if (! $this->__saveRolesRoom($direction)) {
			return false;
		}
		//Spaceテーブルの登録
		if (! $this->__saveSpace($direction)) {
			return false;
		}
		//PluginRoomの登録
		if (! $this->__savePluginRooms($direction)) {
			return false;
		}

		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 * @throws InternalErrorException
 */
	public function after($direction) {
		//RolesRoomテーブルの関連テーブルの登録
		if ($direction === 'down') {
			if (! $this->__deleteRolesRoomAssociations()) {
				return false;
			}
		} else {
			if (! $this->__saveRolesRoomAssociations()) {
				return false;
			}
		}

		if ($this->Room->useDbConfig !== 'test' &&
				Configure::read('NetCommons.installed') &&
				! $this->Room->recover()) {
			return false;
		}

		//$this->Room->commit();
		return true;
	}

/**
 * サイト全体のルームIDを$this->recordsにセットする
 *
 * @param int $roomId ルームID
 * @return void
 */
	private function __setWholeSiteRoomId($roomId) {
		foreach ($this->records as $modelName => $records) {
			foreach ($records as $i => $record) {
				if ($record['room_id'] === '{WholeSiteRoomId}') {
					$record['room_id'] = $roomId;
				}
				$this->records[$modelName][$i] = $record;
			}
		}
	}

/**
 * サイト全体のルームIDを$this->recordsにセットする
 *
 * @return void
 */
	private function __setWholeSiteRolesRoomId() {
		$this->wholeSiteRolesRooms = $this->RolesRoom->find('list', array(
			'recursive' => -1,
			'fields' => array('role_key', 'id'),
			'conditions' => array('room_id' => $this->wholeSiteRoom['Room']['id']),
		));

		$this->_publicRolesRooms = $this->RolesRoom->find('list', array(
			'recursive' => -1,
			'fields' => array('role_key', 'id'),
			'conditions' => array('room_id' => '1'),
		));
	}

/**
 * Roomテーブルの更新
 *
 * @param string $direction Migration処理 (up or down)
 * @return bool
 */
	private function __saveRoom($direction) {
		if ($direction === 'down') {
			//サイト全体のルーム削除
			if (! $this->Room->deleteAll(array('Room.id' => $this->wholeSiteRoom['Room']['id']), false)) {
				return false;
			}
			unset($this->records['Room']);

			//スペースのデータの更新
			$update = array(
				'Room.root_id' => null,
				'Room.parent_id' => null,
			);
			$conditions = array(
				'Room.root_id' => $this->wholeSiteRoom['Room']['id'],
			);
			if (! $this->Room->updateAll($update, $conditions)) {
				return false;
			}

			//関連データの削除
			$models = array_keys($this->records);
			foreach ($models as $modelName) {
				if ($this->$modelName->hasField('room_id')) {
					$conditions = array('room_id' => $this->wholeSiteRoom['Room']['id']);
					if (! $this->$modelName->deleteAll($conditions, false)) {
						return false;
					}
				}
			}

		} else {
			//サイト全体のルーム登録
			$this->wholeSiteRoom = $this->Room->save(
				$this->records['Room'][0], array('validate' => false, 'callbacks' => false)
			);
			if (! $this->wholeSiteRoom) {
				return false;
			}
			unset($this->records['Room']);
			$this->__setWholeSiteRoomId($this->wholeSiteRoom['Room']['id']);

			//スペースのデータの更新
			$update = array(
				'Room.root_id' => $this->wholeSiteRoom['Room']['id'],
				'Room.parent_id' => $this->wholeSiteRoom['Room']['id'],
			);
			$conditions = array(
				'Room.root_id' => null,
				'Room.id !=' => $this->wholeSiteRoom['Room']['id'],
			);
			$this->Room->unbindModel(['belongsTo' => ['ParentRoom']]);
			if (! $this->Room->updateAll($update, $conditions)) {
				return false;
			}
		}

		return true;
	}

/**
 * RoomsLanguageテーブルの登録
 *
 * @param string $direction Migration処理 (up or down)
 * @return bool
 */
	private function __saveRoomsLanguage($direction) {
		if ($direction === 'down') {
			$conditions = array(
				'RoomsLanguage.room_id' => $this->wholeSiteRoom['Room']['id']
			);
			if (! $this->RoomsLanguage->deleteAll($conditions, false)) {
				return false;
			}
		} else {
			if (! $this->RoomsLanguage->saveMany($this->records['RoomsLanguage'])) {
				return false;
			}
		}

		return true;
	}

/**
 * RolesRoomテーブルの登録
 *
 * @param string $direction Migration処理 (up or down)
 * @return bool
 */
	private function __saveRolesRoom($direction) {
		if ($direction === 'down') {
			$conditions = array(
				'RolesRoom.room_id' => $this->wholeSiteRoom['Room']['id']
			);
			if (! $this->RolesRoom->deleteAll($conditions, false)) {
				return false;
			}
			foreach ($this->records['RolesRoom'] as $record) {
				if ($record['room_id'] === '{WholeSiteRoomId}') {
					continue;
				}
				$conditions = array(
					'RolesRoom.room_id' => $record['room_id'],
					'RolesRoom.role_key' => $record['role_key'],
				);
				if (! $this->RolesRoom->deleteAll($conditions, false)) {
					return false;
				}
			}
		} else {
			if (! $this->RolesRoom->saveMany($this->records['RolesRoom'])) {
				return false;
			}
		}
		$this->__setWholeSiteRolesRoomId();

		return true;
	}

/**
 * Spaceテーブルの登録
 *
 * @param string $direction Migration処理 (up or down)
 * @return bool
 */
	private function __saveSpace($direction) {
		if ($direction === 'down') {
			$update = array(
				'Space.room_id_root' => null
			);
			$conditions = array();
			if (! $this->Space->updateAll($update, $conditions)) {
				return false;
			}
		} else {
			$updates = array(
				'1' => '1', //サイト全体をパブリックスペースのルームとして処理する。
				'2' => '1',
				'3' => '2',
				'4' => '3',
			);
			foreach ($updates as $spaceId => $roomId) {
				$update = array(
					'Space.room_id_root' => $roomId
				);
				$conditions = array(
					'Space.id' => $spaceId,
				);
				if (! $this->Space->updateAll($update, $conditions)) {
					return false;
				}
			}
		}

		return true;
	}

/**
 * RolesRoomの関連テーブルの登録
 *
 * @return bool
 */
	private function __saveRolesRoomAssociations() {
		$models = array('RoomRolePermission');

		$wholeSiteRoomId = $this->wholeSiteRoom['Room']['id'];
		foreach ($models as $modelName) {
			$model = $this->generateModel($modelName);

			$tableName = $model->tablePrefix . $model->table;
			$schema = $model->schema();
			unset($schema['id'], $schema['roles_room_id']);
			if ($model->hasField('room_id')) {
				unset($schema['room_id']);
			}

			foreach ($this->_publicRolesRooms as $roleKey => $rolesRoomId) {
				$commonSchema = implode(', ', array_keys($schema));
				if ($model->hasField('room_id')) {
					$insertSchema = 'roles_room_id, room_id, ' . $commonSchema;
					$selectSchema = $this->wholeSiteRolesRooms[$roleKey] . ', ' .
									$wholeSiteRoomId . ', ' . $commonSchema;
				} else {
					$insertSchema = 'roles_room_id, ' . $commonSchema;
					$selectSchema = $this->wholeSiteRolesRooms[$roleKey] . ', ' . $commonSchema;
				}
				$sql = 'INSERT INTO ' . $tableName . '(' . $insertSchema . ') ' .
						'SELECT ' . $selectSchema . ' FROM ' . $tableName .
						' WHERE roles_room_id = ' . $rolesRoomId;
				$model->query($sql);
			}
		}

		$spaceRolesRoomIds = $this->RolesRoomsUser->getSpaceRolesRoomsUsers();

		$rolesRoomsUsers = $this->RolesRoomsUser->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID, 'Room')
			),
		));

		foreach ($rolesRoomsUsers as $data) {
			if (! $this->RolesRoomsUser->saveSpaceRoomForRooms($data['RolesRoomsUser'], $spaceRolesRoomIds)) {
				return false;
			}
		}

		return true;
	}

/**
 * RolesRoomの関連テーブルの削除
 *
 * @return bool
 */
	private function __deleteRolesRoomAssociations() {
		$models = array('RolesRoomsUser', 'RoomRolePermission');

		$rolesRoomIds = array_values($this->wholeSiteRolesRooms);
		foreach ($models as $modelName) {
			$model = $this->generateModel($modelName);

			if (! $model->deleteAll(array('roles_room_id' => $rolesRoomIds), false)) {
				return false;
			}
		}

		return true;
	}

/**
 * PluginRoomデータの登録
 *
 * @param string $direction Migration処理 (up or down)
 * @return bool
 */
	private function __savePluginRooms($direction) {
		$PluginsRoom = $this->generateModel('PluginsRoom');
		$tableName = $PluginsRoom->tablePrefix . $PluginsRoom->table;

		if ($direction === 'down') {
			//Migration downで元に戻す
			$conditions = array(
				'PluginsRoom.room_id' => $this->wholeSiteRoom['Room']['id']
			);
			if (! $PluginsRoom->deleteAll($conditions, false)) {
				return false;
			}
		} else {
			$count = $PluginsRoom->find('count', array(
				'recursive' => -1,
				'conditions' => array('room_id' => '1'),
			));
			if ($count > 0) {
				return true;
			}

			$schema = $PluginsRoom->schema();
			unset($schema['id']);
			unset($schema['room_id']);
			unset($schema['created_user'], $schema['modified_user']);
			unset($schema['created'], $schema['modified']);
			$schemaColumns = implode(', ', array_keys($schema));

			$now = '\'' . gmdate('Y-m-d H:i:s') . '\'';

			//サイト全体
			$roomIdRoot = $this->wholeSiteRoom['Room']['id'];
			$sql = 'INSERT INTO ' . $tableName . '(room_id, ' . $schemaColumns . ', created, modified)' .
					' SELECT ' . $roomIdRoot . ', ' . $schemaColumns . ', ' . $now . ', ' . $now .
					' FROM ' . $tableName .
					' WHERE room_id = 1';
			$PluginsRoom->query($sql);
			//$result = $PluginsRoom->getAffectedRows() > 0;
			//if (! $result) {
			//	return false;
			//}
		}

		return true;
	}

}
