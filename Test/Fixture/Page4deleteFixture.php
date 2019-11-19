<?php
/**
 * 削除用Fixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PageFixture', 'Blocks.Test/Fixture');

/**
 * 削除用Fixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Fixture
 */
class Page4deleteFixture extends PageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Page';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'pages';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//削除されないデータ
		array(
			'id' => '1',
			'room_id' => '2',
			'parent_id' => 1,
			'root_id' => 1,
			//'lft' => '1',
			//'rght' => '2',
			'weight' => '1',
			'sort_key' => '~00000001',
			'child_count' => '0',
			'permalink' => '',
			'slug' => null,
			'is_container_fluid' => true,
		),
		//削除対象のデータ
		array(
			'id' => '2',
			'room_id' => '5',
			'parent_id' => 1,
			'root_id' => 1,
			//'lft' => '3',
			//'rght' => '4',
			'weight' => '2',
			'sort_key' => '~00000002',
			'child_count' => '1',
			'permalink' => 'delete_page_1',
			'slug' => 'delete_page_1',
			'is_container_fluid' => true,
		),
		array(
			'id' => '3',
			'room_id' => '5',
			'parent_id' => 2,
			'root_id' => 1,
			//'lft' => '5',
			//'rght' => '6',
			'weight' => '1',
			'sort_key' => '~00000002-00000001',
			'child_count' => '0',
			'permalink' => 'delete_page_2',
			'slug' => 'delete_page_2',
			'is_container_fluid' => true,
		),
	);

}
