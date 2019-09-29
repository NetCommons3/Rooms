<?php
/**
 * RoomsRolesUsersController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsControllerTestCase', 'Rooms.TestSuite');

/**
 * RoomsRolesUsersController::edit()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\RoomsRolesUsersController
 */
class RoomsRolesUsersControllerEditTest extends RoomsControllerTestCase {

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
	protected $_controller = 'rooms_roles_users';

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
 * edit()アクションのGETリクエストテスト
 *
 * @return void
 */
	public function testEditGet() {
		//テストデータ
		$spaceId = '2';
		$roomId = '5';

		//テスト実行
		$this->_testGetAction(array('action' => 'edit', $spaceId, $roomId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertInput('form', null, '/rooms/rooms_roles_users/edit/' . $spaceId . '/' . $roomId, $this->view);
		$this->__assetEditGet($spaceId, $roomId);
		$this->__assetEditGetUser($roomId, '1', '6');
	}

/**
 * edit()アクションのGETリクエストテスト(クエリあり)
 *
 * @return void
 */
	public function testEditGetWithQuery() {
		//テストデータ
		$spaceId = '2';
		$roomId = '5';

		//テスト実行
		$this->_testGetAction(
			array('action' => 'edit', $spaceId, $roomId, '?' => array('search' => '1')),
			array('method' => 'assertNotEmpty'), null, 'view'
		);

		//チェック
		$this->assertInput('form', null, '/rooms/rooms_roles_users/edit/' . $spaceId . '/' . $roomId . '?search=1', $this->view);
		$this->__assetEditGet($spaceId, $roomId);
		$this->__assetEditGetUser($roomId, '1', '6');
		$this->__assetEditGetUser($roomId, '2', null);
		$this->__assetEditGetUser($roomId, '3', null);
	}

/**
 * edit()アクションのチェック
 *
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @return void
 */
	private function __assetEditGet($spaceId, $roomId) {
		$this->assertInput('input', '_method', 'PUT', $this->view);
		$this->assertInput('input', 'data[Room][id]', $roomId, $this->view);

		$this->assertTextContains('<button name="search"', $this->view);
		$this->assertInput('select', 'data[Role][key]', '', $this->view);
		$this->assertTextContains('/rooms/rooms_roles_users/edit/' . $spaceId . '/' . $roomId . '/sort:room_role_level/direction:asc', $this->view);
	}

/**
 * edit()アクションのチェック(user)
 *
 * @param int $roomId ルームID
 * @param int $userId ユーザID
 * @param int $rolesRoomUserId ロールルームID
 * @return void
 */
	private function __assetEditGetUser($roomId, $userId, $rolesRoomUserId) {
		$this->assertInput('input', 'data[RolesRoomsUser][' . $userId . '][id]', $rolesRoomUserId, $this->view);
		$this->assertInput('input', 'data[RolesRoomsUser][' . $userId . '][user_id]', $userId, $this->view);
		$this->assertInput('input', 'data[RolesRoomsUser][' . $userId . '][room_id]', $roomId, $this->view);
		$this->assertInput('select', 'data[RolesRoom][' . $userId . '][role_key]', '', $this->view);

		$name = 'data[User][id][' . $userId . ']';
		$domId = 'UserId' . $userId;
		$this->assertTextContains('name="' . $name . '" id="' . $domId . '_" value="0"', $this->view);
		$pattern = '<input type="checkbox" name="' . preg_quote($name, '/') . '".+?value="1" id="' . $domId . '"';
		$this->assertRegExp('/' . $pattern . '/', $this->view);
	}

/**
 * リクエストデータ作成
 *
 * @return array リクエストデータ
 */
	private function __dataSave() {
		$mock = array('components' => array(
			'Security',
		));
		$this->generate('Rooms.' . Inflector::camelize($this->_controller), $mock);

		$this->controller->Components->Session->write('RoomsRolesUsers',
			array(
				'1' => array('id' => '6', 'user_id' => '1', 'room_id' => '5', 'roles_room_id' => '4', 'role_key' => 'room_administrator'),
				'2' => array('id' => null, 'user_id' => '2', 'room_id' => '5', 'roles_room_id' => '4', 'role_key' => 'room_administrator'),
				'3' => array('id' => null, 'user_id' => '3', 'room_id' => '5', 'roles_room_id' => '4', 'role_key' => 'room_administrator'),
				'4' => array('id' => null, 'user_id' => '4', 'room_id' => '5', 'roles_room_id' => '4', 'role_key' => 'room_administrator'),
			)
		);

		$data = array(
			'Room' => array('id' => '5'),
			'save' => null,
		);
		return $data;
	}

/**
 * edit()アクションのテスト(POSTのテスト)
 *
 * @return void
 */
	public function testEditPost() {
		//テストデータ
		$spaceId = '2';
		$roomId = '5';

		//テスト実行
		$data = $this->__dataSave();
		$this->_testPostAction('put', $data,
				array('action' => 'edit', $spaceId, $roomId, '?' => array('search' => '1')), null, 'view');

		$header = $this->controller->response->header();
		$pattern = '/' . preg_quote('/rooms/rooms/index/2', '/') . '/';
		$this->assertRegExp($pattern, $header['Location']);
	}

/**
 * edit()アクションのExpectionErrorテスト(POSTのテスト)
 *
 * @return void
 */
	public function testEditPostOnExpectionError() {
		//テストデータ
		$spaceId = '2';
		$roomId = '5';

		//テスト実行
		$data = $this->__dataSave();

		$this->_mockRoomsComponent();
		$this->_mockForReturnFalse('Rooms.RolesRoomsUser', 'saveRolesRoomsUsersForRooms');
		$this->_testPostAction('put', $data,
				array('action' => 'edit', $spaceId, $roomId, '?' => array('search' => '1')), null, 'view');

		//チェック
		$this->assertInput('form', null, '/rooms/rooms_roles_users/edit/' . $spaceId . '/' . $roomId . '?search=1', $this->view);
		$this->__assetEditGet($spaceId, $roomId);
		$this->__assetEditGetUser($roomId, '1', '6');
		$this->__assetEditGetUser($roomId, '2', null);
		$this->__assetEditGetUser($roomId, '3', null);
	}

/**
 * リクエストデータ作成
 *
 * @return array リクエストデータ
 */
	private function __dataDeletePublic() {
		$data = array(
			'Room' => array('id' => '5'),
			'Role' => array('key' => 'delete'),
			'User' => array('id' => array('1' => '1', '2' => '0', '3' => '0', '4' => '0')),
			'RolesRoomsUser' => array(
				'1' => array('id' => '6', 'user_id' => '1', 'room_id' => '5'),
				'2' => array('id' => null, 'user_id' => '2', 'room_id' => '5'),
				'3' => array('id' => null, 'user_id' => '3', 'room_id' => '5'),
				'4' => array('id' => null, 'user_id' => '4', 'room_id' => '5'),
			),
		);
		return $data;
	}

/**
 * edit()アクションのテスト(POST(delete)のテスト)
 *
 * @return void
 */
	public function testEditDeletePublic() {
		//テストデータ
		$spaceId = '2';
		$roomId = '5';

		//テスト実行
		$data = $this->__dataDeletePublic();
		$this->_testPostAction('put', $data,
				array('action' => 'edit', $spaceId, $roomId, '?' => array('search' => '1')), 'BadRequestException', 'view');
	}

/**
 * リクエストデータ作成
 *
 * @return array リクエストデータ
 */
	private function __dataDeleteCommunity() {
		$data = array(
			'Room' => array('id' => '7'),
			'Role' => array('key' => 'delete'),
			'User' => array('id' => array('1' => '1', '2' => '0', '3' => '0', '4' => '0')),
			'RolesRoomsUser' => array(
				'1' => array('id' => '8', 'user_id' => '1', 'room_id' => '7'),
				'2' => array('id' => null, 'user_id' => '2', 'room_id' => '7'),
				'3' => array('id' => null, 'user_id' => '3', 'room_id' => '7'),
				'4' => array('id' => null, 'user_id' => '4', 'room_id' => '7'),
			),
		);
		return $data;
	}

/**
 * edit()アクションのテスト(POST(delete)のテスト)
 *
 * @return void
 */
	public function testEditDeleteCommunity() {
		//テストデータ
		$spaceId = '3';
		$roomId = '7';

		//テスト実行
		$data = $this->__dataDeleteCommunity();
		$this->_testPostAction('put', $data,
				array('action' => 'edit', $spaceId, $roomId, '?' => array('search' => '1')), null, 'view');

		//チェック
		$this->assertInput('form', null, '/rooms/rooms_roles_users/edit/' . $spaceId . '/' . $roomId . '?search=1', $this->view);
		$this->__assetEditGet($spaceId, $roomId);
		$this->__assetEditGetUser($roomId, '1', null);
		$this->__assetEditGetUser($roomId, '2', null);
		$this->__assetEditGetUser($roomId, '3', null);
	}
}
