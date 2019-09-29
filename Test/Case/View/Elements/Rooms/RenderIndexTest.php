<?php
/**
 * View/Elements/Rooms/render_indexのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('RoomsMockMethods', 'Rooms.TestSuite');

/**
 * View/Elements/Rooms/render_indexのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\View\Elements\Rooms\RenderIndex
 */
class RoomsViewElementsRoomsRenderIndexTest extends NetCommonsControllerTestCase {

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

		// システムプラグインの使用可能なものにroomsプラグインを追加しておく
		$pluginsRole = [
			'id' => 2,
			'role_key' => 'system_administrator',
			'plugin_key' => 'rooms'
		];
		Current::$current['PluginsRole'][] = $pluginsRole;

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Rooms', 'TestRooms');

		//テストコントローラ生成
		$RoomsMockMethods = new RoomsMockMethods();
		$RoomsMockMethods->mockPermissionComponent($this, 'TestRooms.TestViewElementsRoomsRenderIndex');
	}

/**
 * View/Elements/Rooms/render_indexのテスト
 *
 * @return void
 */
	public function testRenderIndex() {
		//テスト実行
		$this->_testGetAction('/test_rooms/test_view_elements_rooms_render_index/render_index/2',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/Rooms/render_index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/' . preg_quote('View/Elements/Rooms/render_index/render_header', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//$pattern = '/' . preg_quote('/rooms/rooms/edit/2/1', '/') . '/';
		//$this->assertRegExp($pattern, $this->view);

		//$pattern = '/' . preg_quote('/rooms/rooms/add/2/1', '/') . '/';
		//$this->assertRegExp($pattern, $this->view);

		$pattern = '/' . preg_quote('View/Elements/Rooms/render_index/render_room_index/2/0', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/' . preg_quote('View/Elements/Rooms/render_index/render_room_index/5/1', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/' . preg_quote('View/Elements/Rooms/render_index/render_room_index/6/1', '/') . '/';
		$this->assertRegExp($pattern, $this->view);
	}

}
