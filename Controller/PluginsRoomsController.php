<?php
/**
 * PluginsRooms Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppController', 'Rooms.Controller');

/**
 * PluginsRooms Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller
 */
class PluginsRoomsController extends RoomsAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'PluginManager.PluginsRoom',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'PluginManager.PluginsForm',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->PluginsForm->roomId = $this->viewVars['activeRoomId'];
	}

/**
 * editアクション
 *
 * @return void
 */
	public function edit() {
		if ($this->request->is('put')) {
			//登録処理
			if ($this->viewVars['activeRoomId'] === Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID)) {
				$result = $this->PluginsRoom->savePluginsRoomsByPrivateRoomId(
					$this->request->data['Room']['id'],
					$this->request->data['PluginsRoom']['plugin_key']
				);
			} else {
				$result = $this->PluginsRoom->savePluginsRoomsByRoomId(
					$this->request->data['Room']['id'],
					$this->request->data['PluginsRoom']['plugin_key']
				);
			}
			if ($result) {
				$this->NetCommons->setFlashNotification(
					__d('net_commons', 'Successfully saved.'), array('class' => 'success')
				);
				$spaceId = $this->viewVars['activeSpaceId'];
				return $this->redirect(
					'/rooms/' . $this->viewVars['spaces'][$spaceId]['Space']['default_setting_action']
				);
			} else {
				return $this->throwBadRequest();
			}

		} else {
			Current::write('Room', $this->viewVars['room']['Room']);
			$this->request->data = $this->viewVars['room'];
		}
	}

}
