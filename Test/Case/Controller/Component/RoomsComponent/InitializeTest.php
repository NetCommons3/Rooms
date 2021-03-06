<?php
/**
 * RoomsComponent::initialize()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * RoomsComponent::initialize()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\Component\RoomsComponent
 */
class RoomsComponentInitializeTest extends NetCommonsControllerTestCase {

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
 * initialize()のテスト
 *
 * @return void
 */
	public function testInitialize() {
		//テストコントローラ生成
		$this->generateNc('TestRooms.TestRoomsComponent');

		//ログイン
		TestAuthGeneral::login($this);

		//事前チェック
		$this->assertEmpty($this->controller->Rooms->controller);

		//テスト実行
		$this->_testGetAction('/test_rooms/test_rooms_component/index',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('Controller/Component/TestRoomsComponent', '/') . '/';
		$this->assertRegExp($pattern, $this->view);
		$this->assertNotEmpty(get_class($this->controller->Rooms->controller));
		$this->assertNotEmpty(get_class($this->controller->Rooms->controller->Paginator));
		$this->assertNotEmpty(get_class($this->controller->Rooms->controller->Room));
		$this->assertNotEmpty(get_class($this->controller->Rooms->controller->Role));
	}

}
