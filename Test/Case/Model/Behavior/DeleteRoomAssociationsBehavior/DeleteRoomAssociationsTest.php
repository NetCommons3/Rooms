<?php
/**
 * DeleteRoomAssociationsBehavior::deleteRoomAssociations()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * DeleteRoomAssociationsBehavior::deleteRoomAssociations()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\DeleteRoomAssociationsBehavior
 */
class DeleteRoomAssociationsBehaviorDeleteRoomAssociationsTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.plugins_room4test',
		'plugin.rooms.roles_room4test',
		'plugin.rooms.room_role_permission4test',
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
		$this->TestModel->RoomRolePermission = ClassRegistry::init('Rooms.RoomRolePermission');
		$this->TestModel->PluginsRoom = ClassRegistry::init('PluginManager.PluginsRoom');
	}

/**
 * deleteRoomAssociations()テストのDataProvider
 *
 * ### 戻り値
 *  - roomId ルームID
 *
 * @return array データ
 */
	public function dataProvider() {
		$result[0] = array();
		$result[0]['roomId'] = '4';

		return $result;
	}

/**
 * deleteRoomAssociations()のテスト
 *
 * @param int $roomId ルームID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeleteRoomAssociations($roomId) {
		//事前チェック
		$this->__assertTable('RoomRolePermission', 15, array('id', 'roles_room_id'));
		$this->__assertTable('PluginsRoom', 3, array('id', 'room_id'));

		//テスト実施
		$result = $this->TestModel->deleteRoomAssociations($roomId);
		$this->assertTrue($result);

		//チェック
		$this->__assertTable('RoomRolePermission', 5, array('id', 'roles_room_id'), array(
			array('RoomRolePermission' => array('id' => '7', 'roles_room_id' => '1')),
			array('RoomRolePermission' => array('id' => '18', 'roles_room_id' => '2')),
			array('RoomRolePermission' => array('id' => '29', 'roles_room_id' => '3')),
			array('RoomRolePermission' => array('id' => '40', 'roles_room_id' => '4')),
			array('RoomRolePermission' => array('id' => '51', 'roles_room_id' => '5')),
		));

		$this->__assertTable('PluginsRoom', 2, array('id', 'room_id'), array(
			array('PluginsRoom' => array('id' => '1', 'room_id' => '1')),
			array('PluginsRoom' => array('id' => '2', 'room_id' => '1')),
		));
	}

/**
 * テーブルのチェック
 *
 * @param string $model Model名
 * @param int $count データ件数
 * @param array $fields フィールド名
 * @param array $expected 期待値
 * @return void
 */
	private function __assertTable($model, $count, $fields, $expected = null) {
		$result = $this->TestModel->$model->find('all', array(
			'recursive' => -1,
			'fields' => $fields,
		));
		$this->assertCount($count, $result);

		if (isset($expected)) {
			$this->assertEquals($expected, $result);
		}
	}

}