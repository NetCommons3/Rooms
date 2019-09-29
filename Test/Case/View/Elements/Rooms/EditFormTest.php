<?php
/**
 * View/Elements/Rooms/edit_formのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * View/Elements/Rooms/edit_formのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\View\Elements\Rooms\EditForm
 */
class RoomsViewElementsRoomsEditFormTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.pages.pages_language',
		'plugin.roles.default_role_permission4test',
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
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Rooms', 'TestRooms');
		//テストコントローラ生成
		$this->generateNc('TestRooms.TestViewElementsRoomsEditForm');
		//ログイン
		TestAuthGeneral::login($this);
	}

/**
 * View/Elements/Rooms/edit_formのテスト(Room.active=false, viewVars[participationFixed]=false)
 *
 * @return void
 */
	public function testEditForm() {
		//テスト実行
		$roomId = '7';
		$this->_testGetAction('/test_rooms/test_view_elements_rooms_edit_form/edit_form/' . $roomId,
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/Rooms/edit_form', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->__assertEditForm($roomId);
		$this->assertInput('input', 'data[RoomsLanguage][0][id]', '12', $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][1][id]', '11', $this->view);
		$this->assertInput('option', 'general_user', 'selected', $this->view);
		$this->assertInput('option', 'visitor', '', $this->view);
		$this->assertInput('option', '0', 'selected', $this->view);
		$this->assertInput('option', '1', '', $this->view);
	}

/**
 * View/Elements/Rooms/edit_formのテスト(Room.active=true, viewVars[participationFixed]=true)
 *
 * @return void
 */
	public function testEditFormWithActiveAndDefaultParticipation() {
		//テスト実行
		$roomId = '5';
		$this->_testGetAction('/test_rooms/test_view_elements_rooms_edit_form/edit_form/' . $roomId,
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/Rooms/edit_form', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->__assertEditForm($roomId);
		$this->assertInput('input', 'data[RoomsLanguage][0][id]', '8', $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][1][id]', '7', $this->view);
		$this->assertInput('option', 'general_user', '', $this->view);
		$this->assertInput('option', 'visitor', 'selected', $this->view);
		$this->assertInput('option', '0', '', $this->view);
		$this->assertInput('option', '1', 'selected', $this->view);
	}

/**
 * View/Elements/Rooms/edit_formのテスト
 *
 * @param int $roomId ルームID
 * @return void
 */
	private function __assertEditForm($roomId) {
		$this->assertInput('input', 'data[RoomsLanguage][0][room_id]', $roomId, $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][0][language_id]', '2', $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][0][name]', null, $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][1][room_id]', $roomId, $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][1][language_id]', '1', $this->view);
		$this->assertInput('input', 'data[RoomsLanguage][1][name]', null, $this->view);
		$this->assertInput('input', 'data[Room][default_participation]', '0', $this->view);
		$this->assertInput('input', 'data[Room][default_participation]', '1', $this->view);
		$this->assertInput('select', 'data[Room][default_role_key]', '', $this->view);
		$this->assertInput('option', 'room_administrator', '', $this->view);
		$this->assertInput('option', 'chief_editor', '', $this->view);
		$this->assertInput('option', 'editor', '', $this->view);
		$this->assertInput('input', 'data[Room][need_approval]', 'checked', $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][content_publishable][room_administrator][id]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][content_publishable][room_administrator][value]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][content_publishable][chief_editor][id]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][content_publishable][chief_editor][value]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][content_publishable][editor][id]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][content_publishable][editor][value]', null, $this->view);
		$this->assertInput('input', 'data[Room][need_approval]', '0', $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][room_administrator][id]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][room_administrator][value]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][chief_editor][id]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][chief_editor][value]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][editor][id]', null, $this->view);
		$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][editor][value]', null, $this->view);
		//$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][general_user][id]', null, $this->view);
		//$this->assertInput('input', 'data[RoomRolePermission][html_not_limited][general_user][value]', null, $this->view);
		$this->assertInput('select', 'data[Room][active]', null, $this->view);
	}

}
