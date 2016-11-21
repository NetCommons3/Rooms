<?php
/**
 * ボックスの切り替え機能追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * ボックスの切り替え機能追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class AddRoomIdRoot extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_room_id_root';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'spaces' => array(
					'room_id_root' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'room_disk_size'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'spaces' => array('room_id_root'),
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
