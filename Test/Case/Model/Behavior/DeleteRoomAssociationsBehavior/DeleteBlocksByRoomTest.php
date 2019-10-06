<?php
/**
 * DeleteRoomAssociationsBehavior::deleteBlocksByRoom()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * DeleteRoomAssociationsBehavior::deleteBlocksByRoom()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\DeleteRoomAssociationsBehavior
 */
class DeleteRoomAssociationsBehaviorDeleteBlocksByRoomTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.block4delete',
		'plugin.rooms.blocks_language4delete',
		'plugin.rooms.delete_test_block_id',
		'plugin.rooms.delete_test_block_key',
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
		$this->Block = ClassRegistry::init('Blocks.Block');
	}

/**
 * deleteBlocksByRoom()テストのDataProvider
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
 * deleteBlocksByRoom()のテスト
 *
 * @param int $roomId ルームID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeleteBlocksByRoom($roomId) {
		//事前チェック
		$this->__assertTable('Block', 3);

		//テスト実施
		$result = $this->TestModel->deleteBlocksByRoom($roomId);
		$this->assertTrue($result);

		//チェック
		$this->__assertTable('Block', 1);
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
