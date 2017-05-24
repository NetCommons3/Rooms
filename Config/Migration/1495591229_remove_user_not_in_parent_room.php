<?php
/**
 * RemoveUserNotInParentRoom
 *
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');
App::uses('Space', 'Rooms.Model');
App::uses('Room', 'Rooms.Model');
App::uses('RolesRoomsUser', 'Rooms.Model');

/**
 * RemoveUserNotInParentRoom
 *
 */
class RemoveUserNotInParentRoom extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'remove_user_not_in_parent_room';

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
		/* @var $Room Room */
		$Room = ClassRegistry::init('Rooms.Room');
		$communityRootId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
		$query = [
			'fields' => [
				'Room.id',
				'Room.parent_id'
			],
			'conditions' => [
				'Room.space_id' => Space::COMMUNITY_SPACE_ID,
				'Room.parent_id !=' => $communityRootId,
				'Room.id !=' => $communityRootId
			],
			'recursive' => -1,
			'callbacks' => false,
		];
		$subRoomIdList = $Room->find('list', $query);

		/* @var $RolesRoomsUser RolesRoomsUser */
		$RolesRoomsUser = ClassRegistry::init('Rooms.RolesRoomsUser');
		$query = [
			'fields' => [
				'RolesRoomsUser.id',
				'RolesRoomsUser.user_id',
				'RolesRoomsUser.room_id',
			],
			'conditions' => [
				'RolesRoomsUser.room_id' => $subRoomIdList,
			],
			'recursive' => -1,
			'callbacks' => false,
		];
		$parentRoomUserList = $RolesRoomsUser->find('list', $query);

		foreach ($subRoomIdList as $subRoomId => $parentRoomId) {
			$userList = $parentRoomUserList[$parentRoomId];
			$conditions = [
				'RolesRoomsUser.room_id' => $subRoomId,
				'NOT' => [
					'RolesRoomsUser.user_id' => $userList,
				],
			];
			if (!$RolesRoomsUser->deleteAll($conditions, false)) {
				return false;
			}
		}

		return true;
	}

}
