<?php
/**
 * View/Elements/Rooms/render_room_indexテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsController', 'Rooms.Controller');

/**
 * View/Elements/Rooms/render_room_indexテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\test_app\Plugin\TestRooms\Controller
 */
class TestViewElementsRoomsRenderRoomIndexController extends RoomsController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Rooms.Rooms',
	);

/**
 * use helper
 *
 * @var array
 */
	public $helpers = array(
		'Rooms.RoomsForm',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		$action = $this->params['action'];
		$this->params['action'] = 'index';

		parent::beforeFilter();

		$this->params['plugin'] = 'rooms';
		$this->params['controller'] = 'rooms';
		$this->params['action'] = $action;
	}

/**
 * render_room_index
 *
 * @return void
 */
	public function render_room_index() {
		parent::index();
		$this->autoRender = true;
		Current::remove('Block.id');

		$this->set('options', array(
			'room' => $this->viewVars['rooms']['5'],
			'nest' => 1
		));
	}

/**
 * render_room_index_root
 *
 * @return void
 */
	public function render_room_index_root() {
		parent::index();
		$this->autoRender = true;
		$this->view = 'render_room_index';
		Current::remove('Block.id');

		$this->set('options', array(
			'room' => $this->viewVars['spaces']['2'],
			'nest' => 0
		));
	}

}
