<?php
/**
 * Room::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('RoomFixture', 'Rooms.Test/Fixture');
App::uses('RoomsLanguageFixture', 'Rooms.Test/Fixture');
App::uses('RoomRolePermission4testFixture', 'Rooms.Test/Fixture');

/**
 * Room::validate()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\Model\Room
 */
class RoomValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'Room';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'validates';

/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ(省略可)
 *
 * @return array テストデータ
 */
	public function dataProviderValidationError() {
		$data['Room'] = (new RoomFixture())->records[0];

		return array(
			array('data' => $data, 'field' => 'space_id', 'value' => null,
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'space_id', 'value' => '',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'space_id', 'value' => 'a',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'page_id_top', 'value' => 'a',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'parent_id', 'value' => 'a',
				'message' => __d('net_commons', 'Invalid request.')),
			//array('data' => $data, 'field' => 'lft', 'value' => 'a',
			//	'message' => __d('net_commons', 'Invalid request.')),
			//array('data' => $data, 'field' => 'rght', 'value' => 'a',
			//	'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'active', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'need_approval', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'default_participation', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
			array('data' => $data, 'field' => 'page_layout_permitted', 'value' => '2',
				'message' => __d('net_commons', 'Invalid request.')),
		);
	}

/**
 * RoomsLanguageのValidationErrorテスト
 *
 * @return void
 */
	public function testRoomsLanguageValidationError() {
		$model = $this->_modelName;
		$this->_mockForReturnFalse($model, 'Rooms.RoomsLanguage', 'validateMany');

		//データ生成
		$data['Room'] = (new RoomFixture())->records[1];
		$data['RoomsLanguage'] = (new RoomsLanguageFixture())->records[0];

		//テスト実施
		$this->$model->set($data);
		$result = $this->$model->validates();

		//チェック
		$this->assertFalse($result);
	}

/**
 * RoomRolePermissionのValidationSuccessテスト
 *
 * @return void
 */
	public function testRoomRolePermissionValidationSuccess() {
		$model = $this->_modelName;

		//データ生成
		$data['Room'] = (new RoomFixture())->records[1];
		$data['RoomRolePermission'][0][0] = (new RoomRolePermission4testFixture())->records[0];

		//テスト実施
		$this->$model->set($data);
		$result = $this->$model->validates();

		//チェック
		$this->assertTrue($result);
	}

/**
 * RoomRolePermissionのValidationErrorテスト
 *
 * @return void
 */
	public function testRoomRolePermissionValidationError() {
		$model = $this->_modelName;
		$this->_mockForReturnFalse($model, 'Rooms.RoomRolePermission', 'validateMany');

		//データ生成
		$data['Room'] = (new RoomFixture())->records[1];
		$data['RoomRolePermission'][0][0] = (new RoomRolePermission4testFixture())->records[0];

		//テスト実施
		$this->$model->set($data);
		$result = $this->$model->validates();

		//チェック
		$this->assertFalse($result);
	}

}
