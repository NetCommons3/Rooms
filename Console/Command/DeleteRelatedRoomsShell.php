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
App::uses('RoomsLibLog', 'Rooms.Lib');

/**
 * ルーム削除した際の関連テーブルのデータを削除する
 *
 * @property DeleteRelatedRoomsTask $DeleteRelatedRooms
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Console\Command
 */
class DeleteRelatedRoomsShell extends AppShell {

/**
 * 使用するタスク
 *
 * @var array
 */
	public $tasks = [
		'Rooms.DeleteRelatedRooms',
	];

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
		RoomsLibLog::shellStartLog($this);

		$this->DeleteRelatedRooms->execute();

		RoomsLibLog::shellEndLog($this);
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
