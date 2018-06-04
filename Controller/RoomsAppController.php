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
App::uses('Space', 'Rooms.Model');

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
		'Pages.Page',
		'Rooms.Room',
		'Rooms.Space',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'ControlPanel.ControlPanelLayout',
		'NetCommons.Permission' => array(
			'type' => PermissionComponent::CHECK_TYPE_SYSTEM_PLUGIN,
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
		'Rooms.RoomsForm',
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
			//ルームデータのセット
			$this->_setRoom();

			//親ルームデータのセット
			$this->_setParentRooms();
		}
	}

/**
 * ルームデータをセットする
 *
 * @return void
 */
	protected function _setRoom() {
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
	}

/**
 * 親ルームデータをセットする
 *
 * @return void
 */
	protected function _setParentRooms() {
		$roomId = $this->viewVars['activeRoomId'];

		$spaceRoomIds = $this->Space->find('list', [
			'recursive' => -1,
			'fields' => ['id', 'room_id_root']
		]);
		if (! in_array($roomId, $spaceRoomIds, true)) {
			$parentRooms = $this->Room->getPath($roomId, null, 1);
			//サイト全体のルームIDを削除する
			if (Hash::get($parentRooms, '0.Space.id') === Space::WHOLE_SITE_ID) {
				unset($parentRooms[0]);
			}
			//if (in_array(Hash::get($parentRooms, '1.Room.id'), $spaceRoomIds, true)) {
			//	unset($parentRooms[1]);
			//}
			$this->set('parentRooms', $parentRooms);
		} else {
			$this->set('parentRooms', null);
		}
	}

}
