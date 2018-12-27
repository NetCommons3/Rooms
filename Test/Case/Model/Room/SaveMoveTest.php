<?php
/**
 * Room::saveMove()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
/**
 * Room::saveMove()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Rooms\Test\Case\Model\Room
 */
class SaveMoveTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.rooms.roles_room',
		'plugin.rooms.roles_rooms_user',
		'plugin.rooms.room4test',
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
	protected $_modelName = 'Room';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'saveMove';

/**
 * saveMove()のテスト用DataProvider
 *
 * ### 戻り値
 *  - roomId ルームID
 *  - pageIdTop ルームのTOPページID
 *  - direction UP-DOWNの方向
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			array('roomId' => '5', 'pageIdTop' => '3', 'direction' => 'moveDown'),
			array('roomId' => '6', 'pageIdTop' => '4', 'direction' => 'moveUp'),
		);
	}

/**
 * saveMove()のテスト
 *
 * @param string $roomId ルームID
 * @param string $pageIdTop ルームTOPページID
 * @param string $direction UP-DOWNの方向
 * @dataProvider dataProvider
 * @return void
 */
	public function testSaveMove($roomId, $pageIdTop, $direction) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$data = array(
			'Room' => array('id' => $roomId, 'page_id_top' => $pageIdTop)
		);

		//テスト前のチェック
		$expected = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $roomId),
		));
		$oldWeight = $expected['Room']['weight'];

		//テスト実施
		$result = $this->$model->$methodName($data, $direction);

		//チェック
		$this->assertTrue($result);
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $roomId),
		));
		$newWeight = $actual['Room']['weight'];

		if ($direction == 'moveUp') {
			$this->assertEquals($oldWeight - 1, $newWeight);
		} else {
			$this->assertEquals($oldWeight + 1, $newWeight);
		}
	}

/**
 * saveActive()のExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveMoveOnExceptionError() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		$this->_mockForReturnFalse($model, 'Pages.Page', 'updateAll');
		$this->setExpectedException('InternalErrorException');

		//データ生成
		$data = array(
			'Room' => array('id' => 6, 'page_id_top' => 4)
		);

		//テスト実施
		$this->$model->$methodName($data, 'moveUp');
	}

}