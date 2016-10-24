<?php
/**
 * RolesRoomFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * RolesRoomFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Fixture
 */
class RolesRoomFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'room_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'room_id' => '2',
			'role_key' => 'room_administrator',
		),
		array(
			'id' => '2',
			'room_id' => '2',
			'role_key' => 'chief_editor',
		),
		array(
			'id' => '3',
			'room_id' => '2',
			'role_key' => 'editor',
		),
		array(
			'id' => '4',
			'room_id' => '2',
			'role_key' => 'general_user',
		),
		array(
			'id' => '5',
			'room_id' => '2',
			'role_key' => 'visitor',
		),
		//別ルーム(room_id=4)
		array(
			'id' => '6',
			'room_id' => '5',
			'role_key' => 'room_administrator',
		),
		//別ルーム(room_id=5、ブロックなし)
		array(
			'id' => '7',
			'room_id' => '6',
			'role_key' => 'room_administrator',
		),
	);
}
