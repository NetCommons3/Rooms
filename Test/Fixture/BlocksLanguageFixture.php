<?php
/**
 * BlocksLanguageFixture
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for BlocksLanguageFixture
 */
class BlocksLanguageFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('connection' => 'master');

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'block_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'block_id' => '1',
			'language_id' => '1',
			'name' => 'Test001',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '2',
			'block_id' => '1',
			'language_id' => '2',
			'name' => 'テスト001',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '3',
			'block_id' => '2',
			'language_id' => '1',
			'name' => 'Test002',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '4',
			'block_id' => '2',
			'language_id' => '2',
			'name' => 'テスト002',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '5',
			'block_id' => '3',
			'language_id' => '1',
			'name' => 'Test003',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '6',
			'block_id' => '3',
			'language_id' => '2',
			'name' => 'テスト003',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '7',
			'block_id' => '4',
			'language_id' => '1',
			'name' => 'Test004',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '8',
			'block_id' => '4',
			'language_id' => '2',
			'name' => 'テスト004',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '9',
			'block_id' => '5',
			'language_id' => '1',
			'name' => 'Test005',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
		array(
			'id' => '10',
			'block_id' => '5',
			'language_id' => '2',
			'name' => 'テスト005',
			'created_user_id' => null,
			'created' => '2014-08-24 23:35:05',
			'modified_user_id' => null,
			'modified' => '2014-08-24 23:35:05'
		),
	);

}
