<?php
/**
 * RoomsController::delete()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsControllerTestCase', 'Rooms.TestSuite');

/**
 * RoomsController::delete()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\RoomsController
 */
class RoomsControllerDeleteTest extends RoomsControllerTestCase {

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
 * delete()アクションのテスト(GETのテスト)
 *
 * @return void
 */
	public function testDeleteGet() {
		//テスト実行
		$this->_testGetAction(array('action' => 'delete', '2', '5'), null, 'BadRequestException', 'view');
	}

/**
 * delete()アクションのテスト(POSTのテスト)
 *
 * @return void
 */
	public function testDeletePost() {
		$this->_mockRoomsComponent();
		$this->_mockForReturnTrue('Rooms.Room', 'deleteRoom');

		//テスト実行
		$data = array('Room' => array('id' => '5'));
		$this->_testPostAction('delete', $data, array('action' => 'delete', '2', '5'), null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$pattern = '/' . preg_quote('/rooms/rooms/index/2', '/') . '/';
		$this->assertRegExp($pattern, $header['Location']);
	}

/**
 * delete()アクションのテスト(POSTのテスト、Deleteエラー)
 *
 * @return void
 */
	public function testDeletePostSaveError() {
		$this->_mockRoomsComponent();
		$this->_mockForReturnFalse('Rooms.Room', 'deleteRoom');

		//テスト実行
		$data = array('Room' => array('id' => '5'));
		$this->_testPostAction('delete', $data, array('action' => 'delete', '2', '5'), 'BadRequestException', 'view');
	}

}
