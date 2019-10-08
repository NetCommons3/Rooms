<?php
/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsLibDeleteRoomTables', 'Rooms.Lib');

/**
 * ルーム削除時の関連テーブルのforeign_field_conditionsのパーサ処理に関するライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Lib
 */
class RoomsLibForeignConditionsParser {

/**
 * 条件のデフォルト値
 *
 * ※定数扱いだが、php7からconstに配列が使用できるが、php5はconstに配列が使えないためprivateのメンバー変数とする
 *
 * @var array
 */
	private static $__defaultConditions = [
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
	];

/**
 * foreign_field_conditionsのデータ取得する
 *
 * @param string $tableName テーブル名
 * @return array
 */
	public static function getForeignCondition($tableName) {
		if (isset(self::$__defaultConditions[$tableName])) {
			return self::$__defaultConditions[$tableName];
		} else {
			return [];
		}
	}

/**
 * 外部フィールドのDBに登録する値に変換する
 *
 * @param array $value 外部フィールドの条件(変換前)
 * @return string
 */
	public static function convertDbValue($value) {
		return json_encode($value);
	}

/**
 * 外部フィールドのDBに登録するから処理できる型に変換する
 *
 * @param string $value 外部フィールドの条件(変換後)
 * @return array
 */
	public static function invertDbValue($value) {
		return json_decode($value, true);
	}

}
