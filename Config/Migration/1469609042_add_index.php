<?php
/**
 * AddIndex migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * AddIndex migration
 *
 * @package NetCommons\Rooms\Config\Migration
 */
class AddIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'roles_rooms_users' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
				'room_role_permissions' => array(
					'roles_room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'Role type
e.g.) roomRole, announcementBlockRole, bbsBlockRole
'),
				),
				'room_roles' => array(
					'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'rooms' => array(
					'space_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'in_draft' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '作成中かどうか。1: 作成中、0: 確定'),
				),
				'rooms_languages' => array(
					'room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
			),
			'create_field' => array(
				'roles_rooms_users' => array(
					'indexes' => array(
						'user_id' => array('column' => array('user_id', 'room_id'), 'unique' => 0),
					),
				),
				'room_role_permissions' => array(
					'indexes' => array(
						'roles_room_id' => array('column' => 'roles_room_id', 'unique' => 0),
					),
				),
				'room_roles' => array(
					'indexes' => array(
						'role_key' => array('column' => 'role_key', 'unique' => 0),
					),
				),
				'rooms' => array(
					'indexes' => array(
						'space_id' => array('column' => array('space_id', 'page_id_top', 'lft'), 'unique' => 0),
					),
				),
				'rooms_languages' => array(
					'indexes' => array(
						'room_id' => array('column' => 'room_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'roles_rooms_users' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'room_role_permissions' => array(
					'roles_room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'Role type
e.g.) roomRole, announcementBlockRole, bbsBlockRole
'),
				),
				'room_roles' => array(
					'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'rooms' => array(
					'space_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'in_draft' => array('type' => 'boolean', 'null' => false, 'default' => false, 'comment' => '作成中かどうか。1: 作成中、0: 確定'),
				),
				'rooms_languages' => array(
					'room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
			),
			'drop_field' => array(
				'roles_rooms_users' => array('indexes' => array('user_id')),
				'room_role_permissions' => array('indexes' => array('roles_room_id')),
				'room_roles' => array('indexes' => array('role_key')),
				'rooms' => array('indexes' => array('space_id')),
				'rooms_languages' => array('indexes' => array('room_id')),
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
