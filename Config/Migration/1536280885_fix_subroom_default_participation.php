<?php
/**
 * コミュニティスペースのルームがデフォルト参加OFFでそのサブルームがデフォルト参加ONのバグ修正
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * コミュニティスペースのルームがデフォルト参加OFFでそのサブルームがデフォルト参加ONのバグ修正
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 * @see https://github.com/NetCommons3/NetCommons3/issues/1336
 */
class FixSubroomDefaultParticipation extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fix_subroom_default_participation';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'rooms' => array('indexes' => array('default_participation')),
			),
			'create_field' => array(
				'rooms' => array(
					'indexes' => array(
						'default_participation' => array('column' => array('default_participation', 'parent_id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'create_field' => array(
				'rooms' => array(
					'indexes' => array(
						'default_participation' => array(),
					),
				),
			),
			'drop_field' => array(
				'rooms' => array('indexes' => array('default_participation')),
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
