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

/**
 * ルーム表示ヘルパー
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\UserAttribute\View\Helper
 */
class RoomsHtmlHelper extends AppHelper {

/**
 * ルーム名のナビゲータの区切り文字
 *
 * @var const
 */
	const ROOM_NAME_PAUSE = ' / ';

/**
 * 使用するヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
	);

/**
 * After render file callback.
 * Called after any view fragment is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The file just be rendered.
 * @param string $content The content that was rendered.
 * @return void
 */
	public function afterRenderFile($viewFile, $content) {
		$content = $this->NetCommonsHtml->css('/rooms/css/style.css') . $content;
		parent::afterRenderFile($viewFile, $content);
	}

/**
 * サブタイトルの出力
 *
 * @return string HTML
 */
	public function subtitle($activeSpaceId) {
		$output = '';

		if (isset($this->_View->viewVars['parentRooms'])) {
			$pathName = '{n}.RoomsLanguage.{n}[language_id=' . Current::read('Language.id') . '].name';
			$roomNames = Hash::extract($this->_View->viewVars['parentRooms'], $pathName);
		}

		if (isset($roomNames)) {
			$element = implode(self::ROOM_NAME_PAUSE, array_map('h', $roomNames));
		} else {
			$element = $this->roomName($this->_View->viewVars['spaces'][$activeSpaceId]);
		}
		if ($this->_View->request->params['action'] === 'add') {
			if (! $this->_View->viewVars['room']['Room']['parent_id']) {
				$element .= self::ROOM_NAME_PAUSE .
						'<span class="glyphicon glyphicon-plus"></span>' . __d('rooms', 'Add new room');
			} else {
				$element .= self::ROOM_NAME_PAUSE .
						'<span class="glyphicon glyphicon-plus"></span>' . __d('rooms', 'Add new subroom');
			}
		}
		$output .= $this->NetCommonsHtml->div(array(
			'text-muted', 'small',
			'visible-xs-inline-block', 'visible-sm-inline-block', 'visible-md-inline-block', 'visible-lg-inline-block'
		), $element);

		return '(' . $output . ')';
	}

/**
 * タブの出力
 *
 * @param bool $tabType タブの種類（tabs or pills）
 * @return string HTML
 */
	public function tabs($activeSpaceId, $tabType = 'tabs', $urlFormat = null) {
		$output = '';
		$output .= '<ul class="nav nav-' . $tabType . '" role="tablist">';
		foreach ($this->_View->viewVars['spaces'] as $space) {
			if ($space['Space']['default_setting_action']) {
				$output .= '<li class="' . ($space['Space']['id'] === $activeSpaceId ? 'active' : '') . '">';

				if (isset($urlFormat)) {
					$url = sprintf($urlFormat, $space['Space']['id']);
				} else {
					$url = '/rooms/' . $space['Space']['default_setting_action'];
				}
				$output .= $this->NetCommonsHtml->link($this->roomName($space), $url);
				$output .= '</li>';
			}
		}
		$output .= '</ul>';
		$output .= '<br>';

		return $output;
	}

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
			$urlRooms = '';
			$urlRolesRoomsUsers = '';
			$urlPluginsRooms = '';
		} else {
			$disabled = '';
			$urlRooms = '/rooms/rooms/' . $this->_View->params['action'] . '/' . h($activeSpaceId) . '/' . h($activeRoomId) . '/';
			$urlRolesRoomsUsers = '/rooms/rooms_roles_users/edit/' . h($activeSpaceId) . '/' . h($activeRoomId) . '/';
			$urlPluginsRooms = '/rooms/plugins_rooms/edit/' . h($activeSpaceId) . '/' . h($activeRoomId) . '/';
		}

		$output .= '<ul class="nav nav-pills" role="tablist">';
		$output .= '<li class="' . ($this->_View->params['controller'] === 'rooms' ? 'active' : $disabled) . '">';
		$output .= $this->NetCommonsHtml->link(__d('rooms', 'General setting'), $urlRooms);
		$output .= '</li>';

		if (isset($this->_View->request->data['Room']) &&
				$this->_View->request->data['Room']['id'] !== Room::ROOM_PARENT_ID) {
			$output .= '<li class="' . ($this->_View->params['controller'] === 'rooms_roles_users' ? 'active' : $disabled) . '">';
			$output .= $this->NetCommonsHtml->link(__d('rooms', 'Edit the members to join'), $urlRolesRoomsUsers);
			$output .= '</li>';
		}

		if (isset($this->_View->request->data['Room']['parent_id'])) {
			$output .= '<li class=' . ($this->_View->params['controller'] === 'plugins_rooms' ? 'active' : $disabled) . '>';
			$output .= $this->NetCommonsHtml->link(__d('rooms', 'Select the plugins to join'), $urlPluginsRooms);
			$output .= '</li>';
		}
		$output .= '</ul>';
		$output .= '<br>';

		return $output;
	}

/**
 * ルーム一覧の出力
 *
 * @return string HTML
 */
	public function roomsRender($activeSpaceId, $dataElementPath, $headElementPath = null) {
		$output = '';
		$output .= $this->_View->element('Rooms.Rooms/render_index', array(
			'headElementPath' => $headElementPath,
			'dataElementPath' => $dataElementPath,
			'space' => $this->_View->viewVars['spaces'][$activeSpaceId],
		));

		return $output;
	}

/**
 * ルーム名の出力
 *
 * @return string HTML
 */
	public function roomName($room, $nest = null) {
		$roomsLanguage = Hash::extract(
			$room,
			'RoomsLanguage.{n}[language_id=' . Current::read('Language.id') . ']'
		);

		$output = '';
		if (isset($nest)) {
			$output .= str_repeat('<span class="rooms-tree"> </span>', $nest);
		}
		$output .= h($roomsLanguage[0]['name']);
		return $output;
	}

/**
 * 状態によるCSSのクラス定義を返す
 *
 * @return string HTML
 */
	public function statusCss($room) {
		$output = '';
		if (! $room['Room']['active']) {
			$output .= 'danger';
		}
		return $output;
	}

/**
 * 状態の変更
 *
 * @return string HTML
 */
	public function changeStatus($room) {
		$this->_View->request->data = $room;
		$output = '';

		$output .= $this->NetCommonsForm->create('Room', array(
			'action' => $this->NetCommonsHtml->url(array('action' => 'active', $room['Space']['id'], $room['Room']['id']))
		));

		$output .= $this->NetCommonsForm->hidden('Room.id');
		if ($room['Room']['active']) {
			$output .= $this->NetCommonsForm->hidden('Room.active', array('value' => false));
			$output .= $this->NetCommonsForm->button(__d('rooms', 'It will be in maintenance'), array(
				'name' => 'save',
				'class' => 'btn-link',
				'ng-disabled' => 'sending'
			));
		} else {
			$output .= $this->NetCommonsForm->hidden('Room.active', array('value' => true));
			$output .= $this->NetCommonsForm->button(__d('rooms', 'Open the room'), array(
				'name' => 'save',
				'class' => 'btn-link',
				'ng-disabled' => 'sending'
			));
		}
		$output .= $this->NetCommonsForm->end();

		return $output;
	}

}
