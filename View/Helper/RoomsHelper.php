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

		$roomNames = [];

		if (isset($this->_View->viewVars['parentRooms'])) {
			$pathName = '{n}.RoomsLanguage.{n}[language_id=' . Current::read('Language.id') . '].name';
			$roomNames = Hash::extract($this->_View->viewVars['parentRooms'], $pathName);
		}

		if ($roomNames) {
			//array_shift($roomNames);
			$output = implode(self::ROOM_NAME_PAUSE, array_map('h', $roomNames));
		} else {
			$output = $this->roomName($this->_View->viewVars['spaces'][$activeSpaceId]);
		}
		if ($this->_View->request->params['controller'] === 'room_add') {
			if (! $roomNames) {
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
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
	public function spaceTabs($activeSpaceId, $tabType = 'tabs', $urls = null) {
		$output = '';
		$output .= '<ul class="nav nav-' . $tabType . '" role="tablist">';
		foreach ($this->_View->viewVars['spaces'] as $space) {
			if (is_array($urls) && ! isset($urls[$space['Space']['id']])) {
				continue;
			}

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
		$name = '';
		foreach ($room['RoomsLanguage'] as $item) {
			if ($item['language_id'] == Current::read('Language.id')) {
				$name = $item['name'];
				break;
			}
		}

		$output = '';
		if (isset($nest)) {
			$output .= str_repeat('<span class="rooms-tree"> </span>', $nest);
		}
		$output .= h($name);
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
 * @link https://github.com/NetCommons3/UserManager/blob/3.0.1/View/Elements/UsersRolesRooms/render_room_index.ctp#L31
 * @link https://github.com/NetCommons3/Users/blob/3.0.1/View/Elements/Users/view_rooms_index.ctp#L27
 */
	public function statusLabel($room, $messageFormat = '%s', $displayActive = false) {
		$output = '';

		if (! $room['Room']['active']) {
			if ($messageFormat === '(%s)') {
				$output .= ' ' . __d('rooms', '(Under maintenance)');
			} else {
				$output .= ' ' . __d('rooms', 'Under maintenance');
			}
		} elseif ($displayActive) {
			if ($messageFormat === '(%s)') {
				$output .= ' ' . __d('rooms', '(Open)');
			} else {
				$output .= ' ' . __d('rooms', 'Open');
			}
		}
		return $output;
	}

/**
 * ルームロール名の出力
 *
 * @param string|array $roomRoleKey ルームロールKey
 * @param array $options オプション
 * @return string HTML
 */
	public function roomRoleName($roomRoleKey, $options = []) {
		if (is_array($roomRoleKey)) {
			$roomRoleKey = Hash::get($roomRoleKey, 'RolesRoom.role_key', '');
		}

		if (Hash::get($options, 'roles')) {
			$role = Hash::get($options, 'roles')[$roomRoleKey];
		} elseif (Hash::get($this->_View->request->data, 'Role.' . $roomRoleKey)) {
			$role = $this->_View->request->data['Role'][$roomRoleKey];
		} elseif (Hash::get($this->_View->viewVars, 'defaultRoles.' . $roomRoleKey)) {
			$role = Hash::get($this->_View->viewVars, 'defaultRoles.' . $roomRoleKey);
		} else {
			return h(Hash::get(
				$this->_View->viewVars, 'defaultRoles.' . $roomRoleKey, Hash::get($options, 'default', ''))
			);
		}

		$roleName = h($role['name']);
		$html = $roleName;

		if (Hash::get($options, 'help') && $role['description']) {
			$roleDesc = h(__d('roles', $role['description']));

			$html .= ' <a href="" data-toggle="popover"' .
						' data-placement="' . Hash::get($options, 'placement', 'top') . '"' .
						' title="' . h($roleName) . '"' .
						' data-content="' . $roleDesc . '"' .
						//' data-trigger="focus">';
						' data-trigger="focus">';
			$html .= '<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>';
			$html .= '</a>';
			$html .= '<script type="text/javascript">' .
				'$(function () { $(\'[data-toggle="popover"]\').popover({html: true}) });</script>';
		}

		return $html;
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

}
