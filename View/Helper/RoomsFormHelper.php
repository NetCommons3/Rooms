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
 * ルームFormヘルパー
 *
 * RoomsFormComponentとセットで使用する
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\UserAttribute\View\Helper
 */
class RoomsFormHelper extends AppHelper {

/**
 * 使用するヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'Rooms.Rooms',
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
 * Roomに対するプラグインチェックボックスリスト
 *
 * @param string $fieldName フィールド名
 * @param array $attributes HTMLの属性オプション
 * @return string HTML
 */
	public function checkboxRooms($fieldName, $attributes = array()) {
		$html = '';

		$roomTreeList = $this->_View->viewVars['roomTreeList'];

		if (! $roomTreeList) {
			return $html;
		}

		$html .= $this->NetCommonsForm->hidden($fieldName, array(
			'value' => false,
		));
		$this->NetCommonsForm->unlockField($fieldName);

		$html .= '<dl>';
		foreach ($roomTreeList as $roomId => $tree) {
			$nest = substr_count($tree, Room::$treeParser);
			$html .= $this->__room($fieldName, $roomId, $nest, $attributes);
		}
		$html .= '</dl>';

		return $html;
	}

/**
 * Roomに対するプラグインチェックボックスリスト
 *
 * self::checkboxRooms()から実行される
 *
 * @param string $fieldName フィールド名
 * @param int $roomId ルームID
 * @param string $nest インデント
 * @param array $attributes HTMLの属性オプション
 * @return string HTML
 */
	private function __room($fieldName, $roomId, $nest, $attributes = array()) {
		$html = '';

		$rooms = $this->_View->viewVars['rooms'];
		$default = Hash::get($attributes, 'default', array());

		if (! Hash::get($rooms, $roomId) ||
				Hash::get($rooms, $roomId . '.Room.space_id') === Space::PRIVATE_SPACE_ID &&
				! Hash::get($attributes, 'privateSpace', true)) {
			return $html;
		}
		$room = Hash::get($rooms, $roomId);

		$html .= '<dd class="form-group form-inline">';
		$html .= str_repeat('<span class="rooms-tree"> </span>', $nest);

		$html .= $this->NetCommonsForm->checkbox($fieldName . '.', array(
			'label' => $this->Rooms->roomName($room),
			'escape' => false,
			'div' => false,
			'id' => $this->domId($fieldName . $roomId),
			'value' => $roomId,
			'hiddenField' => false,
			'checked' => in_array((string)$roomId, $default, true),
		));

		$html .= $this->NetCommonsForm->hidden('Room.' . $roomId . '.id', array(
			'value' => $roomId
		));
		$html .= '</dd>';

		return $html;
	}

}
