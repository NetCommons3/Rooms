<?php
/**
 * ルーム削除した際の関連テーブルのデータを削除する
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppShell', 'Console/Command');
App::uses('RoomsLibForeignConditionsParser', 'Rooms.Lib');
App::uses('RoomsLibDeleteRoomTables', 'Rooms.Lib');
App::uses('RoomsLibLog', 'Rooms.Lib');
App::uses('SiteSettingUtil', 'SiteManager.Utility');

/**
 * ルーム削除した際の関連テーブルのデータを削除する
 *
 * @property Room $Room
 * @property RolesRoomsUser $RolesRoomsUser
 * @property User $User
 * @property PrivateSpace $PrivateSpace
 * @property RoomDeleteRelatedTable $RoomDeleteRelatedTable
 *
 * @property DeleteRelatedRoomsTask $DeleteRelatedRooms
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Console\Command
 */
class DeleteNotUsedUsersShell extends AppShell {

/**
 * 使用するモデル
 *
 * @var array
 */
	public $uses = [
		'Rooms.Room',
		'Rooms.RolesRoomsUser',
		'Users.User',
		'PrivateSpace.PrivateSpace',
		'Rooms.RoomDeleteRelatedTable',
	];

/**
 * 使用するタスク
 *
 * @var array
 */
	public $tasks = [
		'Rooms.DeleteRelatedRooms',
	];

/**
 * 一度の処理する件数
 *
 * @var int
 */
	const PROCESS_COUNT = 10;

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->out(__d('rooms', 'Delete room associations Shell'));
		$this->hr();
	}

/**
 * 処理実行
 *
 * @return void
 */
	public function main() {
		//ログ出力
		RoomsLibLog::shellStartLog($this);

		//プライベートルームのデータ削除
		$result = $this->Room->query(
			$this->__makeSqlDeletedPrivateRoom('COUNT(*)')
		);
		$totalCount = $result[0][0]['count_num'];
		$maxLoop = ceil($totalCount / self::PROCESS_COUNT);
		$procCount = 0;

		for ($i = 0; $i < $maxLoop; $i++) {
			//ログ出力
			RoomsLibLog::processStartLog(
				$this,
				sprintf('Delete Private Rooms[%s]', ($i * self::PROCESS_COUNT + 1) . '/' . $totalCount)
			);

			$records = $this->Room->query(
				$this->__makeSqlDeletedPrivateRoom($this->Room->alias . '.id')
			);
			foreach ($records as $record) {
				//$this->out(sprintf('room_id=%s', $record[$this->Room->alias]['id']), 1, self::VERBOSE);
				$this->__deleteRoom($record[$this->Room->alias]['id']);
				$procCount++;
			}

			//ログ出力
			RoomsLibLog::processEndLog(
				$this,
				sprintf('Delete Private Rooms[%s]', $procCount . '/' . $totalCount)
			);
		}

		//ユーザのデータ削除
		$result = $this->User->query(
			$this->__makeSqlDeletedUser('COUNT(*)')
		);
		$totalCount = $result[0][0]['count_num'];
		$maxLoop = ceil($totalCount / self::PROCESS_COUNT);
		$procCount = 0;

		for ($i = 0; $i < $maxLoop; $i++) {
			//ログ出力
			RoomsLibLog::processStartLog(
				$this,
				sprintf('Delete Users[%s]', ($i * self::PROCESS_COUNT + 1) . '/' . $totalCount)
			);

			$records = $this->User->query(
				$this->__makeSqlDeletedUser($this->User->alias . '.id')
			);

			$this->RoomDeleteRelatedTable->begin();
			foreach ($records as $record) {
				try {
					//プライベートルーム削除
					//ルーム削除情報を登録する
					$this->RoomDeleteRelatedTable->insertUser($record[$this->User->alias]['id'], '0');

					//トランザクションCommit
					$this->RoomDeleteRelatedTable->commit();

				} catch (Exception $ex) {
					//トランザクションRollback
					$this->RoomDeleteRelatedTable->rollback($ex);
				}

				$procCount++;
			}

			//ログ出力
			RoomsLibLog::processEndLog(
				$this,
				sprintf('Delete Users[%s]', $procCount . '/' . $totalCount)
			);
		}

		//関連データの削除
		$this->DeleteRelatedRooms->execute();

		RoomsLibLog::shellEndLog($this);
	}

/**
 * 既に削除されているユーザに対するプライベートルームIDを取得するSQLの生成
 *
 * @param string $columnName 取得するカラム名
 * @return void
 */
	private function __makeSqlDeletedPrivateRoom($columnName) {
		$roomTableName = $this->Room->tablePrefix . $this->Room->table;
		$roomAliasName = $this->Room->alias;
		$userRoomTableName = $this->RolesRoomsUser->tablePrefix . $this->RolesRoomsUser->table;
		$userRoomAliasName = $this->RolesRoomsUser->alias;

		$sqlFromWhere = "{$roomTableName} AS {$roomAliasName} " .
				"LEFT JOIN {$userRoomTableName} AS {$userRoomAliasName} " .
					"ON ({$roomAliasName}.id = {$userRoomAliasName}.room_id) " .
				"WHERE {$roomAliasName}.space_id = " . Space::PRIVATE_SPACE_ID . " " .
					"AND {$roomAliasName}.page_id_top IS NOT NULL " .
					"AND {$userRoomAliasName}.id IS NULL";

		if ($columnName === 'COUNT(*)') {
			$sql = "SELECT {$columnName} count_num FROM {$sqlFromWhere}";
		} else {
			$sql = "SELECT {$columnName} FROM {$sqlFromWhere} LIMIT " . self::PROCESS_COUNT;
		}

		return $sql;
	}

/**
 * 既に削除されているユーザに対するプライベートルームIDを取得するSQLの生成
 *
 * @param string $columnName 取得するカラム名
 * @return void
 */
	private function __makeSqlDeletedUser($columnName) {
		$userTableName = $this->User->tablePrefix . $this->User->table;
		$usetAliasName = $this->User->alias;
		$delRoomTableName = $this->RoomDeleteRelatedTable->tablePrefix .
						$this->RoomDeleteRelatedTable->table;
		$delRoomAliasName = $this->RoomDeleteRelatedTable->alias;

		$sqlFromWhere = "{$userTableName} AS {$usetAliasName} " .
				"LEFT JOIN {$delRoomTableName} AS {$delRoomAliasName} " .
					"ON (" .
						"{$delRoomAliasName}.delete_table_name = 'users' " .
						"AND {$delRoomAliasName}.field_name = 'id' " .
						"AND {$delRoomAliasName}.value = {$usetAliasName}.id" .
					") " .
				"WHERE {$usetAliasName}.is_deleted = 1 " .
				"AND {$delRoomAliasName}.id IS NULL";

		if ($columnName === 'COUNT(*)') {
			$sql = "SELECT {$columnName} count_num FROM {$sqlFromWhere}";
		} else {
			$sql = "SELECT {$columnName} FROM {$sqlFromWhere} LIMIT " . self::PROCESS_COUNT;
		}

		return $sql;
	}

/**
 * ルーム削除
 *
 * @param int|string $roomId ルームID
 * @return void
 */
	private function __deleteRoom($roomId) {
		$this->RoomDeleteRelatedTable->begin();
		try {
			//プライベートルーム削除
			//ルーム削除情報を登録する
			$this->RoomDeleteRelatedTable->insertByRoomId($roomId);

			//roles_roomsデータ削除
			$this->Room->deleteRolesRoomByRoom($roomId);

			//frameデータの削除
			$this->Room->deleteFramesByRoom($roomId);

			//pageデータの削除
			$this->Room->deletePagesByRoom($roomId);

			//blockデータの削除
			$this->Room->deleteBlocksByRoom($roomId);

			//トランザクションCommit
			//$this->RoomDeleteRelatedTable->rollback();
			$this->RoomDeleteRelatedTable->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->RoomDeleteRelatedTable->rollback($ex);
		}
	}

/**
 * 引数の使い方の取得
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('rooms', 'The Delete room associations Shell'));
	}

}
