<?php
/**
 * RoomsLanguageFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * RoomsLanguageFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Fixture
 */
class RoomsLanguageFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'language_id' => 1,
			'room_id' => '2',
			'name' => 'Room name',
			'created_user' => 1,
			'created' => '2015-08-04 07:59:41',
			'modified_user' => 1,
			'modified' => '2015-08-04 07:59:41'
		),
		array(
			'id' => 2,
			'language_id' => 2,
			'room_id' => '2',
			'name' => 'Room name',
			'created_user' => 1,
			'created' => '2015-08-04 07:59:41',
			'modified_user' => 1,
			'modified' => '2015-08-04 07:59:41'
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Rooms') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new RoomsSchema())->tables['ooms_languages'];
		parent::init();
	}

}
