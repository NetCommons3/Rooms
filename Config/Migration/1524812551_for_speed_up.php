<?php
/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class ForSpeedUp extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'for_speed_up';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'roles_rooms_users' => array(
					'roles_room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
				'rooms_languages' => array(
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false, 'key' => 'index'),
				),
			),
			'create_field' => array(
				'roles_rooms_users' => array(
					'indexes' => array(
						'roles_room_id' => array('column' => 'roles_room_id', 'unique' => 0),
						'room_id' => array('column' => 'room_id', 'unique' => 0),
					),
				),
				'rooms_languages' => array(
					'indexes' => array(
						'language_id' => array('column' => array('language_id', 'id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'roles_rooms_users' => array(
					'roles_room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'rooms_languages' => array(
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
				),
			),
			'drop_field' => array(
				'roles_rooms_users' => array('indexes' => array('roles_room_id', 'room_id')),
				'rooms_languages' => array('indexes' => array('language_id')),
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
