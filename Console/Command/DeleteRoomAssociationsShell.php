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

/**
 * ルーム削除した際の関連テーブルのデータを削除する
 *
 * @property RoomDeleteRelatedTable $RoomDeleteRelatedTable
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Console\Command
 */
class DeleteRoomAssociationsShell extends AppShell {

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
		$this->__RoomsLibDeleteRoomTables = new RoomsLibDeleteRoomTables($this->RoomDeleteRelatedTable);

		$conditions = [];
		if ($this->param('all')) {
			$conditions = [];
		} elseif ($this->param('room-id')) {
			$conditions = [
				'room_id' => $this->param('room-id'),
			];
		} else {
			return;
		}

		$records = $this->RoomDeleteRelatedTable->find('all', [
			'recursive' => -1,
			'conditions' => $conditions
		]);

		//トランザクションBegin
		$this->RoomDeleteRelatedTable->begin();
//\CakeLog::debug(__METHOD__ . '(' . __LINE__ . ') ' . var_export($records, true));

		try {
			foreach ($records as $record) {
				$this->__RoomsLibDeleteRoomTables->deleteRelatedTables(
					$record['RoomDeleteRelatedTable']['delete_table_name'],
					$record['RoomDeleteRelatedTable']['field_name'],
					$record['RoomDeleteRelatedTable']['value'],
					$record['RoomDeleteRelatedTable']['foreign_field_conditions']
				);
			}

			//トランザクションCommit
			$this->RoomDeleteRelatedTable->rollback();
			//$this->RoomDeleteRelatedTable->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->RoomDeleteRelatedTable->rollback($ex);
		}

		$this->hr();
	}

/**
 * 引数の使い方の取得
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('rooms', 'The Delete room associations Shell'))
			->addOption(
				'room-id',
				[
					'help' => __d('rooms', 'Process room id'),
				]
			)
			->addOption(
				'limit',
				[
					'help' => __d('rooms', 'Process limit'),
					'default' => 100,
				]
			)
			->addOption(
				'all',
				array(
					'short' => 'a',
					'boolean' => true,
					'help' => __d('rooms', 'All records'),
				)
			);
	}

}
