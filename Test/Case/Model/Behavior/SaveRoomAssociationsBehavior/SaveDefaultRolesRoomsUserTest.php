<?php
/**
 * SaveRoomAssociationsBehavior::saveDefaultRolesRoomsUser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * SaveRoomAssociationsBehavior::saveDefaultRolesRoomsUser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Behavior\SaveRoomAssociationsBehavior
 */
class SaveRoomAssociationsBehaviorSaveDefaultRolesRoomsUserTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.roles_room4test',
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
		$this->TestModel = ClassRegistry::init('TestRooms.TestSaveRoomAssociationsBehaviorModel');
	}

/**
 * saveDefaultRolesRoomsUser()テストのDataProvider
 *
 * ### 戻り値
 *  - data Room data
 *
 * @return array データ
 */
	public function dataProvider() {
		return array(
			array('data' => array('Room' => array(
				'id' => '99',
				'space_id' => '2',
				'parent_id' => '2',
				'default_role_key' => 'visitor',
				'default_participation' => '1'
			)))
		);
	}

/**
 * saveDefaultRolesRoomsUser()のテスト
 * [Room.default_participation=true、isRoomCreate=true]
 *
 * @param array $data Room data
 * @dataProvider dataProvider
 * @return void
 */
	public function testWithDefaultParticipationOnRoomCreated($data) {
		//テストデータ作成
		Current::$current = Hash::insert(Current::$current, 'User.id', '2');
		Current::$current = Hash::insert(Current::$current, 'Language.id', '2');
		$roomId = $data['Room']['id'];
		$isRoomCreate = true;

		//事前データ作成
		$result = $this->TestModel->saveDefaultRolesRoom($data);
		$this->assertTrue($result);
		$this->__acualRolesRoom($roomId);

		//テスト実施
		$result = $this->TestModel->saveDefaultRolesRoomsUser($data, $isRoomCreate);
		$this->assertTrue($result);

		//チェック
		$this->__acualRolesRoomsUser($roomId);
	}

/**
 * saveDefaultRolesRoomsUser()のテスト
 * [isRoomCreate=false、ユーザID指定]
 *
 * @param array $data Room data
 * @dataProvider dataProvider
 * @return void
 */
	public function testWithDefaultParticipationOnUserCreated($data) {
		//テストデータ作成
		Current::$current = Hash::insert(Current::$current, 'User.id', '1');
		Current::$current = Hash::insert(Current::$current, 'Language.id', '2');
		$roomId = $data['Room']['id'];
		$isRoomCreate = false;
		$userId = '2';
		$data['RolesRoomsUser'] = array('user_id' => $userId);

		//事前データ作成
		$result = $this->TestModel->saveDefaultRolesRoom($data);
		$this->assertTrue($result);
		$this->__acualRolesRoom($roomId);

		//テスト実施
		$result = $this->TestModel->saveDefaultRolesRoomsUser($data, $isRoomCreate);
		$this->assertTrue($result);

		//チェック
		$this->__acualRolesRoomsUser($roomId, $userId, '25');
	}

/**
 * saveDefaultRolesRoomsUser()のテスト
 * [Room.default_participation=true、isRoomCreate=true]
 *
 * @param array $data Room data
 * @dataProvider dataProvider
 * @return void
 */
	public function testWODefaultParticipationOnRoomCreated($data) {
		//テストデータ作成
		Current::$current = Hash::insert(Current::$current, 'User.id', '2');
		Current::$current = Hash::insert(Current::$current, 'Language.id', '2');
		$roomId = $data['Room']['id'];
		$isRoomCreate = true;
		$data['Room']['default_participation'] = '0';

		//事前データ作成
		$result = $this->TestModel->saveDefaultRolesRoom($data);
		$this->assertTrue($result);
		$this->__acualRolesRoom($roomId);

		//テスト実施
		$result = $this->TestModel->saveDefaultRolesRoomsUser($data, $isRoomCreate);
		$this->assertTrue($result);

		//チェック
		$this->__acualRolesRoomsUser($roomId, '2', '21');
	}

/**
 * saveDefaultRolesRoomsUser()のExceptionError(save)テスト
 *
 * @param array $data Room data
 * @dataProvider dataProvider
 * @return void
 */
	public function testSaveRolesRoomsUserOnExceptionError($data) {
		//テストデータ作成
		Current::$current = Hash::insert(Current::$current, 'User.id', '2');
		Current::$current = Hash::insert(Current::$current, 'Language.id', '2');

		//事前データ作成
		$result = $this->TestModel->saveDefaultRolesRoom($data);
		$this->assertTrue($result);
		$this->__acualRolesRoom($data['Room']['id']);

		//テスト実施
		$this->_mockForReturnFalse('TestModel', 'Rooms.RolesRoomsUser', 'save');
		$this->setExpectedException('InternalErrorException');
		$this->TestModel->saveDefaultRolesRoomsUser($data, true);
	}

/**
 * saveDefaultRolesRoomsUser()のExceptionError(query)テスト
 *
 * @param array $data Room data
 * @dataProvider dataProvider
 * @return void
 */
	//public function testQueryRolesRoomsUserOnExceptionError($data) {
	//	//テストデータ作成
	//	Current::$current = Hash::insert(Current::$current, 'User.id', '2');
	//	Current::$current = Hash::insert(Current::$current, 'Language.id', '2');
	//
	//	//事前データ作成
	//	$result = $this->TestModel->saveDefaultRolesRoom($data);
	//	$this->assertTrue($result);
	//	$this->__acualRolesRoom($data['Room']['id']);
	//
	//	//テスト実施
	//	$this->_mockForReturn('TestModel', 'Rooms.RolesRoomsUser', 'getAffectedRows', 0);
	//	$this->setExpectedException('InternalErrorException');
	//	$this->TestModel->saveDefaultRolesRoomsUser($data, true);
	//}

/**
 * RolesRoomのチェック
 *
 * @param int $roomId ルームID
 * @return void
 */
	private function __acualRolesRoom($roomId) {
		$expected = array(
			array('RolesRoom' => array('id' => '21', 'room_id' => $roomId, 'role_key' => 'room_administrator')),
			array('RolesRoom' => array('id' => '22', 'room_id' => $roomId, 'role_key' => 'chief_editor')),
			array('RolesRoom' => array('id' => '23', 'room_id' => $roomId, 'role_key' => 'editor')),
			array('RolesRoom' => array('id' => '24', 'room_id' => $roomId, 'role_key' => 'general_user')),
			array('RolesRoom' => array('id' => '25', 'room_id' => $roomId, 'role_key' => 'visitor')),
		);

		$result = $this->TestModel->RolesRoom->find('all', array(
			'recursive' => -1,
			'fields' => array_keys($expected[0]['RolesRoom']),
			'conditions' => array('room_id' => $roomId),
		));

		$this->assertEquals($expected, $result);
	}

/**
 * RolesRoomsUserのチェック
 *
 * @param int $roomId ルームID
 * @param int $userId ユーザID
 * @param int $rolesRoomId ロールルームID
 * @return void
 */
	private function __acualRolesRoomsUser($roomId, $userId = null, $rolesRoomId = '21') {
		if ($userId) {
			$expected = array(
				array('RolesRoomsUser' => array(
					'id' => '12', 'roles_room_id' => $rolesRoomId, 'user_id' => $userId, 'room_id' => $roomId,
				)),
			);
		} else {
			$visitorRolesRoomId = '25';
			$expected = array(
				array('RolesRoomsUser' => array(
					'id' => '12', 'roles_room_id' => $rolesRoomId, 'user_id' => '2', 'room_id' => $roomId,
				)),
				array('RolesRoomsUser' => array(
					'id' => '13', 'roles_room_id' => $visitorRolesRoomId, 'user_id' => '1', 'room_id' => $roomId,
				)),
				array('RolesRoomsUser' => array(
					'id' => '14', 'roles_room_id' => $visitorRolesRoomId, 'user_id' => '3', 'room_id' => $roomId,
				)),
				array('RolesRoomsUser' => array(
					'id' => '15', 'roles_room_id' => $visitorRolesRoomId, 'user_id' => '4', 'room_id' => $roomId,
				)),
				array('RolesRoomsUser' => array(
					'id' => '16', 'roles_room_id' => $visitorRolesRoomId, 'user_id' => '5', 'room_id' => $roomId,
				)),
			);
		}

		$result = $this->TestModel->RolesRoomsUser->find('all', array(
			'recursive' => -1,
			'fields' => array_keys($expected[0]['RolesRoomsUser']),
			'conditions' => array('room_id' => $roomId),
		));
		$this->assertEquals($expected, $result);
	}

}
