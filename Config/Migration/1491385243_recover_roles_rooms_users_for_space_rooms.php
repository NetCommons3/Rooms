<?php
/**
 * 不要なRoom.root_idを削除
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 不要なRoom.root_idを削除
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 */
class RecoverRolesRoomsUsersForSpaceRooms extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'recover_roles_rooms_users_for_space_rooms';

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
		$this->RolesRoomsUser = ClassRegistry::init('Rooms.RolesRoomsUser');
		$this->RolesRoomsUser->setMasterDataSource();

		$spaceRolesRoomIds = $this->RolesRoomsUser->getSpaceRolesRoomsUsers();

		$publicRooms = $this->RolesRoomsUser->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID),
			),
		));
		foreach ($publicRooms as $publicRoom) {
			$result = $this->RolesRoomsUser->saveSpaceRoomForRooms(
				$publicRoom['RolesRoomsUser'], $spaceRolesRoomIds
			);
			if (! $result) {
				return false;
			}
		}

		return true;
	}
}
