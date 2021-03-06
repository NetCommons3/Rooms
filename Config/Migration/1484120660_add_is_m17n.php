<?php
/**
 * 多言語を使うスペースかどうかのフィールド追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 多言語を使うスペースかどうかのフィールド追加 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class AddIsM17n extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_is_m17n';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'spaces' => array(
					'is_m17n' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'permalink'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'spaces' => array('is_m17n'),
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
