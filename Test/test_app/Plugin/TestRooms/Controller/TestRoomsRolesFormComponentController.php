<?php
/**
 * RoomsRolesFormComponentテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RoomsRolesFormComponentテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\test_app\Plugin\TestRooms\Controller
 */
class TestRoomsRolesFormComponentController extends AppController {

/**
 * 使用コンポーネント
 *
 * @var array
 */
	public $components = array(
		'Rooms.RoomsRolesForm'
	);

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->autoRender = true;
	}

/**
 * index_request_action
 *
 * @return void
 */
	public function index_request_action() {
		$this->autoRender = true;
		$view = $this->requestAction('/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/index', array('return'));
		$this->set('view', $view);
	}

/**
 * index
 *
 * @return void
 */
	public function index_before_render() {
		$this->autoRender = true;
		$this->view = 'index';
		$this->RoomsRolesForm->settings['permissions'] = array('content_publishable');
		$this->RoomsRolesForm->settings['type'] = 'room_role';
		$this->RoomsRolesForm->settings['room_id'] = '1';
	}

/**
 * index_request_action
 *
 * @return void
 */
	public function index_before_render_request_action() {
		$this->autoRender = true;
		$view = $this->requestAction('/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/index_before_render', array('return'));
		$this->set('view', $view);
	}

}