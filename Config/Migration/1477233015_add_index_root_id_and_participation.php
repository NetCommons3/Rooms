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
class AddIndexRootIdAndParticipation extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index_root_id_and_participation';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'rooms' => array(
					'root_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'default_participation' => array('type' => 'boolean', 'null' => true, 'default' => null, 'key' => 'index'),
				),
			),
			'create_field' => array(
				'rooms' => array(
					'indexes' => array(
						'default_participation' => array('column' => 'default_participation', 'unique' => 0),
						'root_id' => array('column' => 'root_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'rooms' => array(
					'root_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'default_participation' => array('type' => 'boolean', 'null' => true, 'default' => null),
				),
			),
			'drop_field' => array(
				'rooms' => array('indexes' => array('default_participation', 'root_id')),
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
