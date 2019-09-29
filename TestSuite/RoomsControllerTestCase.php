<?php
/**
 * RoomsControllerTestCase TestCase
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

//@codeCoverageIgnoreStart;
App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('RoomsMockMethods', 'Rooms.TestSuite');
//@codeCoverageIgnoreEnd;

/**
 * RoomsControllerTestCase TestCase
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\TestSuite
 * @codeCoverageIgnore
 */
abstract class RoomsControllerTestCase extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	private $__fixtures = array(
		'plugin.data_types.data_type4test',
		'plugin.data_types.data_type_choice4test',
		'plugin.groups.group',
		'plugin.groups.groups_user',
		'plugin.pages.pages_language',
		'plugin.roles.default_role_permission4test',
		'plugin.rooms.page4test',
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
		'plugin.user_attributes.user_attribute4test',
		'plugin.user_attributes.user_attribute_choice4test',
		'plugin.user_attributes.user_attribute_layout',
		'plugin.user_attributes.user_attribute_setting4test',
		'plugin.user_attributes.user_attributes_role4test',
		'plugin.user_attributes.user_role_setting4test',
		'plugin.users.users_language4user',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'rooms';

/**
 * RoomsのMocksメソッドクラス
 *
 * @var RoomsMockMethods
 */
	private $__RoomsMockMethods;

/**
 * Fixtures load
 *
 * @param string $name The name parameter on PHPUnit_Framework_TestCase::__construct()
 * @param array  $data The data parameter on PHPUnit_Framework_TestCase::__construct()
 * @param string $dataName The dataName parameter on PHPUnit_Framework_TestCase::__construct()
 * @return void
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		if (! isset($this->fixtures)) {
			$this->fixtures = array();
		}
		$this->fixtures = array_merge($this->__fixtures, $this->fixtures);
		parent::__construct($name, $data, $dataName);

		$this->__RoomsMockMethods = new RoomsMockMethods();
	}

/**
 * RoomsコンポーネントをMockに差し替える
 *
 * @return void
 */
	protected function _mockRoomsComponent() {
		$this->__RoomsMockMethods->mockRoomsComponent($this, $this->_controller);
	}

/**
 * PermissionコンポーネントをMockに差し替える
 *
 * @return void
 */
	protected function _mockPermissionComponent() {
		$this->__RoomsMockMethods->mockPermissionComponent($this, $this->_controller);
	}

}
