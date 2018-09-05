<?php
/**
 * RoomFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * RoomFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Fixture
 */
class RoomFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//サイト全体
		array(
			'id' => '1',
			'space_id' => '1',
			'page_id_top' => null,
			'parent_id' => null,
			//'lft' => '1',
			//'rght' => '12',
			'weight' => '1',
			'sort_key' => '~00000001',
			'child_count' => '5',
			'active' => '1',
			'default_role_key' => 'visitor',
			'need_approval' => '1',
			'default_participation' => '1',
			'page_layout_permitted' => '0',
			'theme' => null,
		),
		//パブリックスペース
		array(
			'id' => '2',
			'space_id' => '2',
			'page_id_top' => '1',
			'parent_id' => '1',
			//'lft' => '2',
			//'rght' => '7',
			'weight' => '1',
			'sort_key' => '~00000001-00000001',
			'child_count' => '2',
			'active' => true,
			'default_role_key' => 'visitor',
			'need_approval' => true,
			'default_participation' => true,
			'page_layout_permitted' => true,
			'theme' => null,
		),
		//別ルーム(room_id=5)
		array(
			'id' => '5',
			'space_id' => '2',
			'page_id_top' => '4',
			'parent_id' => '2',
			//'lft' => '3',
			//'rght' => '4',
			'weight' => '1',
			'sort_key' => '~00000001-00000001-00000001',
			'child_count' => '0',
			'active' => true,
			'default_role_key' => 'visitor',
			'need_approval' => true,
			'default_participation' => true,
			'page_layout_permitted' => true,
			'theme' => null,
		),
		//別ルーム(room_id=6、ブロックなし)
		array(
			'id' => '6',
			'space_id' => '2',
			'page_id_top' => '5',
			'parent_id' => '2',
			//'lft' => '5',
			//'rght' => '6',
			'weight' => '2',
			'sort_key' => '~00000001-00000001-00000002',
			'child_count' => '0',
			'active' => true,
			'default_role_key' => 'visitor',
			'need_approval' => true,
			'default_participation' => true,
			'page_layout_permitted' => true,
			'theme' => null,
		),
		//プライベート
		array(
			'id' => '3',
			'space_id' => '3',
			'page_id_top' => null,
			'parent_id' => '1',
			//'lft' => '8',
			//'rght' => '9',
			'weight' => '2',
			'sort_key' => '~00000001-00000002',
			'child_count' => '0',
			'active' => true,
			'default_role_key' => 'room_administrator',
			'need_approval' => false,
			'default_participation' => false,
			'page_layout_permitted' => false,
			'theme' => null,
		),
		//コミュニティスペース
		array(
			'id' => '4',
			'space_id' => '4',
			'page_id_top' => null,
			'parent_id' => '1',
			//'lft' => '10',
			//'rght' => '11',
			'weight' => '3',
			'sort_key' => '~00000001-00000003',
			'child_count' => '0',
			'active' => true,
			'default_role_key' => 'general_user',
			'need_approval' => true,
			'default_participation' => true,
			'page_layout_permitted' => true,
			'theme' => null,
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Rooms') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new RoomsSchema())->tables['rooms'];
		parent::init();
	}

}
