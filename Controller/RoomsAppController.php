<?php
/**
 * RoomsApp Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * RoomsApp Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller
 */
class RoomsAppController extends AppController {

/**
 * ウィザード定数(一般設定)
 *
 * @var string
 */
	const WIZARD_ROOMS = 'rooms';

/**
 * ウィザード定数(参加者の管理)
 *
 * @var string
 */
	const WIZARD_ROOMS_ROLES_USERS = 'rooms_roles_users';

/**
 * ウィザード定数(プラグイン選択)
 *
 * @var string
 */
	const WIZARD_PLUGINS_ROOMS = 'plugins_rooms';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Rooms.Room',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'ControlPanel.ControlPanelLayout',
		'NetCommons.Permission' => array(
			'type' => PermissionComponent::CHECK_TYEP_SYSTEM_PLUGIN,
			'allow' => array()
		),
		'Rooms.Rooms',
		'Security',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Rooms.RoomForm',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index', 'view');

		//スペースデータチェック
		$spaceId = Hash::get($this->params['pass'], '0');
		if (! $this->Room->Space->exists($spaceId)) {
			return $this->setAction('throwBadRequest');
		}

		$this->set('activeSpaceId', $spaceId);

		//ルームデータチェック＆セット
		if ($this->params['action'] !== 'index') {
			if ($this->Session->read('RoomAdd.Room.id')) {
				$roomId = $this->Session->read('RoomAdd.Room.parent_id');
			} elseif ($this->request->is('post')) {
				$roomId = Hash::get($this->data, 'Room.parent_id');
			} elseif ($this->request->is('put') || $this->request->is('delete')) {
				$roomId = Hash::get($this->data, 'Room.id');
			} else {
				$roomId = Hash::get($this->params['pass'], '1');
			}
			$room = $this->Room->findById($roomId);
			if (! $room) {
				return $this->setAction('throwBadRequest');
			}
			$this->set('room', $room);
			$this->set('activeRoomId', $roomId);
			$this->set('activeSpaceId', $room['Space']['id']);

			$parentRooms = $this->Room->getPath($roomId, null, 1);
			$this->set('parentRooms', $parentRooms);
		}
	}

}
