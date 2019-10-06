<?php
/**
 * beforeDelete()とafterDelete()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * beforeDelete()とafterDelete()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Room
 */
class RoomDeleteTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.roles_room4test',
		'plugin.rooms.roles_rooms_user4test',
		'plugin.rooms.room4test',
		'plugin.rooms.room_role',
		'plugin.rooms.room_role_permission',
		'plugin.rooms.rooms_language4test',
		'plugin.rooms.space',
		'plugin.rooms.room_delete_related_table',
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
	protected $_methodName = 'delete';

/**
 * delete()のテスト
 *
 * @return void
 */
	public function testDelete() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$this->$model = $this->getMockForModel('Rooms.Room', array(
			'deleteFramesByRoom', 'deletePagesByRoom', 'deleteBlocksByRoom', 'deleteRolesRoomByRoom'
		));
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'deleteFramesByRoom', 4);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'deletePagesByRoom', 4);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'deleteBlocksByRoom', 4);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'deleteRolesRoomByRoom', 4);

		//事前チェック
		$roomIds = array('2', '5', '6', '9');
		$count = $this->$model->find('count', array(
			'recursive' => -1,
			'conditions' => array('id' => $roomIds)
		));
		$this->assertEquals(4, $count);

		//テスト実施
		$result = $this->$model->$methodName('2', false);
		$this->assertTrue($result);

		//テスト後のチェック
		$count = $this->$model->find('count', array(
			'recursive' => -1,
			'conditions' => array('id' => $roomIds)
		));
		$this->assertEquals(0, $count);
	}

}
