<?php
/**
 * RoomsHelper::roomName()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');

/**
 * RoomsHelper::roomName()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\View\Helper\RoomsHelper
 */
class RoomsHelperRoomNameTest extends NetCommonsHelperTestCase {

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

		//テストデータ生成
		$viewVars = array();
		$requestData = array();

		//Helperロード
		$this->loadHelper('Rooms.Rooms', $viewVars, $requestData);
	}

/**
 * roomName()のテスト用DataProvider
 *
 * ### 戻り値
 *  - nest ネスト
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			array('nest' => null),
			array('nest' => 0),
			array('nest' => 1),
			array('nest' => 2),
		);
	}

/**
 * roomName()のテスト
 *
 * @param int $nest ネスト
 * @dataProvider dataProvider
 * @return void
 */
	public function testRoomName($nest) {
		//データ生成
		$room = array('RoomsLanguage' => array(0 => array('language_id' => '2', 'name' => 'Room name')));

		//テスト実施
		$result = $this->Rooms->roomName($room, $nest);

		//チェック
		$this->assertEquals(
			str_repeat('<span class="rooms-tree"> </span>', (int)$nest) . 'Room name', $result
		);
	}

}
