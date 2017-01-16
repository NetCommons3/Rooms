<?php
/**
 * Spaceのパーマリンクを付けるように修正 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');
App::uses('Space', 'Rooms.Model');

/**
 * Spaceのパーマリンクを付けるように修正 Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class AddPermalink1 extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_permalink_1';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
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
		if ($direction === 'up') {
			$Space = $this->generateModel('Space');
			$update = array(
				'permalink' => '\'private\''
			);
			$conditions = array(
				'id' => Space::PRIVATE_SPACE_ID
			);
			if (! $Space->updateAll($update, $conditions)) {
				return false;
			}

			$update = array(
				'permalink' => '\'community\''
			);
			$conditions = array(
				'id' => Space::COMMUNITY_SPACE_ID
			);
			if (! $Space->updateAll($update, $conditions)) {
				return false;
			}
		}
		return true;
	}
}
