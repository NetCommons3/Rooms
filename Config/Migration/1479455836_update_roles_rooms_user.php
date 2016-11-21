<?php
/**
 * Initial data generation of Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');
App::uses('Room', 'Rooms.Model');

/**
 * Initial data generation of Migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class UpdateRolesRoomsUser extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_roles_rooms_user';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(),
		'down' => array(),
	);

/**
 * Records keyed by model name.
 *
 * @var array $records
 */
	public $records = array();

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
//		if ($direction === 'down') {
//			return true;
//		}
//
//		$RolesRoomsUser = ClassRegistry::init('Rooms.RolesRoomsUser');
//
//		$spaceRolesRoomIds = $RolesRoomsUser->getSpaceRolesRoomsUsers();
//		$rolesRoomsUsers = $RolesRoomsUser->find('all', array(
//			'recursive' => -1,
//			'conditions' => array(
//				'room_id' => Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID)
//			),
//		));
//		foreach ($rolesRoomsUsers as $data) {
//			if (! $RolesRoomsUser->saveSpaceRoomForRooms($data['RolesRoomsUser'], $spaceRolesRoomIds)) {
//				return false;
//			}
//		}

		return true;
	}
}
