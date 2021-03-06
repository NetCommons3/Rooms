<?php
/**
 * View/Elements/Rooms/render_indexテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsController', 'Rooms.Controller');

/**
 * View/Elements/Rooms/render_indexテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\test_app\Plugin\TestRooms\Controller
 */
class TestViewElementsRoomsRenderIndexController extends RoomsController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Rooms.Rooms',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		$action = $this->params['action'];
		$this->params['plugin'] = 'rooms';
		$this->params['controller'] = 'rooms';
		$this->params['action'] = 'index';

		parent::beforeFilter();

		$this->params['action'] = $action;
	}

/**
 * render_index
 *
 * @return void
 */
	public function render_index() {
		parent::index();
		Current::remove('Block.id');

		$this->autoRender = true;
		$this->view = 'render_index';
		$this->params['action'] = 'index';

		$this->set('options', array(
			'headElementPath' => 'TestRooms.TestViewElementsRoomsRenderIndex/render_header',
			'dataElementPath' => 'TestRooms.TestViewElementsRoomsRenderIndex/render_room_index',
			'roomTreeList' => $this->viewVars['roomTreeList'],
			'space' => $this->viewVars['spaces']['2'],
			'paginator' => true,
			'displaySpace' => true,
		));
	}

}
