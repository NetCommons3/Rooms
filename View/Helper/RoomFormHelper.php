<?php
/**
 * Rooms Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Room', 'Rooms.Model');

/**
 * ルーム表示ヘルパー
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\UserAttribute\View\Helper
 */
class RoomFormHelper extends AppHelper {

/**
 * 使用するヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.LinkButton',
		'NetCommons.MessageFlash',
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'Rooms.Rooms',
	);

/**
 * ルーム設定タブの出力
 *
 * @return string HTML
 */
	public function settingTabs() {
		$activeSpaceId = $this->_View->viewVars['activeSpaceId'];
		if (isset($this->_View->viewVars['activeRoomId'])) {
			$activeRoomId = $this->_View->viewVars['activeRoomId'];
		} else {
			$activeRoomId = null;
		}

		$output = '';
		if ($this->_View->params['action'] === 'add') {
			$disabled = 'disabled';
			$urlRooms = '#';
			$urlRolesRoomsUsers = '#';
			$urlPluginsRooms = '#';
		} else {
			$disabled = '';
			$urlRooms = '/rooms/rooms/' . $this->_View->params['action'] . '/' .
					h($activeSpaceId) . '/' . h($activeRoomId) . '/';
			$urlRolesRoomsUsers = '/rooms/rooms_roles_users/edit/' .
					h($activeSpaceId) . '/' . h($activeRoomId) . '/';
			$urlPluginsRooms = '/rooms/plugins_rooms/edit/' .
					h($activeSpaceId) . '/' . h($activeRoomId) . '/';
		}

		$output .= '<ul class="nav nav-pills" role="tablist">';
		if ($this->_View->params['controller'] === 'rooms') {
			$class = 'active';
		} else {
			$class = $disabled;
		}
		$output .= '<li class="' . $class . '">';
		$output .= $this->NetCommonsHtml->link(__d('rooms', 'General setting'), $urlRooms);
		$output .= '</li>';

		if (Hash::get($this->_View->request->data, 'Room.id') !== Room::ROOM_PARENT_ID) {
			if ($this->_View->params['controller'] === 'rooms_roles_users') {
				$class = 'active';
			} else {
				$class = $disabled;
			}
			$output .= '<li class="' . $class . '">';
			$output .= $this->NetCommonsHtml->link(
				__d('rooms', 'Edit the members to join'), $urlRolesRoomsUsers
			);
			$output .= '</li>';
		}

		if (isset($this->_View->request->data['Room']['parent_id'])) {
			if ($this->_View->params['controller'] === 'plugins_rooms') {
				$class = 'active';
			} else {
				$class = $disabled;
			}
			$output .= '<li class="' . $class . '">';
			$output .= $this->NetCommonsHtml->link(
				__d('rooms', 'Select the plugins to join'), $urlPluginsRooms
			);
			$output .= '</li>';
		}
		$output .= '</ul>';

		return $output;
	}

/**
 * 状態の変更
 *
 * @param array $room ルームデータ配列
 * @return string HTML
 */
	public function changeStatus($room) {
		$this->_View->request->data = $room;
		$output = '';

		$output .= $this->NetCommonsForm->create('Room', array(
			'type' => 'put',
			'class' => 'inline-block rooms-change-status',
			'url' => $this->NetCommonsHtml->url(array(
				'action' => 'active', $room['Space']['id'], $room['Room']['id']
			)),
		));

		$output .= $this->NetCommonsForm->hidden('Room.id');
		if ($room['Room']['active']) {
			$output .= $this->NetCommonsForm->hidden('Room.active', array('value' => '0'));
			$output .= $this->NetCommonsForm->button(__d('rooms', 'Open'), array(
				'name' => 'save',
				'class' => 'btn btn-default btn-xs',
				'ng-disabled' => 'sending'
			));
		} else {
			$output .= $this->NetCommonsForm->hidden('Room.active', array('value' => '1'));
			$output .= $this->NetCommonsForm->button(__d('rooms', 'Under maintenance'), array(
				'name' => 'save',
				'class' => 'btn btn-default btn-xs',
				'ng-disabled' => 'sending'
			));
		}
		$output .= $this->NetCommonsForm->end();

		return $output;
	}

/**
 * スペース編集の説明＋ボタン
 *
 * @param int $spaceId スペースID
 * @return string HTML
 */
	public function editSpaceDescription($spaceId) {
		$space = $this->_View->viewVars['spaces'][$spaceId];

		$output = '';
		$output .= '<div class="clearfix">';

		if ($spaceId === Space::PUBLIC_SPACE_ID) {
			$output .= sprintf(
				__d('rooms', 'The setting of %s space also to non-members is published.'),
				$this->Rooms->roomName($space)
			);
		} else {
			$output .= sprintf(
				__d('rooms', 'The setting of %s space.'),
				$this->Rooms->roomName($space)
			);
		}

		$output .= $this->NetCommonsHtml->div(null,
			$this->LinkButton->edit(
				'',
				array('action' => 'edit', 'key' => $space['Space']['id'], 'key2' => $space['Room']['id']),
				array('iconSize' => 'btn-xs')
			),
			array(
				'class' => 'pull-right'
			)
		);

		$output .= '</div>';

		return $this->MessageFlash->description($output);
	}

/**
 * ルーム追加の説明＋ボタン
 *
 * @param int $spaceId スペースID
 * @return string HTML
 */
	public function addRoomDescription($spaceId) {
		$space = $this->_View->viewVars['spaces'][$spaceId];

		$output = '';
		$output .= '<div class="clearfix">';

		$output .= sprintf(
			__d('rooms', 'In %s space, to add a new room.'),
			$this->Rooms->roomName($space)
		);

		$output .= $this->NetCommonsHtml->div(null,
			$this->LinkButton->add(
				__d('rooms', 'Add new room'),
				array(
					'controller' => 'room_add',
					'action' => 'basic',
					'key' => $space['Space']['id'],
					'key2' => $space['Room']['id']
				),
				array('iconSize' => 'btn-xs')
			),
			array(
				'class' => 'pull-right'
			)
		);

		$output .= '</div>';

		return $this->MessageFlash->description($output);
	}

/**
 * ルームの説明
 *
 * @param int $spaceId スペースID
 * @return string HTML
 */
	public function indexRoomDescription($spaceId) {
		$space = $this->_View->viewVars['spaces'][$spaceId];

		$output = '';

		$output .= sprintf(
			__d('rooms', 'The setting of each room was created in a %s space.'),
			$this->Rooms->roomName($space)
		);

		return $this->MessageFlash->description($output);
	}

}
