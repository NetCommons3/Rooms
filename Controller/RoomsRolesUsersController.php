<?php
/**
 * RoomsUsers Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppController', 'Rooms.Controller');

/**
 * RoomsUsers Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller
 */
class RoomsRolesUsersController extends RoomsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Rooms.Room',
		'Rooms.RolesRoomsUser',
		'Users.User',
		'Users.UserSearch',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Rooms.RoomsRolesForm',
		'UserAttributes.UserAttributeLayout',
		'Users.UserSearchComp',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
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
		if ($this->params['action'] === 'role_room_user') {
			$this->request->data = Hash::insert(
				$this->request->data, 'Room.id', Hash::get($this->params['pass'], '1')
			);
		}
		parent::beforeFilter();
		$this->Security->unlockedActions = array('role_room_user');
	}

/**
 * 参加者の選択アクション
 *
 * @return void
 */
	public function edit() {
		$result = $this->RoomsRolesForm->actionRoomsRolesUser($this);
		if ($result === true) {
			//正常の場合
			$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
				'class' => 'success',
			));
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
}
