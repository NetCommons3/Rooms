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
App::uses('RoomsComponent', 'Rooms.Controller/Component');
App::uses('RoomsAppController', 'Rooms.Controller');

/**
 * ルーム表示ヘルパー
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\UserAttribute\View\Helper
 */
class RoomsHelper extends AppHelper {

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
		'NetCommons.Date',
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'Users.DisplayUser',
	);

/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->NetCommonsHtml->css('/rooms/css/style.css');
		parent::beforeRender($viewFile);
	}

/**
 * ルームナビの出力
 *
 * @param int $activeSpaceId スペースID
 * @return string HTML
 */
	public function roomsNavi($activeSpaceId) {
		$output = '';

		if (isset($this->_View->viewVars['parentRooms'])) {
			$pathName = '{n}.RoomsLanguage.{n}[language_id=' . Current::read('Language.id') . '].name';
			$roomNames = Hash::extract($this->_View->viewVars['parentRooms'], $pathName);
		}

		if (isset($roomNames)) {
			$output = implode(self::ROOM_NAME_PAUSE, array_map('h', $roomNames));
		} else {
			$output = $this->roomName($this->_View->viewVars['spaces'][$activeSpaceId]);
		}
		if ($this->_View->request->params['controller'] === 'room_add') {
			if (count($roomNames) === 1) {
				$output .= self::ROOM_NAME_PAUSE . __d('rooms', 'Add new room');
			} else {
				$output .= self::ROOM_NAME_PAUSE . __d('rooms', 'Add new subroom');
			}
		}

		return $output;
	}

/**
 * タブの出力
 *
 * @param int $activeSpaceId スペースID
 * @param bool $tabType タブの種類（tabs or pills）
 * @param string|null $urls URLオプション
 * @return string HTML
 */
	public function spaceTabs($activeSpaceId, $tabType = 'tabs', $urls = null) {
		$output = '';
		$output .= '<ul class="nav nav-' . $tabType . '" role="tablist">';
		foreach ($this->_View->viewVars['spaces'] as $space) {
			if ($space['Space']['default_setting_action']) {
				if ($space['Space']['id'] === $activeSpaceId) {
					$listClass = ' class="active"';
				} elseif ($urls === false) {
					$listClass = ' class="disabled"';
				} else {
					$listClass = '';
				}

				if ($urls === false) {
					$output .= '<li' . $listClass . '>';
					$output .= '<a href="">' . $this->roomName($space) . '</a>';
					$output .= '</li>';
					continue;
				}

				$attributes = array();

				$output .= '<li' . $listClass . '>';
				if (! isset($urls)) {
					$url = '/rooms/' . $space['Space']['default_setting_action'];
				} elseif (is_string($urls)) {
					$url = sprintf($urls, (int)$space['Space']['id']);
				} elseif (isset($urls[$space['Space']['id']])) {
					$url = Hash::get($urls[$space['Space']['id']], 'url');
					$attributes = (array)Hash::get($urls[$space['Space']['id']], 'attributes');
				}
				$output .= $this->NetCommonsHtml->link($this->roomName($space), $url, $attributes);
				$output .= '</li>';
			}
		}
		$output .= '</ul>';

		return $output;
	}

/**
 * ルーム一覧の出力
 *
 * @param int $activeSpaceId スペースID
 * @param array $element データ表示エレメント
 * ```
 * array(
 *		'headElement' => ヘッダ表示エレメント,
 *		'dataElemen' => データ表示エレメント,
 * )
 * ```
 * @param array $options オプション
 * ```
 * array(
 *		'paginator' => ページネーションの有無 デフォルト：true,
 *		'displaySpace' => スペース表示,
 *		'roomTreeList' => ルームのTreeリスト,
 *		'rooms' => ルームリスト,
 *		'tableClass' => テーブルタグのclass属性,
 * )
 * ```
 * @return string HTML
 */
	public function roomsRender($activeSpaceId, $element, $options = []) {
		$output = '';

		$roomTreeList = Hash::get($options, 'roomTreeList');
		if (! isset($roomTreeList)) {
			$roomTreeList = Hash::get($this->_View->viewVars, 'roomTreeList');
		}

		$rooms = Hash::get($options, 'rooms');
		if (! isset($rooms)) {
			$rooms = Hash::get($this->_View->viewVars, 'rooms');
		}

		$output .= $this->_View->element('Rooms.Rooms/render_index', array(
			'headElementPath' => Hash::get($element, 'headElement'),
			'dataElementPath' => Hash::get($element, 'dataElemen'),
			'roomTreeList' => $roomTreeList,
			'rooms' => $rooms,
			'space' => $this->_View->viewVars['spaces'][$activeSpaceId],
			'paginator' => Hash::get($options, 'paginator', true),
			'displaySpace' => Hash::get($options, 'displaySpace', false),
			'tableClass' => Hash::get($options, 'tableClass', 'table'),
		));

		return $output;
	}

/**
 * ルーム名の出力
 *
 * @param array $room ルームデータ配列
 * @param int|null $nest インデント
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
 * @param array $room ルームデータ配列
 * @param string|null $prefix CSSクラスのプレフィックス
 * @return string HTML
 */
	public function statusCss($room, $prefix = '') {
		$output = '';
		if (! $room['Room']['active']) {
			$output .= $prefix . 'danger';
		}
		return $output;
	}

/**
 * 状態のラベルを出力
 *
 * @param array $room ルームデータ配列
 * @param string $messageFormat メッセージフォーマット
 * @param bool $displayActive アクティブの表示有無
 * @return string HTML
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function statusLabel($room, $messageFormat = '%s', $displayActive = false) {
		$output = '';

		if (! $room['Room']['active']) {
			$output .= ' ' . __d('rooms', sprintf($messageFormat, 'Under maintenance'));
		} elseif ($displayActive) {
			$output .= ' ' . __d('rooms', sprintf($messageFormat, 'Open'));
		}
		return $output;
	}

/**
 * ルームロール名の出力
 *
 * @param string|array $roomRoleKey ルームロールKey
 * @return string HTML
 */
	public function roomRoleName($roomRoleKey) {
		if (is_array($roomRoleKey)) {
			$roomRoleKey = Hash::get($roomRoleKey, 'RolesRoom.role_key', '');
		}
		return h(Hash::get($this->_View->viewVars, 'defaultRoles.' . $roomRoleKey, ''));
	}

/**
 * ルームのアクセス日時の出力
 *
 * @param array $room ルームデータ配列
 * @param string $field フィールド
 * @return string HTML
 */
	public function roomAccessed($room, $field = 'last_accessed') {
		$output = '';

		if (Hash::get($room, 'RolesRoomsUser.' . $field)) {
			$output .= $this->Date->dateFormat(Hash::get($room, 'RolesRoomsUser.' . $field));
		}

		return $output;
	}

/**
 * ルームの参加者リストを表示する(一覧表示)
 *
 * @param array $roomUsers ルームのユーザリスト配列
 * @param int $roomId ルームID
 * @return string HTML
 */
	public function roomMembers($roomUsers, $roomId) {
		$output = '';

		if (! $roomUsers) {
			return $output;
		}
		$moreParams = $this->_View->viewVars['activeSpaceId'] . ', ' .
				$roomId . ', ' .
				'\'' . RoomsAppController::WIZARD_ROOMS_ROLES_USERS . '\'';

		foreach ($roomUsers as $i => $user) {
			if ($i >= RoomsComponent::LIST_LIMIT_ROOMS_USERS) {
				break;
			}
			$handlename = Hash::get($user, 'User.handlename');
			$output .= $this->DisplayUser->avatarLink(
				$user, array('alt' => $handlename, 'title' => $handlename), array(), 'User.id'
			);
		}
		if (count($roomUsers) > RoomsComponent::LIST_LIMIT_ROOMS_USERS) {
			$output .= '<a href="" ng-click="showRoom(' . $moreParams . ')">' .
						__d('net_commons', '...') .
					'</a>';
		}

		return $output;
	}

}
