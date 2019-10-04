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
//@codeCoverageIgnoreEnd;

/**
 * RoomsControllerTestCase TestCase
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\TestSuite
 * @codeCoverageIgnore
 */
class RoomsMockMethods {

/**
 * RoomsコンポーネントをMockに差し替える
 *
 * @param NetCommonsControllerTestCase $testCase テストケース
 * @param string $controllerName コントローラ名
 * @return void
 */
	public function mockRoomsComponent(NetCommonsControllerTestCase $testCase, $controllerName) {
		$this->__mockComponent($testCase, $controllerName, [
			'Rooms.Rooms' => ['initialize'],
			'NetCommons.Permission' => ['initialize', 'startup'],
		]);
	}

/**
 * PermissionコンポーネントをMockに差し替える
 *
 * @param NetCommonsControllerTestCase $testCase テストケース
 * @param string $controllerName コントローラ名
 * @return void
 */
	public function mockPermissionComponent(NetCommonsControllerTestCase $testCase, $controllerName) {
		$this->__mockComponent($testCase, $controllerName, [
			'NetCommons.Permission' => ['initialize', 'startup'],
		]);
	}

/**
 * コンポーネントをMockに差し替える
 *
 * @param NetCommonsControllerTestCase $testCase テストケース
 * @param string $controllerName コントローラ名
 * @param array $components Mockにするコンポーネントリスト
 * @return void
 */
	private function __mockComponent(
			NetCommonsControllerTestCase $testCase, $controllerName, $components) {
		$testCase->generate(Inflector::camelize($controllerName), [
			'components' => array_merge(['Security'], $components),
		]);

		if (isset($components['Rooms.Rooms'])) {
			$callback = function () use ($testCase) {
				$testCase->controller->Role = ClassRegistry::init('Roles.Role');
				$testCase->controller->set('spaces', [
					'2' => [
						'Space' => [
							'id' => '2',
							'plugin_key' => 'public_space',
							'default_setting_action' => 'rooms/index/2'
						],
						'RoomsLanguage' => [
							'2' => [
								'id' => '1', 'language_id' => '2', 'room_id' => '2',
								'name' => 'パブリックスペース'
							],
							'1' => [
								'id' => '2', 'language_id' => '1', 'room_id' => '2',
								'name' => 'Public space'
							]
						]
					],
				]);
				return;
			};
			$testCase->controller->Rooms
				->expects($testCase->once())->method('initialize')
				->will($testCase->returnCallback($callback));
		}
		if (isset($components['NetCommons.Permission'])) {
			$testCase->controller->Permission
				->expects($testCase->once())->method('initialize')
				->will($testCase->returnValue(null));
			$testCase->controller->Permission
				->expects($testCase->once())->method('startup')
				->will($testCase->returnValue(null));
		}
	}

}
