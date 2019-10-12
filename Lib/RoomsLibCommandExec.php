<?php
/**
 * コマンド実行 ライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * コマンド実行 ライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Lib
 * @see MailSend よりコピー
 */
class RoomsLibCommandExec {

/**
 * 関連データの削除処理のシェルを実行
 *
 * @return void
 */
	public static function deleteRelatedRooms() {
		// バックグラウンドで実行
		// コマンド例) Console/cake rooms.delete_related_rooms
		self::__execInBackground(APP . 'Console' . DS . 'cake rooms.delete_related_rooms -q');
	}

/**
 * バックグラウンド実行
 *
 * @param string $cmd コマンド
 * @return void
 */
	private static function __execInBackground($cmd) {
		if (self::__isWindows()) {
			// Windowsの場合
			pclose(popen('cd ' . APP . ' && start /B ' . $cmd, 'r'));
		} else {
			// Linuxの場合
			// logrotate問題対応 http://dqn.sakusakutto.jp/2012/08/php_exec_nohup_background.html
			exec('nohup ' . $cmd . ' > /dev/null &');
		}
	}

/**
 * 動作しているOS がWindows かどうかを返す。
 *
 * @return bool
 */
	private static function __isWindows() {
		if (DIRECTORY_SEPARATOR == '\\') {
			return true;
		}
		return false;
	}
}
