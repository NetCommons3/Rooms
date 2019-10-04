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

/**
 * ルーム削除時に関連して削除するテーブル Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 */
class RoomDeleteRelatedTable extends RoomsAppModel {

/**
 * 削除
 *
 * @var array
 */
	private $__defaultConditions = [
		'rooms' => [
			'id' => ['*.room_id'],
		],
		'blocks' => [
			'id' => ['*.block_id'],
			'key' => ['*.block_key'],
		],
		'pages' => [
			'id' => ['*.page_id'],
		],
		'users' => [
			'id' => [
				'*.user_id',
				'calendar_event_share_users.share_user',
				'groups.created_user',
				'reservation_event_share_users.share_user'
			],
		],
		'frames' => [
			'id' => ['*.frame_id'],
			'key' => ['*.frame_key'],
		],
		'roles_rooms' => [
			'id' => ['*.roles_room_id'],
		],
	];

/**
 * ルームIDを基にルーム削除関連データを追加
 *
 * @param int|string $roomId ルームID
 * @return void
 */
	public function insertByRoomId($roomId) {
		$this->loadModels([
			'Room' => 'Rooms.Room',
			'RolesRoom' => 'Rooms.RolesRoom',
			'Page' => 'Pages.Page',
			'Frame' => 'Frames.Frame',
			'Block' => 'Blocks.Block',
		]);

		$targetTables = [
			//$this->Room->table,
			$this->Page->table,
			$this->Frame->table,
			$this->Block->table,
			$this->RolesRoom->table,
		];

		//トランザクションBegin
		$this->begin();

		try {
			$table = $this->Room->table;
			$this->__execInsertQuery(
				$roomId, 'id', $roomId, $table, 'id', $this->__defaultConditions[$table]
			);

			foreach ($targetTables as $table) {
				$this->__execInsertQuery(
					$roomId, 'room_id', $roomId, $table, 'id', $this->__defaultConditions[$table]
				);
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

		//トランザクションBegin
		$this->begin();

		try {
			$table = $this->User->table;
			$this->__execInsertQuery(
				$roomId, 'id', $userId, $table, 'id', $this->__defaultConditions[$table]
			);

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}
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

}
