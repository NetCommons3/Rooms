<?php
/**
 * PluginsRoomsController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * PluginsRoomsController::edit()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\PluginsRoomsController
 */
class PluginsRoomsControllerEditTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.pages.languages_page',
		'plugin.rooms.default_role_permission4test',
		'plugin.rooms.page4test',
		'plugin.rooms.plugins_room4test',
		'plugin.rooms.plugin4test',
		'plugin.rooms.plugins_role4test',
		'plugin.rooms.roles_room4test',
		'plugin.rooms.roles_rooms_user4test',
		'plugin.rooms.room4test',
		'plugin.rooms.room_role',
		'plugin.rooms.room_role_permission4test',
		'plugin.rooms.rooms_language4test',
		'plugin.rooms.space',
		'plugin.user_roles.user_role_setting',
	);

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
	protected $_controller = 'plugins_rooms';

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
 * edit()アクションのテスト
 *
 * @return void
 */
	public function testEditGet() {
		//テストデータ
		$spaceId = '2';
		$roomId = '4';

		//テスト実行
		$this->_testGetAction(array('action' => 'edit', $spaceId, $roomId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertInput('form', null, '/rooms/plugins_rooms/edit/' . $spaceId . '/' . $roomId, $this->view);
		$this->assertInput('input', '_method', 'PUT', $this->view);
		$this->assertInput('input', 'data[Room][id]', $roomId, $this->view);

		$this->assertInput('input', 'data[Plugin][0][key]', 'tests', $this->view);
		$this->assertInput('input', 'data[Plugin][1][key]', 'test2s', $this->view);
		$this->assertInput('input', 'data[PluginsRoom][plugin_key]', '', $this->view);

		$pattern = '<input type="checkbox" name="data[PluginsRoom][plugin_key][]" checked="checked" value="tests"';
		$this->assertTextContains($pattern, $this->view);

		$pattern = '<input type="checkbox" name="data[PluginsRoom][plugin_key][]" value="test2s"';
		$this->assertTextContains($pattern, $this->view);
	}

/**
 * リクエストデータ作成
 *
 * @return array リクエストデータ
 */
	private function __data() {
		$data = array(
			'Room' => array(
				'id' => '4',
			),
			'Plugin' => array(
				0 => array('key' => 'tests'),
				1 => array('key' => 'test2s'),
			),
			'PluginsRoom' => array(
				'plugin_key' => array('tests'),
			),
		);
		return $data;
	}

/**
 * edit()アクションのテスト(POSTのテスト)
 *
 * @return void
 */
	public function testEditPost() {
		//テスト実行
		$data = $this->__data();
		$this->_testPostAction('put', $data, array('action' => 'edit', '2', '4'), null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$pattern = '/' . preg_quote('/rooms/rooms/index/2', '/') . '/';
		$this->assertRegExp($pattern, $header['Location']);
	}

/**
 * edit()アクションのValidationErrorテスト(POSTのテスト)
 *
 * @return void
 */
	public function testEditPostValidationError() {
		$this->_mockForReturnFalse('PluginManager.PluginsRoom', 'savePluginsRoomsByRoomId');

		//テスト実行
		$data = $this->__data();
		$this->_testPostAction('put', $data, array('action' => 'edit', '2', '1'), 'BadRequestException', 'view');
	}

}