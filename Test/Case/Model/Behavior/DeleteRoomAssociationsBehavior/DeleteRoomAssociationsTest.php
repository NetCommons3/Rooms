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
		$this->TestModel->RoomRolePermission = ClassRegistry::init('Rooms.RoomRolePermission');
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
		$result[0]['roomId'] = '5';

		return $result;
	}

/**
 * deleteRoomAssociations()のテスト
 *
 * @param int $roomId ルームID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeleteRolesRoomByRoom($roomId) {
		//事前チェック
		$this->__assertTable('RoomRolePermission', 29, array('id', 'roles_room_id'));

		//テスト実施
		$result = $this->TestModel->deleteRolesRoomByRoom($roomId);
		$this->assertTrue($result);

		//チェック
		$this->__assertTable('RoomRolePermission', 18, array('id', 'roles_room_id'), array(
			array('RoomRolePermission' => array('id' => '2', 'roles_room_id' => '1')),
			array('RoomRolePermission' => array('id' => '7', 'roles_room_id' => '1')),
			array('RoomRolePermission' => array('id' => '13', 'roles_room_id' => '2')),
			array('RoomRolePermission' => array('id' => '18', 'roles_room_id' => '2')),
			array('RoomRolePermission' => array('id' => '24', 'roles_room_id' => '3')),
			array('RoomRolePermission' => array('id' => '29', 'roles_room_id' => '3')),
			array('RoomRolePermission' => array('id' => '35', 'roles_room_id' => '4')),
			array('RoomRolePermission' => array('id' => '40', 'roles_room_id' => '4')),
			array('RoomRolePermission' => array('id' => '46', 'roles_room_id' => '5')),
			array('RoomRolePermission' => array('id' => '51', 'roles_room_id' => '5')),
			array('RoomRolePermission' => array('id' => '75', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '76', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '77', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '78', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '79', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '80', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '81', 'roles_room_id' => '20')),
			array('RoomRolePermission' => array('id' => '82', 'roles_room_id' => '20')),
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
