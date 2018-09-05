<?php
/**
 * Room::saveRoom()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');

/**
 * Room::saveRoom()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Room
 */
class RoomSaveRoomTest extends NetCommonsSaveTest {

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
		'plugin.rooms.room_role_permission4test',
		'plugin.rooms.rooms_language4test',
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
	protected $_methodName = 'saveRoom';

/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$results = array();
		// * 編集の登録処理
		$roomId = '4';
		$results[0] = array(array(
			'Room' => array(
				'id' => $roomId,
				'space_id' => '2',
				'parent_id' => '1',
				'default_participation' => '1',
				'default_role_key' => 'visitor',
				'need_approval' => '1',
				'active' => '1',
			),
			'Page' => array('parent_id' => '1'),
			'RoomsLanguage' => array(
				0 => array('id' => '7', 'room_id' => $roomId, 'language_id' => '1', 'name' => 'Test room'),
				1 => array('id' => '8', 'room_id' => $roomId, 'language_id' => '2', 'name' => 'Test room'),
			),
			'RoomRolePermission' => array(
				'content_publishable' => array(
					'room_administrator' => array('id' => '62'),
					'chief_editor' => array('id' => '67', 'value' => '1'),
					'editor' => array('id' => '69', 'value' => '0'),
				),
				'html_not_limited' => array(
					'room_administrator' => array('id' => '65', 'value' => '1'),
					'chief_editor' => array('id' => '68', 'value' => '1'),
					'editor' => array('id' => '70', 'value' => '1'),
					'general_user' => array('id' => '71', 'value' => '1'),
				),
			),
		));
		// * 新規の登録処理
		$roomId = '';
		$results[1] = array(array(
			'Room' => array(
				'id' => $roomId,
				'space_id' => '2',
				'parent_id' => '1',
				'default_participation' => '1',
				'default_role_key' => 'visitor',
				'need_approval' => '1',
				'active' => '1',
			),
			'Page' => array('parent_id' => '1'),
			'RoomsLanguage' => array(
				0 => array('id' => '', 'room_id' => $roomId, 'language_id' => '1', 'name' => 'Test room'),
				1 => array('id' => '', 'room_id' => $roomId, 'language_id' => '2', 'name' => 'Test room'),
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
		));

		return $results;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		if ($data['Room']['id']) {
			$count = 0;
			$treeData[$this->$model->alias] = array('lft' => '3', 'lft' => '4');
		} else {
			$count = 1;
			$treeData[$this->$model->alias] = array('lft' => '6', 'lft' => '7');
		}

		$this->$model = $this->getMockForModel('Rooms.Room', array(
			'saveDefaultRolesRoom', 'saveDefaultRolesRoomsUser', 'saveDefaultRolesPluginsRoom',
			'saveDefaultRoomRolePermission', 'saveDefaultPage'
		));
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRolesRoom', $count);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRolesRoomsUser', $count);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRolesPluginsRoom', $count);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultRoomRolePermission', $count);
		$this->_mockForReturnTrue($model, 'Rooms.Room', 'saveDefaultPage', $count);

		//チェック用データ取得
		if (Hash::get($data[$this->$model->alias], 'id')) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias]['id']),
			));
		} else {
			$before[$this->$model->alias] = array();
		}

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//idのチェック
		if (Hash::get($data[$this->$model->alias], 'id')) {
			$id = $data[$this->$model->alias]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $id),
		));
		if (isset($data[$this->$model->alias]['id'])) {
			unset($actual[$this->$model->alias]['modified']);
			unset($actual[$this->$model->alias]['modified_user']);
		} else {
			unset($actual[$this->$model->alias]['created']);
			unset($actual[$this->$model->alias]['created_user']);
			unset($actual[$this->$model->alias]['modified']);
			unset($actual[$this->$model->alias]['modified_user']);
			$before[$this->$model->alias] = array();
		}
		$expected[$this->$model->alias] = Hash::merge(
			$before[$this->$model->alias],
			$data[$this->$model->alias],
			array('id' => $id),
			$treeData
		);
		unset($expected[$this->$model->alias]['modified']);
		unset($expected[$this->$model->alias]['modified_user']);
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Rooms.Room', 'save'),
		);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Rooms.Room'),
		);
	}

}
