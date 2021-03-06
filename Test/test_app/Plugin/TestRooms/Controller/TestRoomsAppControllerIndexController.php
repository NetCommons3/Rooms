<?php
/**
 * RoomsAppController::beforeFilter()テスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppController', 'Rooms.Controller');

/**
 * RoomsAppController::beforeFilter()テスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\test_app\Plugin\TestRooms\Controller
 */
class TestRoomsAppControllerIndexController extends RoomsAppController {

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->autoRender = true;
	}

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		$this->autoRender = true;
	}
}
