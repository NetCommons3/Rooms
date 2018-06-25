<?php
/**
 * ルーム作成(ウィザード) Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppController', 'Rooms.Controller');
App::uses('DefaultRolePermission', 'Roles.Model');
App::uses('Room', 'Rooms.Model');

/**
 * ルーム作成(ウィザード) Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller
 */
class RoomAddController extends RoomsAppController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'M17n.SwitchLanguage' => array(
			'fields' => array(
				'RoomsLanguage.name'
			)
		),
		'PluginManager.PluginsForm',
		'Rooms.RoomsRolesForm' => array(
			'permissions' => array('content_publishable', 'html_not_limited')
		),
		'UserAttributes.UserAttributeLayout',
		'Users.UserSearchComp',
	);

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Rooms.Room',
		'Rooms.RolesRoom',
		'Rooms.RolesRoomsUser',
		'Users.User',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Wizard' => array(
			'navibar' => array(
				parent::WIZARD_ROOMS => array(
					'url' => array(
						'controller' => 'room_add',
						'action' => 'basic',
					),
					'label' => array('rooms', 'General setting'),
				),
				parent::WIZARD_ROOMS_ROLES_USERS => array(
					'url' => array(
						'controller' => 'room_add',
						'action' => 'rooms_roles_users',
					),
					'label' => array('rooms', 'Edit the members to join'),
				),
				parent::WIZARD_PLUGINS_ROOMS => array(
					'url' => array(
						'controller' => 'room_add',
						'action' => 'plugins_rooms',
					),
					'label' => array('rooms', 'Select the plugins to join'),
				),
			),
			'cancelUrl' => array(
				'controller' => 'room_add',
				'action' => 'cancel',
			)
		),
		'UserAttributes.UserAttributeLayout',
		'Users.UserSearchForm',
		'Users.UserSearch',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		//ウィザードの設定
		$navibar = $this->helpers['NetCommons.Wizard']['navibar'];
		$navibar = Hash::insert($navibar, '{s}.url.key', $this->params['pass'][0]);
		$navibar = Hash::insert($navibar, '{s}.url.key2', $this->params['pass'][1]);
		$this->helpers['NetCommons.Wizard']['navibar'] = $navibar;

		$cancelUrl = $this->helpers['NetCommons.Wizard']['cancelUrl'];
		$cancelUrl = Hash::insert($cancelUrl, 'key', $this->params['pass'][0]);
		$cancelUrl = Hash::insert($cancelUrl, 'key2', $this->params['pass'][1]);
		$this->helpers['NetCommons.Wizard']['cancelUrl'] = $cancelUrl;

		$this->PluginsForm->roomId = $this->Session->read('RoomAdd.Room.id');

		if ($this->params['action'] === 'basic') {
			$this->Rooms->defaultRoleKeyList = Room::$defaultRoleKeyList;
		}

		if ($this->params['action'] !== 'basic' && ! $this->Session->read('RoomAdd.Room.id')) {
			return $this->setAction('cancel');
		}
	}

/**
 * ウィザードキャンセルアクション
 *
 * @return void
 */
	public function cancel() {
		if ($this->Session->read('RoomAdd.Room.id')) {
			//削除処理
			try {
				$this->Room->deleteRoom($this->Session->read('RoomAdd'));
			} catch (Exception $ex) {
				//エラーにしない
			}
			$this->Session->delete('RoomAdd');
		}

		$spaces = $this->viewVars['spaces'];
		$activeSpaceId = $this->viewVars['activeSpaceId'];
		return $this->redirect('/rooms/' . $spaces[$activeSpaceId]['Space']['default_setting_action']);
	}

/**
 * 一般設定アクション
 *
 * @return void
 */
	public function basic() {
		$spaceId = $this->viewVars['activeSpaceId'];
		$roomId = $this->viewVars['activeRoomId'];
		$room = $this->viewVars['room'];

		//スペースModel
		$model = Inflector::camelize($this->viewVars['spaces'][$spaceId]['Space']['plugin_key']);
		$this->$model = ClassRegistry::init($model . '.' . $model);
		$this->set('participationFixed', $this->$model->participationFixed);

		if ($this->request->is('post') || $this->request->is('put')) {
			//不要パラメータ除去
			unset($this->request->data['save'], $this->request->data['active_lang_id']);

			//他言語が入力されていない場合、Currentの言語データをセット
			$this->SwitchLanguage->setM17nRequestValue();

			//登録処理
			$this->request->data['Room']['in_draft'] = true;
			$room = $this->Room->saveRoom($this->request->data);
			if ($room) {
				//正常の場合
				$this->Session->write('RoomAdd', $room);
				return $this->redirect('/rooms/room_add/rooms_roles_users/' . $spaceId . '/' . $roomId);
			}
			$this->NetCommons->handleValidationError($this->Room->validationErrors);

		} else {
			//表示処理
			if (isset($room['Room']['page_id_top'])) {
				$pageId = $this->viewVars['room']['Room']['page_id_top'];
			} else {
				$pageId = null;
			}

			$referer = '/rooms/rooms/index/' . $spaceId;
			if ($this->Session->read('RoomAdd.Room.id') &&
					! preg_match('/' . preg_quote($referer, '/') . '/', $this->referer())) {
				$roomId = $this->Session->read('RoomAdd.Room.id');
				$this->request->data = $this->Session->read('RoomAdd');
				$this->RoomsRolesForm->settings['room_id'] = $roomId;
			} else {
				$this->request->data = Hash::merge($this->request->data,
					$this->$model->createRoom(array(
						'space_id' => $spaceId,
						'parent_id' => $roomId,
						'default_role_key' => $room['Room']['default_role_key'],
					))
				);
				$this->request->data = Hash::merge($this->request->data,
					$this->Page->create(array(
						'parent_id' => $pageId,
					))
				);
				$this->RoomsRolesForm->settings['room_id'] = null;
			}
		}

		//RoomsRolesFormのセット
		$this->RoomsRolesForm->settings['type'] = DefaultRolePermission::TYPE_ROOM_ROLE;
	}

/**
 * 参加者の選択アクション
 *
 * @return void
 */
	public function rooms_roles_users() {
		$this->set('room', $this->Session->read('RoomAdd'));

		$spaceId = $this->viewVars['activeSpaceId'];
		$roomId = $this->viewVars['activeRoomId'];
		$redirect = (bool)Hash::get($this->request->data, '_RoomsRolesUsers.redirect');

		$result = $this->RoomsRolesForm->actionRoomsRolesUser($this);
		if ($result === true && $redirect) {
			return $this->redirect('/rooms/room_add/plugins_rooms/' . $spaceId . '/' . $roomId);
		} elseif ($result === false) {
			$this->NetCommons->handleValidationError($this->RolesRoomsUser->validationErrors);
		}
	}

/**
 * 参加者の個別選択のアクション
 *
 * @return void
 */
	public function role_room_user() {
		$this->set('room', $this->Session->read('RoomAdd'));
		$result = $this->RoomsRolesForm->actionRoomsRolesUser($this);
		if ($result === false) {
			$this->NetCommons->handleValidationError($this->RolesRoomsUser->validationErrors);
		}
	}

/**
 * 検索フォーム表示アクション
 *
 * @return void
 */
	public function search_conditions() {
		//検索フォーム表示
		$this->UserSearchComp->conditions();
	}

/**
 * プラグイン選択アクション
 *
 * @return void
 */
	public function plugins_rooms() {
		$this->set('room', $this->Session->read('RoomAdd'));

		if ($this->request->is('put')) {
			//登録処理
			$room = $this->Session->read('RoomAdd');
			$room['Room']['in_draft'] = false;
			$room['PluginsRoom'] = $this->request->data['PluginsRoom'];

			if ($this->Room->saveRoom($room)) {
				//正常の場合
				$this->Session->delete('RoomAdd');

				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				$spaces = $this->viewVars['spaces'];
				$activeSpaceId = $this->viewVars['activeSpaceId'];
				return $this->redirect('/rooms/' . $spaces[$activeSpaceId]['Space']['default_setting_action']);
			} else {
				$this->NetCommons->handleValidationError($this->RolesRoomsUser->validationErrors);
			}
		}

		$this->request->data = $this->viewVars['room'];
	}

}
