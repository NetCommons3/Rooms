<?php
/**
 * View/Elements/Rooms/render_room_indexのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * View/Elements/Rooms/render_room_indexのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\View\Elements\Rooms\RenderRoomIndex
 */
class RoomsViewElementsRoomsRenderRoomIndexTest extends NetCommonsControllerTestCase {

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
		$this->generateNc('TestRooms.TestViewElementsRoomsRenderRoomIndex');
	}

/**
 * View/Elements/Rooms/render_room_indexのテスト
 *
 * @return void
 */
	public function testRenderRoomIndex() {
		//テスト実行
		$this->_testGetAction('/test_rooms/test_view_elements_rooms_render_room_index/render_room_index/2',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/Rooms/render_room_index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/<a href=".*?' . preg_quote('/rooms/rooms/edit/2/5', '/') . '.*?">' .
				preg_quote('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', '/') . '.+?' . preg_quote('</a>', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertTextContains('/rooms/rooms/active/2/5', $this->view);
	}

/**
 * View/Elements/Rooms/render_room_indexのテスト(Rootのルーム)
 *
 * @return void
 */
	public function testRenderRoomIndexRoot() {
		//テスト実行
		$this->_testGetAction('/test_rooms/test_view_elements_rooms_render_room_index/render_room_index_root/2',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/Rooms/render_room_index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/<a href=".*?' . preg_quote('/rooms/rooms/edit/2/2', '/') . '.*?">' .
				preg_quote('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', '/') . '.+?' . preg_quote('</a>', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertTextContains('/rooms/rooms/active', $this->view);

		$pattern = '/<a href=".*?' . preg_quote('/rooms/room_add/basic/2/2', '/') . '.*?">' .
				preg_quote('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', '/') .
				' <span class="hidden-xs">' . __d('rooms', 'Sub room') . preg_quote('</span></a>', '/') . '/';
		$this->assertRegExp($pattern, $this->view);
	}

}
