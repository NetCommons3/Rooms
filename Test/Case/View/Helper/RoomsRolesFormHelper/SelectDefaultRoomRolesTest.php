<?php
/**
 * RoomsRolesFormHelper::selectDefaultRoomRoles()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsHelperTestCase', 'NetCommons.TestSuite');

/**
 * RoomsRolesFormHelper::selectDefaultRoomRoles()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Case\View\Helper\RoomsRolesFormHelper
 */
class RoomsRolesFormHelperSelectDefaultRoomRolesTest extends NetCommonsHelperTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'rooms';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Role = ClassRegistry::init('Roles.Role');

		//テストデータ生成
		$viewVars = array();
		$viewVars['defaultRoleOptions'] = $Role->find('list', array(
			'recursive' => -1,
			'fields' => array('key', 'name'),
			'conditions' => array(
				'is_system' => true,
				'language_id' => Current::read('Language.id'),
				'type' => Role::ROLE_TYPE_ROOM
			),
			'order' => array('id' => 'asc')
		));

		$requestData = array();
		$requestData = Hash::insert($requestData, 'Model.field', 'general_user');

		//Helperロード
		$this->loadHelper('Rooms.RoomsRolesForm', $viewVars, $requestData);
	}

/**
 * selectDefaultRoomRoles()のテスト
 *
 * @return void
 */
	public function testSelectDefaultRoomRoles() {
		//テスト実施
		$result = $this->RoomsRolesForm->selectDefaultRoomRoles('Model.field', array(
			'options' => array(
				__d('rooms', 'Room role') => $this->RoomsRolesForm->_View->viewVars['defaultRoleOptions'],
				'-----------------------' => array(
					'' => __d('users', 'Non members')
				)
			),
		));

		//チェック
		$this->__assertSelectDefaultRoomRoles($result);
	}

/**
 * selectDefaultRoomRoles()のテスト
 * [optionのフォーマット]
 *
 * @return void
 */
	public function testWithNameFormat() {
		//テスト実施
		$result = $this->RoomsRolesForm->selectDefaultRoomRoles('Model.field', array(
			'optionFormat' => 'Format %s',
			'options' => array(
				__d('rooms', 'Room role') => $this->RoomsRolesForm->_View->viewVars['defaultRoleOptions'],
				'-----------------------' => array(
					'' => __d('users', 'Non members')
				)
			),
		));

		//チェック
		$this->__assertSelectDefaultRoomRoles($result, 'Format %s');
	}

/**
 * selectDefaultRoomRoles()のテスト
 * [labelタグ追加]
 *
 * @return void
 */
	public function testWithLabel() {
		//テスト実施
		$result = $this->RoomsRolesForm->selectDefaultRoomRoles('Model.field', array(
			'label' => 'Model label',
			'options' => array(
				__d('rooms', 'Room role') => $this->RoomsRolesForm->_View->viewVars['defaultRoleOptions'],
				'-----------------------' => array(
					'' => __d('users', 'Non members')
				)
			),
		));

		//チェック
		$pattern = '<label for="ModelField" class="control-label">Model label</label>';
		$this->assertTextContains($pattern, $result);

		$this->__assertSelectDefaultRoomRoles($result);
	}

/**
 * selectDefaultRoomRoles()のテスト
 * [labelタグとlabelのCSS追加]
 *
 * @return void
 */
	public function testWithLabelAndClass() {
		//テスト実施
		$result = $this->RoomsRolesForm->selectDefaultRoomRoles('Model.field', array(
			'label' => array(
				'label' => 'Model label',
				'class' => 'model-css'
			),
			'options' => array(
				__d('rooms', 'Room role') => $this->RoomsRolesForm->_View->viewVars['defaultRoleOptions'],
				'-----------------------' => array(
					'' => __d('users', 'Non members')
				)
			),
		));

		//チェック
		$pattern = '<label for="ModelField" class="model-css">Model label</label>';
		$this->assertTextContains($pattern, $result);

		$this->__assertSelectDefaultRoomRoles($result);
	}

/**
 * selectDefaultRoomRoles()のテスト
 * [deleteオプション追加]
 *
 * @return void
 */
	public function testWithEmptyOption() {
		//テスト実施
		$result = $this->RoomsRolesForm->selectDefaultRoomRoles('Model.field', array(
			'empty' => __d('rooms', 'Change the user role of the room'),
			'options' => array(
				__d('rooms', 'Room role') => $this->RoomsRolesForm->_View->viewVars['defaultRoleOptions'],
				'-----------------------' => array(
					'' => __d('users', 'Non members')
				)
			),
		));

		//チェック
		$pattern = '<option value="">' . __d('rooms', 'Change the user role of the room') . '</option>';
		$this->assertTextContains($pattern, $result);

		$this->__assertSelectDefaultRoomRoles($result);
	}

/**
 * selectDefaultRoomRoles()のテスト
 * [deleteオプション追加]
 *
 * @return void
 */
	public function testWithDeleteOption() {
		//テスト実施
		$result = $this->RoomsRolesForm->selectDefaultRoomRoles('Model.field', array(
			'options' => array(
				__d('rooms', 'Room role') => $this->RoomsRolesForm->_View->viewVars['defaultRoleOptions'],
				'----------------------------------' => array('delete' => __d('users', 'Non members'))
			),
		));

		//チェック
		$this->__assertSelectDefaultRoomRoles($result);

		$pattern = '<option value="delete">' . __d('users', 'Non members') . '</option>';
		$this->assertTextContains($pattern, $result);
	}

/**
 * selectDefaultRoomRoles()のチェック
 *
 * @param string $result 結果
 * @param string $format 文言のフォーマット(sprintf)
 * @return void
 */
	private function __assertSelectDefaultRoomRoles($result, $format = '%s') {
		$pattern = '<select name="data[Model][field]" class="form-control" id="ModelField">';
		$this->assertTextContains($pattern, $result);

		$pattern = '<option value="chief_editor">' . sprintf($format, 'Chief editor') . '</option>';
		$this->assertTextContains($pattern, $result);

		$pattern = '<option value="editor">' . sprintf($format, 'Editor') . '</option>';
		$this->assertTextContains($pattern, $result);

		$pattern = '<option value="general_user" selected="selected">' . sprintf($format, 'General user') . '</option>';
		$this->assertTextContains($pattern, $result);

		$pattern = '<option value="visitor">' . sprintf($format, 'Visitor') . '</option>';
		$this->assertTextContains($pattern, $result);
	}

}
