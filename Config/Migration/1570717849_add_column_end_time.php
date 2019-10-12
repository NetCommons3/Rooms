<?php
/**
 * ルーム削除時に不要データを削除するために使用するテーブルに終了日時を追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * ルーム削除時に不要データを削除するために使用するテーブルに終了日時を追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class AddColumnEndTime extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_end_time';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'room_delete_related_tables' => array(
					'end_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'start_time'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'room_delete_related_tables' => array('end_time'),
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
