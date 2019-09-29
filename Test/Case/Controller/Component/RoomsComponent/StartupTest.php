<?php
/**
 * RoomsComponent::startup()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('RoomBehavior', 'Rooms.Model/Behavior');

/**
 * RoomsComponent::startup()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\Component\RoomsComponent
 */
class RoomsComponentStartupTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
		RoomBehavior::$spaces = array();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
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
 * startup()のテスト
 *
 * @return void
 */
	public function testStartup() {
		//テストコントローラ生成
		$this->generateNc('TestRooms.TestRoomsComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$this->_testGetAction('/test_rooms/test_rooms_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('Controller/Component/TestRoomsComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		// * viewVars['spaces']のチェック
		$this->vars['spaces'] = Hash::remove($this->vars['spaces'], '{n}.{s}.{n}.created');
		$this->vars['spaces'] = Hash::remove($this->vars['spaces'], '{n}.{s}.{n}.created_user');
		$this->vars['spaces'] = Hash::remove($this->vars['spaces'], '{n}.{s}.{n}.modified');
		$this->vars['spaces'] = Hash::remove($this->vars['spaces'], '{n}.{s}.{n}.modified_user');

		$this->assertCount(3, $this->vars['spaces']);
		$this->__assertSpace($this->vars['spaces'], '2', '2', 'public_space', Space::PUBLIC_SPACE_ID);
		$this->__assertSpace($this->vars['spaces'], '3', '3', 'private_space', Space::PRIVATE_SPACE_ID);
		$this->__assertSpace($this->vars['spaces'], '4', '4', 'community_space', Space::COMMUNITY_SPACE_ID);

		// * Roomsヘルパーのチェック
		$this->assertTrue(in_array('Rooms.Rooms', $this->controller->helpers, true));

		// * viewVars['defaultRoleOptions']のチェック
		$expected = array(
			'room_administrator' => 'Room Manager',
			'chief_editor' => 'Chief editor',
			'editor' => 'Editor',
			'general_user' => 'General user',
			'visitor' => 'Visitor'
		);
		$this->assertEquals($expected, $this->vars['defaultRoleOptions']);
	}

}
