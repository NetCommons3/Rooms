<?php
/**
 * 関連テーブルのデータを削除する
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppShell', 'Console/Command');
App::uses('RoomsLibDeleteRoomTables', 'Rooms.Lib');
App::uses('RoomsLibLog', 'Rooms.Lib');
App::uses('SiteSettingUtil', 'SiteManager.Utility');

/**
 * 関連テーブルのデータを削除する
 *
 * @property RoomDeleteRelatedTable $RoomDeleteRelatedTable
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Console\Command
 */
class DeleteRelatedRoomsTask extends AppShell {

/**
 * 使用するモデル
 *
 * @var array
 */
	public $uses = [
		'Rooms.RoomDeleteRelatedTable',
	];

/**
 * DeleteRoomTablesを操作するライブラリ
 *
 * @var RoomsLibDeleteRoomTables
 */
	private $__RoomsLibDeleteRoomTables;

/**
 * 一度の処理する件数
 *
 * @var int
 */
	const PROCESS_COUNT = 100;

/**
 * タスク実行
 *
 * @return void
 */
	public function execute() {
		$this->__RoomsLibDeleteRoomTables =
				new RoomsLibDeleteRoomTables($this->RoomDeleteRelatedTable, $this);

		$tableName = $this->RoomDeleteRelatedTable->tablePrefix .
						$this->RoomDeleteRelatedTable->table;
		$aliasName = $this->RoomDeleteRelatedTable->alias;

		$result = $this->RoomDeleteRelatedTable->query(
			"SELECT COUNT(*) count_num FROM {$tableName} AS {$aliasName}" .
				" WHERE {$aliasName}.end_time IS NULL" .
				//" AND {$aliasName}.room_id = 23"
				//" AND {$aliasName}.id = 1420"
				" FOR UPDATE"
		);
		if (empty($result)) {
			return $this->_stop();
		}

		$totalCount = $result[0][0]['count_num'];
		$maxLoop = ceil($totalCount / self::PROCESS_COUNT);
		$procCount = 0;

		for ($i = 0; $i < $maxLoop; $i++) {
			//ログ出力
			RoomsLibLog::processStartLog(
				$this,
				sprintf('Delete Related Tables[%s]', ($i * self::PROCESS_COUNT + 1) . '/' . $totalCount)
			);

			$records = $this->RoomDeleteRelatedTable->query(
				"SELECT {$aliasName}.* FROM {$tableName} AS {$aliasName}" .
					" WHERE {$aliasName}.end_time IS NULL" .
					//" AND {$aliasName}.room_id = 23"
					//" AND {$aliasName}.id = 1420" .
					" LIMIT " . self::PROCESS_COUNT
			);

			try {
				foreach ($records as $record) {
					//トランザクションBegin
					$this->RoomDeleteRelatedTable->begin();

					$recordId = $record['RoomDeleteRelatedTable']['id'];
					$this->RoomDeleteRelatedTable->updateStartTime($recordId);

					$this->__RoomsLibDeleteRoomTables->deleteRelatedTables(
						$record['RoomDeleteRelatedTable']['delete_table_name'],
						$record['RoomDeleteRelatedTable']['field_name'],
						$record['RoomDeleteRelatedTable']['value'],
						$record['RoomDeleteRelatedTable']['foreign_field_conditions']
					);

					$this->RoomDeleteRelatedTable->updateEndTime($recordId);

					$procCount++;

					//トランザクションCommit
					$this->RoomDeleteRelatedTable->commit();
					//$this->RoomDeleteRelatedTable->rollback();
				}

			} catch (Exception $ex) {
				//トランザクションRollback
				$this->RoomDeleteRelatedTable->rollback($ex);
			}

			//ログ出力
			RoomsLibLog::processEndLog(
				$this,
				sprintf('Delete Related Tables[%s]', $procCount . '/' . $totalCount)
			);
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
