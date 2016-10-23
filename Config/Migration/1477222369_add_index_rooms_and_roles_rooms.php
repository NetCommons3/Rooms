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
class AddIndexRoomsAndRolesRooms extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'Add_index_rooms_and_roles_rooms';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'roles_rooms' => array(
					'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'rooms' => array(
					'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
			),
			'create_field' => array(
				'roles_rooms' => array(
					'indexes' => array(
						'role_key' => array('column' => array('role_key', 'room_id'), 'unique' => 0),
					),
				),
				'rooms' => array(
					'indexes' => array(
						'lft' => array('column' => array('lft', 'id'), 'unique' => 0),
						'rght' => array('column' => array('rght', 'id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'roles_rooms' => array(
					'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'rooms' => array(
					'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
				),
			),
			'drop_field' => array(
				'roles_rooms' => array('indexes' => array('role_key')),
				'rooms' => array('indexes' => array('lft', 'rght')),
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
