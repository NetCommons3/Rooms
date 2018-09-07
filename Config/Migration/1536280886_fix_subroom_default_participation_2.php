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
App::uses('Space', 'Rooms.Model');
App::uses('Room', 'Rooms.Model');

/**
 * コミュニティスペースのルームがデフォルト参加OFFでそのサブルームがデフォルト参加ONのバグ修正
 * 不正なデータを削除
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Config\Migration
 * @see https://github.com/NetCommons3/NetCommons3/issues/1336
 */
class FixSubroomDefaultParticipation2 extends NetCommonsMigration {

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
		$this->Room = ClassRegistry::init('Rooms.Room');
		$this->RolesRoomsUser = ClassRegistry::init('Rooms.RolesRoomsUser');

		$roomIdRoot = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID, 'Room');
		$rooms = $this->Room->find('all', [
			'recursive' => 0,
			'conditions' => [
				'Room.id !=' => $roomIdRoot,
				'Room.parent_id !=' => $roomIdRoot,
				'Room.space_id' => Space::COMMUNITY_SPACE_ID,
				'Room.default_participation' => true,
				'ParentRoom.default_participation' => false,
			]
		]);
		$targets = [];
		foreach ($rooms as $room) {
			$rolesRoomsUsers = $this->RolesRoomsUser->find('all', [
				'recursive' => -1,
				'fields' => ['user_id'],
				'conditions' => [
					'room_id' => $room['ParentRoom']['id']
				],
			]);

			$targets[$room['Room']['id']] = [];
			foreach ($rolesRoomsUsers as $roomUser) {
				$targets[$room['Room']['id']][] = $roomUser['RolesRoomsUser']['user_id'];
			}
		}
		foreach ($targets as $roomId => $userIds) {
			$conditions = [
				'RolesRoomsUser.room_id' => $roomId,
				'RolesRoomsUser.user_id !=' => $userIds,
			];
			$this->RolesRoomsUser->deleteAll($conditions, false);
		}

		return true;
	}
}
