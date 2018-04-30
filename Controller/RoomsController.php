<?php
/**
 * Rooms Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppController', 'Rooms.Controller');
App::uses('UserAttributeChoice', 'UserAttributes.Model');
App::uses('Space', 'Rooms.Model');

/**
 * Rooms Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller
 */
class RoomsController extends RoomsAppController {

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
		'Paginator',
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
		'Pages.Page',
		'PluginManager.PluginsRoom',
		'Rooms.Room',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'UserAttributes.UserAttributeLayout',
		'Users.UserSearch',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Rooms->defaultRoleKeyList = Room::$defaultRoleKeyList;
	}

/**
 * 不要なデータ削除
 *
 * @return void
 */
	private function __prepare() {
		//もし、不要なルーム作成ウィザード用のデータが残っている場合、削除する
		if ($this->Session->read('RoomAdd.Room.id')) {
			//削除処理
			try {
				$this->Room->deleteRoom($this->Session->read('RoomAdd'));
			} catch (Exception $ex) {
				//エラーにしない
			}
			$this->Session->delete('RoomAdd');
		}

		//参加者修正のデータ削除
		$this->Session->delete('RoomsRolesUsers');
		$this->Session->delete('paginateConditionsByRoomRoleKey');

		//キャンセルボタンを押さずにルーム作成ウィザードを終了したときのごみデータ削除
		$date = new DateTime();
		$date->sub(new DateInterval('P1D'));
		$modified = $date->format('Y-m-d H:i:s');

		$query = array(
			'recursive' => -1,
			'fields' => array('id'),
			'conditions' => array(
				'in_draft' => true,
				'modified <' => $modified
			),
		);
		$count = $this->Room->find('count', $query);

		if ($count > 0) {
			$rooms = $this->Room->find('all', $query);
			foreach ($rooms as $room) {
				$this->Room->deleteRoom($room);
			}
		}
	}

/**
 * indexアクション
 *
 * @return void
 */
	public function index() {
		$this->__prepare();

		//ルームデータセット
		$this->Rooms->setRoomsForPaginator($this->viewVars['activeSpaceId']);

		$roomIds = array_keys($this->viewVars['rooms']);

		//プライベートのときはログインユーザのアクティブルームに限定する
		if ($this->viewVars['activeSpaceId'] === Space::PRIVATE_SPACE_ID) {
			$this->Rooms->setReadableRooms(Current::read('User.id'));

			foreach ($this->viewVars['rolesRoomsUsers'] as $rolesRoomsUser) {
				if ($rolesRoomsUser['Room']['space_id'] === Space::PRIVATE_SPACE_ID) {
					$roomIds = Array($rolesRoomsUser['Room']['id']);
					break;
				}
			}
		}

		//参加者リスト取得
		$rolesRoomsUsers = array();
		$rolesRoomsUsersCount = array();
		foreach ($roomIds as $roomId) {
			$result = $this->RolesRoomsUser->getRolesRoomsUsers(
				array('RolesRoomsUser.room_id' => $roomId),
				array(
					'fields' => array(
						'User.id', 'User.handlename'
					),
					'conditions' => array(
						'User.status' => UserAttributeChoice::STATUS_CODE_ACTIVE
					),
					'order' => array(
						'RoomRole.weight' => 'asc'
					),
					'limit' => RoomsComponent::LIST_LIMIT_ROOMS_USERS + 1
				)
			);
			$rolesRoomsUsers[$roomId] = $result;

			$rolesRoomsUsersCount[$roomId] = $this->RolesRoomsUser->getRolesRoomsUsers(
				array('RolesRoomsUser.room_id' => $roomId),
				array(
					'type' => 'count',
					'conditions' => array(
						'User.status' => UserAttributeChoice::STATUS_CODE_ACTIVE
					),
				)
			);
		}
		$this->set('rolesRoomsUsers', $rolesRoomsUsers);
		$this->set('rolesRoomsUsersCount', $rolesRoomsUsersCount);
	}

/**
 * 編集アクション
 *
 * @return void
 */
	public function view() {
		$this->viewClass = 'View';
		$this->layout = 'NetCommons.modal';
		$this->set('activeTab', Hash::get($this->request->query, 'tab', parent::WIZARD_ROOMS));
		$this->set('isEdit', Hash::get($this->request->query, 'isEdit', true));

		//表示処理
		$this->request->data = $this->viewVars['room'];
		$this->request->data = Hash::merge($this->request->data,
			$this->Page->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $this->viewVars['room']['Room']['page_id_top'])
			))
		);

		$this->RoomsRolesForm->settings['room_id'] = $this->viewVars['activeRoomId'];
		$this->RoomsRolesForm->settings['type'] = DefaultRolePermission::TYPE_ROOM_ROLE;
		$this->PluginsForm->roomId = $this->viewVars['activeRoomId'];

		$this->RoomsRolesForm->limit = 10;
		$this->request->query = array();
		$this->RoomsRolesForm->actionRoomsRolesUser($this);
	}

/**
 * 編集アクション
 *
 * @return void
 */
	public function edit() {
		//スペースModel
		$activeSpaceId = $this->viewVars['activeSpaceId'];
		$model = Inflector::camelize($this->viewVars['spaces'][$activeSpaceId]['Space']['plugin_key']);
		$this->$model = ClassRegistry::init($model . '.' . $model);
		if ($this->viewVars['room']['Room']['id'] === Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID)) {
			$this->set('participationFixed', true);
		} else {
			$this->set('participationFixed', $this->$model->participationFixed);
		}

		if ($this->request->is('put')) {
			//不要パラメータ除去
			unset($this->request->data['save'], $this->request->data['active_lang_id']);

			//他言語が入力されていない場合、Currentの言語データをセット
			$this->SwitchLanguage->setM17nRequestValue();

			//登録処理
			$room = $this->Room->saveRoom($this->request->data);
			if ($room) {
				//正常の場合
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				return $this->redirect(
					'/rooms/' . $this->viewVars['spaces'][$activeSpaceId]['Space']['default_setting_action']
				);
			}
			$this->NetCommons->handleValidationError($this->Room->validationErrors);

		} else {
			//表示処理
			$this->request->data = $this->viewVars['room'];
			$this->request->data = Hash::merge($this->request->data,
				$this->Page->find('first', array(
					'recursive' => -1,
					'conditions' => array('id' => $this->viewVars['room']['Room']['page_id_top'])
				))
			);
		}

		$this->RoomsRolesForm->settings['room_id'] = $this->viewVars['activeRoomId'];
		$this->RoomsRolesForm->settings['type'] = DefaultRolePermission::TYPE_ROOM_ROLE;
	}

/**
 * 削除アクション
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->is('delete')) {
			return $this->throwBadRequest();
		}

		//削除処理
		if (! $this->Room->deleteRoom($this->request->data)) {
			return $this->throwBadRequest();
		}

		$activeSpaceId = $this->viewVars['activeSpaceId'];

		//正常の場合
		$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
			'class' => 'success',
		));
		$this->redirect(
			'/rooms/' . $this->viewVars['spaces'][$activeSpaceId]['Space']['default_setting_action']
		);
	}

/**
 * 状態変更アクション
 *
 * @return void
 */
	public function active() {
		if (! $this->request->is('put')) {
			return $this->throwBadRequest();
		}

		if (! $this->Room->saveActive($this->request->data)) {
			return $this->throwBadRequest();
		}

		$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
			'class' => 'success',
		));
		$this->redirect('/rooms/rooms/index/' . $this->viewVars['activeSpaceId']);
	}

}
