<?php
/**
 * RoomBehavior::getSpaces()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * RoomBehavior::getSpaces()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\RoomBehavior
 */
class RoomBehaviorGetSpacesTest extends NetCommonsModelTestCase {

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
	}

/**
 * spaceのチェック
 *
 * @param array $result 結果
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param string $pluginKey プラグインキー
 * @param int $spaceType スペースタイプ
 * @return void
 */
	private function __assertSpace($result, $spaceId, $roomId, $pluginKey, $spaceType) {
		$this->assertArrayHasKey('Room', Hash::get($result, $spaceId));
		$this->assertEquals($roomId, Hash::get($result, $spaceId . '.Room.id'));
		$this->assertEquals($spaceId, Hash::get($result, $spaceId . '.Room.space_id'));
		$this->assertArrayHasKey('Space', Hash::get($result, $spaceId));
		$this->assertEquals($spaceId, Hash::get($result, $spaceId . '.Space.id'));
		$this->assertEquals($pluginKey, Hash::get($result, $spaceId . '.Space.plugin_key'));
		$this->assertEquals($spaceType, Hash::get($result, $spaceId . '.Space.type'));
		$this->assertArrayHasKey('RoomsLanguage', Hash::get($result, $spaceId));
		$this->assertCount(2, $result[$spaceId]['RoomsLanguage']);
		$this->assertEquals(
			array(
				'id', 'language_id', 'is_origin', 'is_translation', 'is_original_copy', 'room_id', 'name'
			),
			array_keys(Hash::get($result, $spaceId . '.RoomsLanguage.0'))
		);
		$this->assertEquals($roomId, Hash::get($result, $spaceId . '.RoomsLanguage.0.room_id'));
		$this->assertNotEmpty(Hash::get($result, $spaceId . '.RoomsLanguage.0.name'));
		$this->assertEquals(
			array(
				'id', 'language_id', 'is_origin', 'is_translation', 'is_original_copy', 'room_id', 'name'
			),
			array_keys(Hash::get($result, $spaceId . '.RoomsLanguage.1'))
		);
		$this->assertEquals($roomId, Hash::get($result, $spaceId . '.RoomsLanguage.1.room_id'));
		$this->assertNotEmpty(Hash::get($result, $spaceId . '.RoomsLanguage.1.name'));
	}

/**
 * getSpaces()のテスト
 *
 * @return void
 */
	public function testGetSpaces() {
		RoomBehavior::$spaces = null;

		//テスト実施
		$result = $this->TestModel->getSpaces();
		$this->assertEquals(RoomBehavior::$spaces, $result);

		$result = Hash::remove($result, '{n}.{s}.{n}.created');
		$result = Hash::remove($result, '{n}.{s}.{n}.created_user');
		$result = Hash::remove($result, '{n}.{s}.{n}.modified');
		$result = Hash::remove($result, '{n}.{s}.{n}.modified_user');

		//チェック
		$this->assertCount(3, $result);
		$this->__assertSpace($result, '2', '2', 'public_space', Space::PUBLIC_SPACE_ID);
		$this->__assertSpace($result, '3', '3', 'private_space', Space::PRIVATE_SPACE_ID);
		$this->__assertSpace($result, '4', '4', 'community_space', Space::COMMUNITY_SPACE_ID);
	}

/**
 * getSpaces()のテスト
 *
 * @return void
 */
	public function testAcquired() {
		$data = array('Room' => '2');
		RoomBehavior::$spaces = $data;

		//テスト実施
		$result = $this->TestModel->getSpaces();

		//チェック
		$this->assertEquals($data, $result);

		//初期化
		RoomBehavior::$spaces = null;
	}

}
