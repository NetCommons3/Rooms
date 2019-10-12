<?php
/**
 * ルーム削除時に関連して削除するテーブル Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppModel', 'Rooms.Model');
App::uses('RoomsLibForeignConditionsParser', 'Rooms.Lib');

/**
 * ルーム削除時に関連して削除するテーブル Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 */
class RoomDeleteRelatedTable extends RoomsAppModel {

/**
 * ルームIDを基にルーム削除関連データを追加
 *
 * @param int|string $roomId ルームID
 * @return void
 */
	public function insertByRoomId($roomId) {
		$this->loadModels([
			'Room' => 'Rooms.Room',
			'Page' => 'Pages.Page',
			'Frame' => 'Frames.Frame',
			'Block' => 'Blocks.Block',
		]);

		if ($this->__existTableValue($this->Room->table, 'id', $roomId)) {
			return;
		}

		$targetTables = [
			//$this->Room->table,
			$this->Page->table,
			$this->Frame->table,
			$this->Block->table,
		];

		//トランザクションBegin
		$this->begin();

		try {
			$table = $this->Room->table;
			$this->__execInsertQuery(
				$roomId, 'id', $roomId, $table, 'id',
				RoomsLibForeignConditionsParser::getForeignCondition($table)
			);

			foreach ($targetTables as $table) {
				$foreignConditions = RoomsLibForeignConditionsParser::getForeignCondition($table);
				foreach ($foreignConditions as $field => $condition) {
					$this->__execInsertQuery(
						$roomId, 'room_id', $roomId, $table, $field, [$field => $condition]
					);
				}
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}
	}

/**
 * プライベートルームIDを基にルーム削除関連データを追加
 *
 * @param int|string $userId ユーザID
 * @param int|string $roomId プライベートルームID
 * @return void
 */
	public function insertUser($userId, $roomId) {
		$this->loadModels([
			'User' => 'Users.User',
		]);
		if ($this->__existTableValue($this->User->table, 'id', $userId)) {
			return;
		}

		//トランザクションBegin
		$this->begin();

		try {
			$table = $this->User->table;
			$this->__execInsertQuery(
				$roomId, 'id', $userId, $table, 'id',
				RoomsLibForeignConditionsParser::getForeignCondition($table)
			);

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}
	}

/**
 * 対象テーブルの値が存在するか否か
 *
 * @param string $tableName 対象テーブル名
 * @param string $fieldName 対象カラム名
 * @param string $value 対象の値
 *
 * @return bool
 */
	private function __existTableValue($tableName, $fieldName, $value) {
		$findTableName = $this->tablePrefix . $this->table;
		$result = $this->query(
			"SELECT COUNT(*) count_num FROM {$findTableName} AS {$this->alias}" .
				" WHERE {$this->alias}.delete_table_name = :tableName" .
				" AND {$this->alias}.field_name = :fieldName" .
				" AND {$this->alias}.value = :value",
			['tableName' => $tableName, 'fieldName' => $fieldName, 'value' => $value]
		);
		return $result[0][0]['count_num'] > 0;
	}

/**
 * ルーム削除に関する情報を追加する
 *
 * @param int|string $roomId ルームID
 * @param string $findField SELECTで取得するカラム名
 * @param string $findValue SELECTで取得する値
 * @param string $targetTableName 対象テーブル名
 * @param string $targetFieldName 対象カラム名
 * @param array $foreignConditions 外部キー情報
 *
 * @return void
 */
	private function __execInsertQuery(
			$roomId, $findField, $findValue, $targetTableName, $targetFieldName, $foreignConditions) {
		$db = $this->getDataSource();

		$loginUserId = Current::read('User.id', '0');

		$targetAlias = Inflector::classify($targetTableName);
		$fullTargetTableName = $this->tablePrefix . $targetTableName;

		$values = [
			'room_id' => $db->value($roomId, 'string'),
			'delete_table_name' => $db->value($targetTableName, 'string'),
			'field_name' => $db->value($targetFieldName, 'string'),
			'value' => $this->escapeField($targetFieldName, $targetAlias),
			'foreign_field_conditions' => $db->value(json_encode($foreignConditions), 'string'),
			'created' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'created_user' => $db->value($loginUserId, 'string'),
			'modified' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'modified_user' => $db->value($loginUserId, 'string'),
		];

		$sql = 'INSERT INTO ' . $this->tablePrefix . $this->table .
				' (' . implode(', ', array_keys($values)) . ')' .
				' SELECT ' . implode(', ', $values) .
				' FROM ' . $fullTargetTableName . ' AS ' . $targetAlias .
				' WHERE ' . $this->escapeField($findField, $targetAlias) . ' = ' . $findValue;

		//登録処理
		return $this->query($sql);
	}

/**
 * 開始時間の更新
 *
 * @param int|string $id ID
 * @return bool
 */
	public function updateStartTime($id) {
		$this->__updateField($id, 'start_time', gmdate('Y-m-d H:i:s'));
	}

/**
 * 終了日時の更新
 *
 * @param int|string $id ID
 * @return bool
 */
	public function updateEndTime($id) {
		$this->__updateField($id, 'end_time', gmdate('Y-m-d H:i:s'));
	}

/**
 * 更新処理
 *
 * @param int|string $id ID
 * @param string $name カラム名
 * @param string $value 値
 * @return bool|array See Model::save() False on failure or an array of model data on success.
 * @see Model::save()
 * @link https://book.cakephp.org/2.0/en/models/saving-your-data.html#model-savefield-string-fieldname-string-fieldvalue-validate-false
 */
	private function __updateField($id, $name, $value) {
		//トランザクションBegin
		$this->begin();

		try {
			$this->create(false);

			$options = ['validate' => false, 'fieldList' => [$name]];
			$this->save([$this->alias => [$this->primaryKey => $id, $name => $value]], $options);

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}
	}

}
