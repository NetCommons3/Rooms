<?php
/**
 * RoomBehavior::getRoomsConditions()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * RoomBehavior::getRoomsConditions()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\RoomBehavior
 */
class RoomBehaviorGetRoomsConditionsTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
 * getRoomsConditions()のテスト
 *
 * @return void
 */
	public function testGetRoomsCondtions() {
		//テストデータ
		$spaceId = '2';
		$conditions = array(
			'Room.id' => array('5', '6')
		);

		//テスト実施
		$options = $this->TestModel->getRoomsConditions($spaceId, ['conditions' => $conditions]);
		$result = $this->Room->find('all', $options);

		//チェック
		$this->assertCount(2, $result);
		$this->__assertRoom($result[0], $spaceId, '5', '2', array());
		$this->__assertRoom($result[1], $spaceId, '6', '2', array());
	}

/**
 * getRoomsConditions()のテスト($optionsが空)
 *
 * @return void
 */
	public function testGetRoomsNotCondtions() {
		//テストデータ
		$spaceId = '2';
		$options = array();

		//テスト実施
		$options = $this->TestModel->getRoomsConditions($spaceId, $options);
		$result = $this->Room->find('all', $options);

		//チェック
		$this->assertCount(3, $result);
		$this->__assertRoom($result[0], $spaceId, '2', '1', array('5', '6'));
		$this->__assertRoom($result[1], $spaceId, '5', '2', array());
		$this->__assertRoom($result[2], $spaceId, '6', '2', array());
	}

/**
 * roomのチェック
 *
 * @param array $result ルーム配列
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param int $parentRoomId 親ルームID
 * @param array $childRoom 子ルームID
 * @return void
 */
	private function __assertRoom($result, $spaceId, $roomId, $parentRoomId, $childRoom) {
		$this->assertEquals($roomId, Hash::get($result, 'Room.id'));
		$this->assertEquals($spaceId, Hash::get($result, 'Room.space_id'));
		$this->assertEquals($spaceId, Hash::get($result, 'Space.id'));
		$this->assertEquals($parentRoomId, Hash::get($result, 'ParentRoom.id'));
		$this->assertEquals($childRoom, Hash::extract($result, 'ChildRoom.{n}.id'));
		$this->assertEquals($roomId, Hash::get($result, 'RoomsLanguage.0.room_id'));
		$this->assertEquals($roomId, Hash::get($result, 'RoomsLanguage.1.room_id'));
		$this->assertNotEmpty(Hash::extract($result, 'RoomsLanguage.0.name'));
		$this->assertNotEmpty(Hash::extract($result, 'RoomsLanguage.1.name'));
	}

}
