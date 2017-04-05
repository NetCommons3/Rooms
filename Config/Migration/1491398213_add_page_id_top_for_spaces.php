<?php
/**
 * Space.page_id_topを追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * Space.page_id_topを追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class AddPageIdTopForSpaces extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_page_id_top_for_spaces';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'spaces' => array(
					'page_id_top' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'room_id_root'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'spaces' => array('page_id_top'),
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
