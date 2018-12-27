<?php
/**
 * RoomsController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsControllerTestCase', 'Rooms.TestSuite');

/**
 * RoomsController::index()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Controller\RoomsController
 */
class RoomsControllerIndexTest extends RoomsControllerTestCase {

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
 * index()アクションのテスト
 *
 * @return void
 */
	public function testIndex() {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$this->_testGetAction(array('action' => 'index', '2'), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertTextContains('<ul class="nav nav-tabs" role="tablist">', $this->view);
		//$this->assertTextContains('<article class="rooms-manager" ng-controller="RoomsController" ng-init="initialize(', $this->view);
		$this->assertRegExp('/<article([\s\t\n]+?)class="rooms-manager"([\s\t\n]+?)ng-controller="RoomsController"([\s\t\n]+?)ng-init="initialize.*?/', $this->view);
		$this->assertTextContains('<table class="table">', $this->view);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

}
