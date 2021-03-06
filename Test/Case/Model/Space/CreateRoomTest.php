<?php
/**
 * Space::createRoom()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * Space::createRoom()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Space
 */
class SpaceCreateRoomTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.roles_room',
		'plugin.rooms.roles_rooms_user',
		'plugin.rooms.room',
		'plugin.rooms.room_role',
		'plugin.rooms.room_role_permission',
		'plugin.rooms.rooms_language',
		'plugin.rooms.space',
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
	protected $_modelName = 'Space';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'createRoom';

/**
 * createRoom()のテスト
 *
 * @return void
 */
	public function testCreateRoom() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$data = array();

		//テスト実施
		$result = $this->$model->$methodName($data);

		//チェック
		$this->assertEquals(array('Room', 'RoomsLanguage'), array_keys($result));
		$this->assertTrue(Hash::get($result, 'Room.active'));
		$this->assertArrayHasKey('id', Hash::get($result, 'Room'));
		$this->assertCount(2, Hash::get($result, 'RoomsLanguage'));
		$this->assertEquals('1', Hash::get($result, 'RoomsLanguage.0.language_id'));
		$this->assertEquals('2', Hash::get($result, 'RoomsLanguage.1.language_id'));
	}

}
