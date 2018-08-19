<?php
/**
 * TreeBehaviorの改善
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * TreeBehaviorの改善
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class ForNetcommonsTreeBehavior extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'for_netcommons_tree_behavior';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'rooms' => array(
					'weight' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'rght'),
					'sort_key' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'weight'),
					'child_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'sort_key'),
					'indexes' => array(
						'parent_id_2' => array('column' => array('parent_id', 'sort_key', 'id'), 'unique' => 0),
						'sort_key' => array('column' => array('sort_key', 'id'), 'unique' => 0),
						'weight' => array('column' => array('parent_id', 'weight'), 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'rooms' => array('indexes' => array('parent_id')),
			),
		),
		'down' => array(
			'drop_field' => array(
				'rooms' => array('weight', 'sort_key', 'child_count', 'indexes' => array('parent_id_2', 'sort_key', 'weight')),
			),
			'create_field' => array(
				'rooms' => array(
					'indexes' => array(
						'parent_id' => array('column' => 'parent_id', 'unique' => 0),
					),
				),
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
