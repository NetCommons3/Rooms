<?php
/**
 * ルーム削除時に不要データを削除するために使用するテーブルの追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * ルーム削除時に不要データを削除するために使用するテーブルの追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class AddRoomsDeleteTables extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_delete_room_associations';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'room_delete_related_tables' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'room_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'ルームID'),
					'delete_table_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '削除するテーブル名 rooms|blokcs|pages|users|frames|roles_rooms', 'charset' => 'utf8'),
					'field_name' => array('type' => 'string', 'null' => false, 'default' => 'id', 'collate' => 'utf8_general_ci', 'comment' => 'カラム名', 'charset' => 'utf8'),
					'value' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '値', 'charset' => 'utf8'),
					'foreign_field_conditions' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1200, 'collate' => 'utf8_general_ci', 'comment' => '対象とするカラムリストをJSON形式で保持　ex) {"id":["*.block_id"], "key":["*.block_key"]}', 'charset' => 'utf8'),
					'start_time' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'room_id' => array('column' => 'room_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'room_delete_related_tables'
			),
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
 */
	public function after($direction) {
		return true;
	}
}
