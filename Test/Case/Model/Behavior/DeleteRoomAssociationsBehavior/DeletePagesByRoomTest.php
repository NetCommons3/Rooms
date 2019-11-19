<?php
/**
 * DeleteRoomAssociationsBehavior::deletePagesByRoom()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * DeleteRoomAssociationsBehavior::deletePagesByRoom()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\DeleteRoomAssociationsBehavior
 */
class DeleteRoomAssociationsBehaviorDeletePagesByRoomTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.page4delete',
		'plugin.rooms.delete_test_page_id',
		'plugin.rooms.room_delete_related_table',
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
		$this->TestModel = ClassRegistry::init('TestRooms.TestDeleteRoomAssociationsBehaviorModel');
		$this->Page = ClassRegistry::init('Pages.Page');
	}

/**
 * deletePagesByRoom()テストのDataProvider
 *
 * ### 戻り値
 *  - roomId ルームID
 *
 * @return array データ
 */
	public function dataProvider() {
		$result[0] = array();
		$result[0]['roomId'] = '5';

		return $result;
	}

/**
 * deletePagesByRoom()のテスト
 *
 * @param int $roomId ルームID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeletePagesByRoom($roomId) {
		//事前チェック
		$this->__assertTable('Page', 3);

		//テスト実施
		$result = $this->TestModel->deletePagesByRoom($roomId);
		$this->assertTrue($result);

		//チェック
		$this->__assertTable('Page', 1);
	}

/**
 * deletePagesByRoom()のExceptionErrorテスト
 *
 * @param int $roomId ルームID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeletePagesByRoomOnExceptionError($roomId) {
		$this->_mockForReturnFalse('TestModel', 'Pages.Page', 'delete');

		//事前チェック
		$this->__assertTable('Page', 3);

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->TestModel->deletePagesByRoom($roomId);
	}
/**
 * テーブルのチェック
 *
 * @param string $model Model名
 * @param int $count データ件数
 * @return void
 */
	private function __assertTable($model, $count) {
		$result = $this->$model->find('count', array(
			'recursive' => -1,
		));
		$this->assertEquals($count, $result);
	}

}
