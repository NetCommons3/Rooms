<?php
/**
 * RoomsController::add()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * RoomsController::add()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\RoomsController
 */
class RoomsControllerAddTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.pages.languages_page',
		'plugin.rooms.default_role_permission4test',
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
 * add用DataProvider
 *
 * ### 戻り値
 *  - spaceId スペースID
 *  - roomId ルームID
 *  - rootId ルートID
 *  - parentId 親ルームID
 *  - pageId ページID
 *
 * @return array
 */
	public function dataProviderAddGet() {
		$results = array();

		//テストデータ
		$results[0] = array(
			'spaceId' => '2', 'roomId' => '1', 'rootId' => '1', 'parentId' => '1', 'pageId' => '1'
		);
		$results[1] = array(
			'spaceId' => '4', 'roomId' => '6', 'rootId' => '3', 'parentId' => '6', 'pageId' => '5'
		);
		$results[2] = array(
			'spaceId' => '4', 'roomId' => '3', 'rootId' => '3', 'parentId' => '3', 'pageId' => null
		);

		return $results;
	}

/**
 * add()アクションのテスト(GETのテスト)
 *
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param int $rootId ルートID
 * @param int $parentId 親ルームID
 * @param int $pageId ページID
 * @dataProvider dataProviderAddGet
 * @return void
 */
	public function testAddGet($spaceId, $roomId, $rootId, $parentId, $pageId) {
		//テスト実行
		$this->_testGetAction(array('action' => 'add', $spaceId, $roomId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->__assetAdd($spaceId, $roomId, $rootId, $parentId, $pageId);
	}

/**
 * add()アクションのチェック
 *
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param int $rootId ルートID
 * @param int $parentId 親ルームID
 * @param int $pageId ページID
 * @return void
 */
	private function __assetAdd($spaceId, $roomId, $rootId, $parentId, $pageId) {
		$this->assertEqual(null, $this->controller->RoomsRolesForm->settings['room_id']);
		$this->assertEqual('room_role', $this->controller->RoomsRolesForm->settings['type']);

		$data = $this->__data($spaceId, '', $rootId, $parentId, $pageId, '');
		if ($spaceId === '4') {
			$data = Hash::insert($data, 'Room.default_participation', false);
			$data = Hash::insert($data, 'Room.default_role_key', 'general_user');
		}
		$this->__assetRequestData($data, 'Room.id');
		$this->__assetRequestData($data, 'Room.space_id');
		$this->__assetRequestData($data, 'Room.root_id');
		$this->__assetRequestData($data, 'Room.parent_id');
		$this->__assetRequestData($data, 'Room.default_participation');
		$this->__assetRequestData($data, 'Room.default_role_key');
		$this->__assetRequestData($data, 'Room.need_approval');
		$this->__assetRequestData($data, 'Room.active');
		$this->__assetRequestData($data, 'Page.parent_id');
		$this->__assetRequestData($data, 'RoomsLanguage.0.id');
		$this->__assetRequestData($data, 'RoomsLanguage.0.room_id');
		$this->__assetRequestData($data, 'RoomsLanguage.0.language_id');
		$this->__assetRequestData($data, 'RoomsLanguage.0.name');
		$this->__assetRequestData($data, 'RoomsLanguage.1.id');
		$this->__assetRequestData($data, 'RoomsLanguage.1.room_id');
		$this->__assetRequestData($data, 'RoomsLanguage.1.language_id');
		$this->__assetRequestData($data, 'RoomsLanguage.1.name');
		$this->__assetRequestData($data, 'RoomRolePermission.content_publishable.room_administrator.id');
		$this->__assetRequestData($data, 'RoomRolePermission.content_publishable.chief_editor.id');
		$this->__assetRequestData($data, 'RoomRolePermission.content_publishable.chief_editor.value');
		$this->__assetRequestData($data, 'RoomRolePermission.content_publishable.editor.id');
		$this->__assetRequestData($data, 'RoomRolePermission.content_publishable.editor.value');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.room_administrator.id');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.room_administrator.value');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.chief_editor.id');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.chief_editor.value');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.editor.id');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.editor.value');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.general_user.id');
		$this->__assetRequestData($data, 'RoomRolePermission.html_not_limited.general_user.value');

		$pattern = '/<form action=".*?' . preg_quote('/rooms/rooms/add/' . $spaceId . '/' . $roomId, '/') . '"/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertInput('input', '_method', 'POST', $this->view);
		$this->assertInput('input', 'data[Room][id]', null, $this->view);
		$this->assertInput('input', 'data[Room][space_id]', $spaceId, $this->view);
		$this->assertInput('input', 'data[Room][root_id]', $rootId, $this->view);
		$this->assertInput('input', 'data[Room][parent_id]', $parentId, $this->view);
		$this->assertInput('input', 'data[Page][parent_id]', $pageId, $this->view);

		$this->assertInput('input', 'data[RoomsLanguage][0][name]', null, $this->view);

		$pattern = '/<button type="button".*?".*?onclick=".*?' . preg_quote('/rooms/rooms/index/' . $spaceId, '/') . '\'" name="cancel">/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/<button name="save"/';
		$this->assertRegExp($pattern, $this->view);
	}

/**
 * request->dataのチェック
 *
 * @param array $data データ
 * @param string $keyPath Hashのキー
 * @return void
 */
	private function __assetRequestData($data, $keyPath) {
		if (preg_match('/^RoomRolePermission\..+?\..+?\.value$/', $keyPath)) {
			if (Hash::get($data, $keyPath) === '0') {
				$expected = false;
			} else {
				$expected = true;
			}
		} else {
			$expected = Hash::get($data, $keyPath);
		}
		$this->assertEquals($expected, Hash::get($this->controller->request->data, $keyPath));
	}

/**
 * リクエストデータ作成
 *
 * @param int $spaceId スペースID
 * @param int $roomId ルームID
 * @param int $rootId ルートID
 * @param int $parentId 親ルームID
 * @param int $pageId ページID
 * @param string $name ルーム名
 * @return array リクエストデータ
 */
	private function __data($spaceId, $roomId, $rootId, $parentId, $pageId, $name) {
		$data = array(
			'Room' => array(
				'id' => $roomId,
				'space_id' => $spaceId,
				'root_id' => $rootId,
				'parent_id' => $parentId,
				'default_participation' => '1',
				'default_role_key' => 'visitor',
				'need_approval' => '1',
				'active' => '1',
			),
			'Page' => array(
				'parent_id' => $pageId
			),
			'RoomsLanguage' => array(
				0 => array(
					'id' => '',
					'room_id' => $roomId,
					'language_id' => '1',
					'name' => $name
				),
				1 => array(
					'id' => '',
					'room_id' => $roomId,
					'language_id' => '2',
					'name' => $name
				),
			),
			'RoomRolePermission' => array(
				'content_publishable' => array(
					'room_administrator' => array(
						'id' => ''
					),
					'chief_editor' => array(
						'id' => '',
						'value' => '1'
					),
					'editor' => array(
						'id' => '',
						'value' => '0'
					),
				),
				'html_not_limited' => array(
					'room_administrator' => array(
						'id' => '',
						'value' => '0'
					),
					'chief_editor' => array(
						'id' => '',
						'value' => '0'
					),
					'editor' => array(
						'id' => '',
						'value' => '0'
					),
					'general_user' => array(
						'id' => '',
						'value' => '0'
					),
				),
			),
		);
		return $data;
	}

/**
 * add()アクションのテスト(POSTのテスト)
 *
 * @return void
 */
	public function testAddPost() {
		//テスト実行
		$data = $this->__data('2', '', '1', '1', '1', 'Test room');
		$this->_testPostAction('post', $data, array('action' => 'add', '2', '1'), null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$pattern = '/' . preg_quote('/rooms/rooms_roles_users/edit/2/9', '/') . '/';
		$this->assertRegExp($pattern, $header['Location']);
	}

/**
 * add()アクションのValidationErrorテスト(POSTのテスト)
 *
 * @return void
 */
	public function testAddPostValidationError() {
		//テスト実行
		$data = $this->__data('2', '', '1', '1', '1', '');
		$this->_testPostAction('post', $data, array('action' => 'add', '2', '1'), null, 'view');

		//チェック
		$this->__assetAdd('2', '1', '1', '1', '1');

		$pattern = '<div class="help-block">' .
						sprintf(__d('net_commons', 'Please input %s.'), __d('rooms', 'Room name')) .
					'</div>';
		$this->assertTextContains($pattern, $this->view);
	}

}