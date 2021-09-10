<?php
/**
 * RolesRoomsUser::getRolesRoomsUsers()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * RolesRoomsUser::getRolesRoomsUsers()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\RolesRoomsUser
 */
class RolesRoomsUserGetRolesRoomsUsersTest extends NetCommonsGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.roles_room',
		'plugin.rooms.roles_rooms_user',
		'plugin.rooms.room',
		'plugin.rooms.room_role',
		'plugin.rooms.room_role_permission',
		'plugin.rooms.rooms_language',
		'plugin.rooms.space',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'rooms';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'RolesRoomsUser';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getRolesRoomsUsers';

/**
 * getRolesRoomsUsers()のテスト
 *
 * @return void
 */
	public function testGetRolesRoomsUsers() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$userId = '1';
		$spaceId = '2';
		$conditions = array(
			'RolesRoomsUser.user_id' => $userId,
			'Room.space_id' => $spaceId
		);

		//テスト実施
		$result = $this->$model->$methodName($conditions, ['order' => ['Room.id' => 'asc']]);

		//チェック
		$this->assertCount(3, $result);
		$this->__assertRoom($result[0], $spaceId, '2', $userId, '1', 'room_administrator');
		$this->__assertRoom($result[1], $spaceId, '5', $userId, '6', 'room_administrator');
		$this->__assertRoom($result[2], $spaceId, '6', $userId, '7', 'room_administrator');
	}

/**
 * roomのチェック
 *
 * @param array $result ルーム配列
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param int $userId ユーザID
 * @param int $rolesRoomId ロールルームID
 * @param string $roleKey ロールキー
 * @return void
 */
	private function __assertRoom($result, $spaceId, $roomId, $userId, $rolesRoomId, $roleKey) {
		$this->assertEquals(array('RolesRoomsUser', 'RolesRoom', 'Room'), array_keys($result));

		$this->assertEquals($rolesRoomId, Hash::get($result, 'RolesRoomsUser.id'));
		$this->assertEquals($userId, Hash::get($result, 'RolesRoomsUser.user_id'));
		$this->assertEquals($roomId, Hash::get($result, 'RolesRoomsUser.room_id'));

		$this->assertEquals($rolesRoomId, Hash::get($result, 'RolesRoom.id'));
		$this->assertEquals($roomId, Hash::get($result, 'RolesRoom.room_id'));
		$this->assertEquals($roleKey, Hash::get($result, 'RolesRoom.role_key'));

		$this->assertEquals($roomId, Hash::get($result, 'Room.id'));
		$this->assertEquals($spaceId, Hash::get($result, 'Room.space_id'));
	}

}
