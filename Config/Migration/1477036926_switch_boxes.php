<?php
/**
 * ボックスの切り替え機能追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * ボックスの切り替え機能追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
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
			'create_table' => array(
				'rooms_tmps' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'space_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'page_id_top' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'root_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'in_draft' => array('type' => 'boolean', 'null' => false, 'default' => false, 'comment' => '作成中かどうか。1: 作成中、0: 確定'),
					'default_role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => '「ルーム内の役割」のデフォルト値'),
					'need_approval' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'default_participation' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'page_layout_permitted' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'theme' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'create_table' => array(
				'rooms_tmps' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'space_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'page_id_top' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'root_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'in_draft' => array('type' => 'boolean', 'null' => false, 'default' => false, 'comment' => '作成中かどうか。1: 作成中、0: 確定'),
					'default_role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => '「ルーム内の役割」のデフォルト値'),
					'need_approval' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'default_participation' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'page_layout_permitted' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'theme' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
	);

/**
 * Records keyed by model name.
 *
 * @var array $records
 */
	public $records = array(
		'RolesRoom' => array(
			//サイト全体スペース
			//このidはコピー元のID
			array('copy_id' => '1', 'room_id' => '1', 'role_key' => 'room_administrator'),
			array('copy_id' => '2', 'room_id' => '1', 'role_key' => 'chief_editor'),
			array('copy_id' => '3', 'room_id' => '1', 'role_key' => 'editor'),
			array('copy_id' => '4', 'room_id' => '1', 'role_key' => 'general_user'),
			array('copy_id' => '5', 'room_id' => '1', 'role_key' => 'visitor'),
		),
		'Room' => array(
			//サイト全体
			array(
				'id' => '1',
				'space_id' => '1',
				'page_id_top' => null,
				'parent_id' => null,
				'lft' => '1',
				//'rght' => '2',
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
			array('language_id' => '2', 'room_id' => '1', 'name' => 'サイト全体'),
			//--英語
			array('language_id' => '1', 'room_id' => '1', 'name' => 'Whole site'),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
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
		if ($direction === 'down') {
			$sequence = -1;
		} else {
			$sequence = 1;
		}

		$db = ConnectionManager::getDataSource('master');
		$tables = $db->listSources();

		$Room = $this->generateModel('Room');
		$models = array_map(function ($table) use ($Room) {
			$table = preg_replace('/' . preg_quote($Room->tablePrefix) . '/', '', $table);
			return Inflector::classify($table);
		}, $tables);

		$this->Room = $Room;

		$db->begin();
		try {
			if ($direction === 'down') {
				//RolesRoomテーブルの登録
				$this->__saveRolesRoom($direction, $models);

				//RoomsLanguageテーブルの登録
				$this->__saveRoomsLanguage($direction);

				//Roomテーブルの登録
				$this->__saveRoom($direction);

				//関連テーブルのroom_idをずらす
				$this->__updateAssociation($direction, $models);

			} else {
				//関連テーブルのroom_idをずらす
				$this->__updateAssociation($direction, $models);

				//Roomテーブルの登録
				$this->__saveRoom($direction);

				//RoomsLanguageテーブルの登録
				$this->__saveRoomsLanguage($direction);

				//RolesRoomテーブルの登録
				$this->__saveRolesRoom($direction, $models);
			}

			$SiteSetting = $this->generateModel('SiteSetting');
			$update = array(
				'value' => 'value + (' . $sequence . ')'
			);
			if (! $SiteSetting->updateAll($update, array('key' => 'App.default_start_room'))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$db->commit();
			$return = true;

		} catch (Exception $ex) {
			CakeLog::error($ex);
			$db->rollback();
			$return = false;
		}

		$sql = 'DROP TABLE IF EXISTS ' . $this->Room->tablePrefix . $this->Room->table . '_tmps';
		$this->Room->query($sql);

		return $return;
	}

/**
 * 関連テーブルのroom_idをずらす
 *
 * @param string $direction Migration処理 (up or down)
 * @param array $models 更新するモデルリスト
 * @return void
 * @throws InternalErrorException
 */
	private function __updateAssociation($direction, $models) {
		if ($direction === 'down') {
			$sequence = -1;
		} else {
			$sequence = 1;
		}

		foreach ($models as $modelName) {
			$model = $this->generateModel($modelName);
			if (! $model->hasField('room_id')) {
				continue;
			}
			$update = array(
				'room_id' => 'room_id + (' . $sequence . ')'
			);
			if (! $model->updateAll($update, array('room_id !=' => '0'))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
	}

/**
 * Roomテーブルの更新
 *
 * @param string $direction Migration処理 (up or down)
 * @return void
 * @throws InternalErrorException
 */
	private function __saveRoom($direction) {
		if ($direction === 'down') {
			$sequence = -1;
			if (! $this->Room->deleteAll(array('id' => '1'), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} else {
			$sequence = 1;
		}

		$tableName = $this->Room->tablePrefix . $this->Room->table;
		$schema = $this->Room->schema();
		unset($schema['id']);

		$schemaString = implode(', ', array_keys($schema));

		//Primary keyの更新ができないため、一度tmpテーブルに移して変更させる
		$sql = 'INSERT INTO ' . $tableName . '_tmps' . '(id, ' . $schemaString . ') ' .
				'SELECT id + (' . $sequence . '), ' . $schemaString . ' FROM ' . $tableName;
		$this->Room->query($sql);
		$result = $this->Room->getAffectedRows() > 0;
		if (! $result) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		if (! $this->Room->deleteAll(array('1' => '1'), false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$sql = 'INSERT INTO ' . $tableName . '(id, ' . $schemaString . ') ' .
				'SELECT id, ' . $schemaString . ' FROM ' . $tableName . '_tmps';
		$this->Room->query($sql);
		$result = $this->Room->getAffectedRows() > 0;
		if (! $result) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		//roomsテーブルのid、parent_id、lft、rghtをずらす
		$update = array(
			'root_id' => 'root_id + (' . $sequence . ')',
			'parent_id' => 'parent_id + (' . $sequence . ')',
			'lft' => 'lft + (' . $sequence . ')',
			'rght' => 'rght + (' . $sequence . ')',
		);
		if (! $this->Room->updateAll($update, array('1 = 1'))) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		if ($direction === 'down') {
			$update = array(
				'parent_id' => null,
			);
			$conditions = array(
				'id' => array('1', '2', '3')
			);
			if (! $this->Room->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

		} else {
			$update = array(
				'parent_id' => '1',
			);
			$conditions = array(
				'id' => array('2', '3', '4')
			);
			if (! $this->Room->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$result = $this->Room->find('list', array(
				'recursive' => -1,
				'fields' => 'rght',
				'order' => array('rght' => 'desc'),
				'limit' => 1,
			));
			$maxRght = array_shift($result);

			$data = $this->records['Room'][0];
			$data['rght'] = $maxRght + 1;
			$this->Room->create(false);
			if (! $this->Room->save($data)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
	}

/**
 * RoomsLanguageテーブルの登録
 *
 * @param string $direction Migration処理 (up or down)
 * @return void
 * @throws InternalErrorException
 */
	private function __saveRoomsLanguage($direction) {
		$RoomsLanguage = $this->generateModel('RoomsLanguage');

		if ($direction === 'down') {
			if (! $RoomsLanguage->deleteAll(array('room_id' => '1'), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} else {
			foreach ($this->records['RoomsLanguage'] as $record) {
				$RoomsLanguage->create(false);
				if (! $RoomsLanguage->save($record, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
		}
	}

/**
 * RolesRoomテーブルの登録
 *
 * @param string $direction Migration処理 (up or down)
 * @param array $models 更新するモデルリスト
 * @return void
 * @throws InternalErrorException
 */
	private function __saveRolesRoom($direction, $models) {
		$RolesRoom = $this->generateModel('RolesRoom');

		if ($direction === 'down') {
			$rolesRoomIds = $RolesRoom->find('list', array(
				'recursive' => -1,
				'conditions' => array('room_id' => '1'),
			));

			$this->__deleteRolesRoomAssociations($models, $rolesRoomIds);

			if (! $RolesRoom->deleteAll(array('room_id' => '1'), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		} else {
			foreach ($this->records['RolesRoom'] as $record) {
				$copyRolesRoomId = $record['copy_id'];
				unset($record['copy_id']);

				$RolesRoom->create(false);
				$result = $RolesRoom->save($record, false);
				if (! $result) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}

				$rolesRoomId = $result[$RolesRoom->alias]['id'];
				$this->__saveRolesRoomAssociations($models, $rolesRoomId, $copyRolesRoomId);
			}
		}
	}

/**
 * RolesRoomの関連テーブルの登録
 *
 * @param array $models 更新するモデルリスト
 * @param int $rolesRoomId ロール-ルームID
 * @param int $copyRolesRoomId コピー元のロール-ルームID
 * @return void
 */
	private function __saveRolesRoomAssociations($models, $rolesRoomId, $copyRolesRoomId) {
		foreach ($models as $modelName) {
			$model = $this->generateModel($modelName);
			if (! $model->hasField('roles_room_id')) {
				continue;
			}
			$tableName = $model->tablePrefix . $model->table;
			$schema = $model->schema();
			unset($schema['id'], $schema['roles_room_id']);

			if ($model->hasField('room_id')) {
				unset($schema['room_id']);
				$insertSchema = 'roles_room_id, room_id, ' . implode(', ', array_keys($schema));
				$selectSchema = $rolesRoomId . ', ' . '1, ' . implode(', ', array_keys($schema));
			} else {
				$insertSchema = 'roles_room_id, ' . implode(', ', array_keys($schema));
				$selectSchema = $rolesRoomId . ', ' . implode(', ', array_keys($schema));
			}

			$sql = 'INSERT INTO ' . $tableName . '(' . $insertSchema . ') ' .
					'SELECT ' . $selectSchema . ' FROM ' . $tableName .
					' WHERE roles_room_id = ' . $copyRolesRoomId;
			$model->query($sql);
		}
	}

/**
 * RolesRoomの関連テーブルの削除
 *
 * @param array $models 更新するモデルリスト
 * @param array $rolesRoomIds ロール―ルームIDリスト
 * @return void
 * @throws InternalErrorException
 */
	private function __deleteRolesRoomAssociations($models, $rolesRoomIds) {
		foreach ($models as $modelName) {
			$model = $this->generateModel($modelName);
			if (! $model->hasField('roles_room_id')) {
				continue;
			}
			if (! $model->deleteAll(array('roles_room_id' => $rolesRoomIds), false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
	}

}
