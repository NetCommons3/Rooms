<?php
/**
 * RoomsController::active()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsControllerTestCase', 'Rooms.TestSuite');

/**
 * RoomsController::active()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\RoomsController
 */
class RoomsControllerActiveTest extends RoomsControllerTestCase {

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
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'rooms';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		//ログイン
		TestAuthGeneral::login($this);
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
 * active()アクションのテスト(GETのテスト)
 *
 * @return void
 */
	public function testActiveGet() {
		//テスト実行
		$this->_testGetAction(array('action' => 'active', '2', '5'), null, 'BadRequestException', 'view');
	}

/**
 * active()アクションのテスト(POSTのテスト)
 *
 * @return void
 */
	public function testActivePost() {
		$this->_mockRoomsComponent();
		$this->_mockForReturnTrue('Rooms.Room', 'saveActive');

		//テスト実行
		$data = array('Room' => array('id' => '5'));
		$this->_testPostAction('put', $data, array('action' => 'active', '2', '5'), null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$pattern = '/' . preg_quote('/rooms/rooms/index/2', '/') . '/';
		$this->assertRegExp($pattern, $header['Location']);
	}

/**
 * active()アクションのテスト(POSTのテスト、Saveエラー)
 *
 * @return void
 */
	public function testActivePostSaveError() {
		$this->_mockRoomsComponent();
		$this->_mockForReturnFalse('Rooms.Room', 'saveActive');

		//テスト実行
		$data = array('Room' => array('id' => '5'));
		$this->_testPostAction('put', $data, array('action' => 'active', '2', '5'), 'BadRequestException', 'view');
	}

}
