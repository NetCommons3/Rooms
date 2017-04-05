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
App::uses('Page', 'Pages.Model');
App::uses('Space', 'Rooms.Model');

/**
 * Space.page_id_topを追加
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class AddPageIdTopForSpaces1 extends NetCommonsMigration {

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
		$Space = $this->generateModel('Space');

		$Space->id = Space::PUBLIC_SPACE_ID;
		$Space->saveField('page_id_top', Page::PUBLIC_ROOT_PAGE_ID, false);

		$Space->id = Space::PRIVATE_SPACE_ID;
		$Space->saveField('page_id_top', Page::PRIVATE_ROOT_PAGE_ID, false);

		$Space->id = Space::COMMUNITY_SPACE_ID;
		$Space->saveField('page_id_top', Page::ROOM_ROOT_PAGE_ID, false);

		return true;
	}
}
