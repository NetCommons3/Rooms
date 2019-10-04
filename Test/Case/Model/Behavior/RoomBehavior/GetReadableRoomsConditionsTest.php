<?php
/**
 * RoomBehavior::getReadableRoomsConditions()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('CurrentLibUser', 'NetCommons.Lib');
App::uses('UserRole', 'UserRoles.Model');

/**
 * RoomBehavior::getReadableRoomsConditions()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\RoomBehavior
 */
class RoomBehaviorGetReadableRoomsConditionsTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.plugins_role4test',
		'plugin.rooms.roles_room4test',
		'plugin.rooms.roles_rooms_user4test',
		'plugin.rooms.room4test',
		'plugin.rooms.rooms_language4test',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'rooms';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Rooms', 'TestRooms');
		$this->TestModel = ClassRegistry::init('TestRooms.TestRoomBehaviorModel');

		//テストで使用するため
		$this->Room = ClassRegistry::init('Rooms.Room');
	}

/**
 * ログイン処理
 *
 * @return void
 */
	private function __loginWithoutPrivateRoom() {
		$user = [
			'id' => '1',
			'role_key' => UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR,
		];
		$this->__login($user);
	}

/**
 * ログイン処理
 *
 * @return void
 */
	private function __loginWithPrivateRoom() {
		$user = [
			'id' => '1',
			'role_key' => UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR,
			'UserRoleSetting' => [
				'use_private_room' => true,
			],
		];
		$this->__login($user);
		CurrentLib::write('User.UserRoleSetting.use_private_room', true);
	}

/**
 * ログイン処理
 *
 * @param array $user ユーザ情報
 * @return void
 */
	private function __login($user) {
		$userLibInstance = CurrentLibUser::getInstance();

		$reflectionClass = new ReflectionClass('CurrentLibUser');
		$property = $reflectionClass->getProperty('__user');
		$property->setAccessible(true);
		$property->setValue($userLibInstance, $user);

		CurrentLib::write('User.id', '1');
		CurrentLib::write('User.role_key', UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR);
	}

/**
 * getReadableRoomsConditions()のテスト
 *
 * @return void
 */
	public function testGetReadableRoomsConditions() {
		//テストデータ
		$this->__loginWithPrivateRoom();
		CurrentLib::write('PluginsRole.0.plugin_key', 'rooms');
		$conditions = array();

		//テスト実施
		$options = $this->TestModel->getReadableRoomsConditions($conditions);
		$result = $this->Room->find('all', $options);

		//チェック
		$this->assertCount(6, $result);
		$this->__assertRoom($result[0], '2', '2', array('5', '6'));
		$this->__assertRoom($result[1], '2', '5', array('9'));
		$this->__assertRoom($result[2], '2', '9', array());
		$this->__assertRoom($result[3], '2', '6', array());
		$this->__assertRoom($result[4], '3', '8', array());
		$this->__assertRoom($result[5], '4', '7', array());
	}

/**
 * getReadableRoomsConditions()のテスト(プライベートルームなし)
 *
 * @return void
 */
	public function testGetReadableRoomsConditionsWOPrivateRoom() {
		//テストデータ
		$this->__loginWithoutPrivateRoom();
		CurrentLib::write('PluginsRole.0.plugin_key', 'rooms');
		$conditions = array();

		//テスト実施
		$options = $this->TestModel->getReadableRoomsConditions($conditions);
		$result = $this->Room->find('all', $options);

		//チェック
		$this->assertCount(5, $result);
		$this->__assertRoom($result[0], '2', '2', array('5', '6'));
		$this->__assertRoom($result[1], '2', '5', array('9'));
		$this->__assertRoom($result[2], '2', '9', array());
		$this->__assertRoom($result[3], '2', '6', array());
		$this->__assertRoom($result[4], '4', '7', array());
	}

/**
 * getReadableRoomsConditions()のテスト(プライベートルームなし)
 *
 * @return void
 */
	public function testGetReadableRoomsConditionsWithConditions() {
		//テストデータ
		$this->__loginWithPrivateRoom();
		CurrentLib::write('PluginsRole.0.plugin_key', 'rooms');
		$conditions = array(
			'Room.space_id' => '3'
		);

		//テスト実施
		$options = $this->TestModel->getReadableRoomsConditions($conditions);
		$result = $this->Room->find('all', $options);

		//チェック
		$this->assertCount(1, $result);
		$this->__assertRoom($result[0], '3', '8', array());
	}

/**
 * getReadableRoomsConditions()のテスト(ログインなし)
 *
 * @return void
 */
	public function testGetReadableRoomsConditionsWOLogin() {
		//テストデータ
		$conditions = array();

		//テスト実施
		$options = $this->TestModel->getReadableRoomsConditions($conditions);
		$result = $this->Room->find('all', $options);

		//チェック
		$this->assertCount(4, $result);
		$this->__assertRoom($result[0], '2', '2', array('5', '6'));
		$this->__assertRoom($result[1], '2', '5', array('9'));
		$this->__assertRoom($result[2], '2', '9', array());
		$this->__assertRoom($result[3], '2', '6', array());
	}

/**
 * roomのチェック
 *
 * @param array $result ルーム配列
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param array $childRoom 子ルームID
 * @return void
 */
	private function __assertRoom($result, $spaceId, $roomId, $childRoom) {
		$this->assertEquals($roomId, Hash::get($result, 'Room.id'));
		$this->assertEquals($spaceId, Hash::get($result, 'Room.space_id'));
		$this->assertEquals($childRoom, Hash::extract($result, 'ChildRoom.{n}.id'));
		$this->assertEquals($roomId, Hash::get($result, 'RoomsLanguage.0.room_id'));
		$this->assertEquals($roomId, Hash::get($result, 'RoomsLanguage.1.room_id'));
		$this->assertNotEmpty(Hash::extract($result, 'RoomsLanguage.0.name'));
		$this->assertNotEmpty(Hash::extract($result, 'RoomsLanguage.1.name'));
	}

}
