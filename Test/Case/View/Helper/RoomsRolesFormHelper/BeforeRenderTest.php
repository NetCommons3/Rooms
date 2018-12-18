<?php
/**
 * RoomsRolesFormHelper::beforeRender()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * RoomsRolesFormHelper::beforeRender()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\Component\RoomsRolesFormHelper
 */
class RoomsRolesFormHelperBeforeRenderTest extends NetCommonsControllerTestCase {

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
 * beforeRender()のテスト
 *
 * @return void
 */
	public function testBeforeRender() {
		//テストコントローラ生成
		$this->generateNc('TestRooms.TestRoomsRolesFormHelperBeforeRender');

		//テスト実行
		$this->_testGetAction('/test_rooms/test_rooms_roles_form_helper_before_render/index',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Helper/TestRoomsRolesFormHelperBeforeRender', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		//scriptのURLチェック
		// 2018.12 JSファイルの置き場所がwebrootの下になった影響に対応
		$pattern = '/<script.*?' . preg_quote('/js/rooms/role_permissions.js', '/') . '.*?>/';
		$this->assertRegExp($pattern, $this->contents);

		// 2018.12 JSファイルの置き場所がwebrootの下になった影響に対応
		$pattern = '/<script.*?' . preg_quote('/js/rooms/room_role_permissions.js', '/') . '.*?>/';
		$this->assertRegExp($pattern, $this->contents);
	}

}
