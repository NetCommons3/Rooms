<?php
/**
 * RoomsRolesFormHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * RoomsRolesFormHelper
 *
 * @package NetCommons\Rooms\View\Helper
 */
class RoomsRolesFormHelper extends AppHelper {

/**
 * 使用するヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'NetCommons.Token',
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
		$this->NetCommonsHtml->script(array(
			'/rooms/js/role_permissions.js',
			'/rooms/js/room_role_permissions.js'
		));

		parent::beforeRender($viewFile);
	}

/**
 * Outputs room roles radio
 *
 * @param string $fieldName Name attribute of the RADIO
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted RADIO element
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function checkboxRoomRoles($fieldName, $attributes = array()) {
		list($model, $permission) = explode('.', $fieldName);

		$html = '';

		$html .= '<div class="form-group">';

		//ラベル
		if (isset($attributes['label'])) {
			$html .= $this->NetCommonsForm->label($fieldName, $attributes['label']);
		}
		$attributes = Hash::remove($attributes, 'label');

		$initialize = NetCommonsAppController::camelizeKeyRecursive(
			array('roles' => $this->_View->viewVars['roles'])
		);
		$html .= '<div class="form-checkbox-outer" ng-controller="RoomRolePermissions" ' .
					'ng-init="RolePermission.initialize(' .
						h(json_encode($initialize, JSON_FORCE_OBJECT)) .
					')' .
				'">';

		//権限のチェックボックス
		$hidden = '';
		foreach ($this->_View->request->data[$model][$permission] as $roleKey => $role) {
			if (! $role['value'] && $role['fixed']) {
				continue;
			}

			$hidden .= $this->NetCommonsForm->hidden($fieldName . '.' . $roleKey . '.id');

			$options = Hash::merge(array(
				'div' => false,
				'disabled' => (bool)$role['fixed'],
				'inline' => true,
			), $attributes);
			if (! $options['disabled']) {
				$options['ng-click'] = 'RolePermission.clickRole(' . '$event, \'' .
						$model . '\', \'' . $permission . '\', \'' . Inflector::variable($roleKey) . '\')';
			}

			$options['label'] = $this->Rooms->roomRoleName($roleKey, ['help' => true]);
			$options['escape'] = false;
			$html .= $this->NetCommonsForm->checkbox($fieldName . '.' . $roleKey . '.value', $options);
		}
		$html .= $hidden;

		$html .= '</div>';

		if (Hash::get($attributes, 'help')) {
			$html .= $this->NetCommonsForm->help(
				Hash::get($attributes, 'help'),
				Hash::get($attributes, 'helpOptions')
			);
		}
		$html .= '</div>';

		return $html;
	}

/**
 * ルーム内の役割のデフォルト値のSELECTタグ
 *
 * @param string $fieldName フィールド名
 * @param array $attributes HTML属性オプション
 * @return string HTMLタグ
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function selectDefaultRoomRoles($fieldName, $attributes = array()) {
		//Option
		$defaultRoles = Hash::get($attributes, 'options', array());
		$attributes = Hash::remove($attributes, 'options');

		//OptionのFormat
		$optionFormat = Hash::get($attributes, 'optionFormat', '%s');
		$attributes = Hash::remove($attributes, 'optionFormat');

		//OptionのFormat変換
		foreach ($defaultRoles as $optkey => $optgroup) {
			if (is_array($optgroup)) {
				foreach ($optgroup as $key => $text) {
					$defaultRoles[$optkey][$key] = sprintf($optionFormat, $text);
				}
			} else {
				$defaultRoles[$optkey] = sprintf($optionFormat, $optgroup);
			}
		}

		$html = '';

		if (isset($attributes['label'])) {
			if (is_array($attributes['label'])) {
				$label = $attributes['label']['label'];
				unset($attributes['label']['label']);

				$html .= $this->NetCommonsForm->label($fieldName, $label, $attributes['label']) . ' ';
			} else {
				$html .= $this->NetCommonsForm->label($fieldName, $attributes['label']) . ' ';
			}
			unset($attributes['label']);
		}
		$attributes = Hash::merge(array(
			'type' => 'select',
			'class' => 'form-control',
			'empty' => false
		), $attributes);
		$html .= $this->NetCommonsForm->select($fieldName, $defaultRoles, $attributes);

		return $html;
	}

/**
 * ルームロールの説明書きを出力
 *
 * @param array $options オプション
 * @return string HTML
 */
	public function roomRolesDescription($options = []) {
		$html = '';

		$description = '';

		$roles = Hash::get($this->_View->viewVars, 'defaultRoles');
		foreach ($roles as $role) {
			$description .= '<div class="room-role-name-desc">';
			$description .= '<span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span> ';
			$description .= h($role['name']);
			$description .= '</div>';

			$description .= '<div>';
			$description .= h(__d('roles', $role['description']));
			$description .= '</div>';
		}

		$html .= ' <a href="" data-toggle="popover"' .
					' data-placement="bottom"' .
					' title="' . __d('rooms', 'Room role') . '"' .
					' data-content="' . h($description) . '"' .
					' data-trigger="focus">';

		$html .= '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>';
		$html .= '</a>';
		$html .= '<script type="text/javascript">' .
			'$(function () { $(\'[data-toggle="popover"]\').popover({html: true}) });</script>';

		return $html;
	}

/**
 * Tokenを取得する
 *
 * @param string $actionUrl アクションURL
 * @param int $roomId ルームID
 * @return array
 */
	public function getToken($actionUrl, $roomId) {
		$currentData = $this->_View->request->data;

		$data = [
			'RolesRoomsUser' => [
				'user_id' => '',
				'role_key' => '',
			],
			'Room' => [
				'id' => $roomId
			]
		];

		$tokenFields = Hash::flatten($data);
		$hiddenFields = $tokenFields;
		unset(
			$hiddenFields['RolesRoomsUser.user_id'],
			$hiddenFields['RolesRoomsUser.role_key']
		);

		$this->_View->request->data = $data;
		$tokens = $this->Token->getToken(
			'RolesRoomsUser', $actionUrl . '.json', $tokenFields, array_keys($hiddenFields)
		);

		$this->_View->request->data = $currentData;

		return $tokens;
	}

}
