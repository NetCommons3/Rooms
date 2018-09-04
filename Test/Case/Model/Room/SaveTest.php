<?php
/**
 * beforeSave()とafterSave()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * beforeSave()とafterSave()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Room
 */
class RoomSaveTest extends NetCommonsModelTestCase {

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
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'Room';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'save';

/**
 * Save用Data
 *
 * @param bool $useMock モックを使うかどうか
 * @param bool $count モックを通る回数
 * @return array テストデータ
 */
	private function __data($useMock, $count = 1) {
		$model = $this->_modelName;

		if ($useMock) {
			$this->$model = $this->getMockForModel('Rooms.Room', array(
				'saveDefaultRolesRoom', 'saveDefaultRolesRoomsUser', 'saveDefaultRolesPluginsRoom',
				'saveDefaultRoomRolePermission', 'saveDefaultPage'
			));
			$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRolesRoom', $count);
			$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRolesRoomsUser', $count);
			$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRolesPluginsRoom', $count);
			$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRoomRolePermission', $count);
			$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultPage', $count);
		}

		$roomId = '';
		$data = array(
			'Room' => array(
				'id' => $roomId,
				'space_id' => '2',
				'parent_id' => '2',
				'default_participation' => '1',
				'default_role_key' => 'visitor',
				'need_approval' => '1',
				'active' => '1',
			),
			'Page' => array('parent_id' => '1'),
			'RoomsLanguage' => array(
				0 => array('id' => '', 'room_id' => $roomId, 'language_id' => '1', 'name' => 'Test room(lang=1)'),
				1 => array('id' => '', 'room_id' => $roomId, 'language_id' => '2', 'name' => 'Test room(lang=2)'),
			),
			'RoomRolePermission' => array(
				'content_publishable' => array(
					'room_administrator' => array('id' => ''),
					'chief_editor' => array('id' => '', 'value' => '1'),
					'editor' => array('id' => '', 'value' => '1'),
				),
				'html_not_limited' => array(
					'room_administrator' => array('id' => '', 'value' => '1'),
					'chief_editor' => array('id' => '', 'value' => '1'),
					'editor' => array('id' => '', 'value' => '1'),
					'general_user' => array('id' => '', 'value' => '1'),
				),
			),
		);

		return $data;
	}

/**
 * Roomのチェック
 *
 * @param int $roomId ルームID
 * @param int $pageIdTop 最初のページID
 * @return void
 */
	private function __acualRoom($roomId, $pageIdTop) {
		$model = $this->_modelName;

		$expected = array('Room' => array(
			'id' => $roomId,
			'space_id' => '2',
			'page_id_top' => $pageIdTop,
			'parent_id' => '2',
			//'lft' => '9',
			//'rght' => '10',
			'weight' => '3',
			'sort_key' => '~00000001-00000001-00000003',
			'child_count' => '0',
			'default_participation' => true,
			'default_role_key' => 'visitor',
			'need_approval' => true,
			'active' => true,
		));

		$result = $this->$model->find('first', array(
			'recursive' => -1,
			'fields' => array_keys($expected['Room']),
			'conditions' => array('id' => $roomId),
		));

		$this->assertEquals($expected, $result);
	}

/**
 * RoomsLanguageのチェック
 *
 * @param int $roomId ルームID
 * @param int $languageId 言語ID
 * @param int $roomsLangId ルーム言語ID
 * @return void
 */
	private function __acualRoomsLanguage($roomId, $languageId, $roomsLangId) {
		$model = $this->_modelName;

		$expected = array('RoomsLanguage' => array(
			'id' => $roomsLangId,
			'room_id' => $roomId,
			'language_id' => $languageId,
			'name' => sprintf('Test room(lang=%s)', $languageId),
		));

		$result = $this->$model->RoomsLanguage->find('first', array(
			'recursive' => -1,
			'fields' => array_keys($expected['RoomsLanguage']),
			'conditions' => array('room_id' => $roomId, 'language_id' => $languageId),
		));

		$this->assertEquals($expected, $result);
	}

/**
 * RoomRolePermissionのチェック
 *
 * @param string $permission パーミッション
 * @param array $expected 期待値
 * @return void
 */
	private function __acualRoomRolePermission($permission, $expected) {
		$model = $this->_modelName;
		$expected = Hash::insert($expected, '{s}.permission', $permission);

		$result = $this->$model->RoomRolePermission->find('all', array(
			'recursive' => -1,
			'fields' => array_keys($expected[0]['RoomRolePermission']),
			'conditions' => array(
				'roles_room_id' => Hash::extract($expected, '{n}.{s}.roles_room_id'),
				'permission' => $permission
			),
			'order' => array('roles_room_id' => 'asc'),
		));

		$this->assertEquals($expected, $result);
	}

/**
 * save()のテスト
 *
 * @param array $data 登録データ
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		Current::$current = Hash::insert(Current::$current, 'User.id', '1');
		Current::$current = Hash::insert(Current::$current, 'Language.id', '2');
		$data = $this->__data(false);
		$result = $this->$model->$methodName($data);
		$this->assertNotEmpty($result);

		//チェック
		$roomId = '10';
		$this->__acualRoom($roomId, '6');
		$this->__acualRoomsLanguage($roomId, '1', '19');
		$this->__acualRoomsLanguage($roomId, '2', '20');

		$rolesRooms = $this->$model->RolesRoom->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'role_key'),
			'conditions' => array('room_id' => $roomId),
			'order' => array('id' => 'asc'),
		));
		$this->assertEquals(array(
			'21' => 'room_administrator',
			'22' => 'chief_editor',
			'23' => 'editor',
			'24' => 'general_user',
			'25' => 'visitor',
		), $rolesRooms);

		$this->__acualRoomRolePermission('content_publishable', array(
			array('RoomRolePermission' => array('roles_room_id' => '21', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '22', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '23', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '24', 'value' => false)),
			array('RoomRolePermission' => array('roles_room_id' => '25', 'value' => false)),
		));

		$this->__acualRoomRolePermission('html_not_limited', array(
			array('RoomRolePermission' => array('roles_room_id' => '21', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '22', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '23', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '24', 'value' => true)),
			array('RoomRolePermission' => array('roles_room_id' => '25', 'value' => false)),
		));
	}

/**
 * save()のテスト(Roomのみ指定)
 *
 * @return void
 */
	public function testSaveOnlyRoom() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		$this->_mockForReturnTrue($model, 'Rooms.RoomsLanguage', 'save', 0);
		$this->_mockForReturnTrue($model, 'Rooms.RoomRolePermission', 'saveMany', 0);

		//テスト実施
		$data = $this->__data(true, 1);

		$result = $this->$model->$methodName(array('Room' => $data[$this->$model->alias]));
		$this->assertNotEmpty($result);

		//チェック
		$this->__acualRoom('10', null); //モックのため、page_id_topはnull
	}

/**
 * save()のExceptionErrorテスト(RoomsLanguage)
 *
 * @return void
 */
	public function testSaveRoomsLanguageOnExceptionError() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		$data = $this->__data(true, 0);
		$this->_mockForReturnFalse($model, 'Rooms.RoomsLanguage', 'save');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->$model->$methodName($data);
	}

/**
 * save()のExceptionErrorテスト(RoomRolePermission)
 *
 * @return void
 */
	public function testSaveRoomRolePermissionOnExceptionError() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		$data = $this->__data(true, 1);
		$this->_mockForReturnFalse($model, 'Rooms.RoomRolePermission', 'saveMany');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->$model->$methodName($data);
	}

}
