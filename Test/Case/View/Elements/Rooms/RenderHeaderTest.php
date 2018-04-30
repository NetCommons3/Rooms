<?php
/**
 * View/Elements/Rooms/render_headerのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * View/Elements/Rooms/render_headerのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\View\Elements\Rooms\RenderHeader
 */
class RoomsViewElementsRoomsRenderHeaderTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

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
		//テストコントローラ生成
		$this->generateNc('TestRooms.TestViewElementsRoomsRenderHeader');
	}

/**
 * View/Elements/Rooms/render_headerのテスト
 *
 * @return void
 */
	public function testRenderHeader() {
		//テスト実行
		$this->_testGetAction('/test_rooms/test_view_elements_rooms_render_header/render_header',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/Rooms/render_header', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertTextContains(__d('rooms', 'Room name'), $this->view);
		$this->assertTextContains(__d('rooms', 'Created user'), $this->view);
		//$this->assertTextContains(__d('rooms', 'Members'), $this->view);
	}

}
