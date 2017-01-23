<?php
/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 多言語化対応
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class AddIsOriginalCopy1 extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_is_original_copy_1';

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
		if (! Configure::read('NetCommons.installed')) {
			return true;
		}

		if ($direction === 'up') {
			$Room = $this->generateModel('Room');
			$RoomsLanguage = $this->generateModel('RoomsLanguage');

			$roomsLangIds = $Room->find('list', array(
				'recursive' => -1,
				'fields' => array('RoomsLanguage.id', 'RoomsLanguage.id'),
				'conditions' => array(
					$Room->alias . '.space_id' => '3',
					$RoomsLanguage->alias . '.language_id' => '1',
					$Room->alias . '.page_id_top NOT' => null,
				),
				'joins' => array(
					array(
						'table' => $RoomsLanguage->table,
						'alias' => $RoomsLanguage->alias,
						'type' => 'INNER',
						'conditions' => array(
							$RoomsLanguage->alias . '.room_id' . ' = ' . $Room->alias . ' .id',
						),
					),
				)
			));

			$conditions = array(
				'RoomsLanguage.id' => $roomsLangIds
			);
			$RoomsLanguage->deleteAll($conditions, false);
		}
		return true;
	}
}
