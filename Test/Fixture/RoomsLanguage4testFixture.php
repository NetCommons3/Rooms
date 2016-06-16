<?php
/**
 * RoomsLanguage4testFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsLanguageFixture', 'Rooms.Test/Fixture');

/**
 * RoomsLanguage4testFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Fixture
 */
class RoomsLanguage4testFixture extends RoomsLanguageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'RoomsLanguage';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'rooms_languages';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//パブリックスペース
		//--日本語
		array('id' => '1', 'language_id' => '2', 'room_id' => '1', 'name' => 'パブリックスペース'),
		//--英語
		array('id' => '2', 'language_id' => '1', 'room_id' => '1', 'name' => 'Public space'),
		//プライベートスペース
		//--日本語
		array('id' => '3', 'language_id' => '2', 'room_id' => '2', 'name' => 'プライベートスペース'),
		//--英語
		array('id' => '4', 'language_id' => '1', 'room_id' => '2', 'name' => 'Private space'),
		//コミュニティスペース
		//--日本語
		array('id' => '5', 'language_id' => '2', 'room_id' => '3', 'name' => 'コミュニティスペース'),
		//--英語
		array('id' => '6', 'language_id' => '1', 'room_id' => '3', 'name' => 'Group space'),

		//パブリックスペース、別ルーム(room_id=4)
		//--日本語
		array('id' => '7', 'language_id' => '2', 'room_id' => '4', 'name' => 'サブルーム１'),
		//--英語
		array('id' => '8', 'language_id' => '1', 'room_id' => '4', 'name' => 'Sub room 1'),
		//パブリックスペース、別ルーム(room_id=5、ブロックなし)
		//--日本語
		array('id' => '9', 'language_id' => '2', 'room_id' => '5', 'name' => 'サブルーム２'),
		//--英語
		array('id' => '10', 'language_id' => '1', 'room_id' => '5', 'name' => 'Sub room 2'),
		//コミュニティスペース、別ルーム(room_id=6, 準備中)
		//--日本語
		array('id' => '11', 'language_id' => '2', 'room_id' => '6', 'name' => 'ルーム１'),
		//--英語
		array('id' => '12', 'language_id' => '1', 'room_id' => '6', 'name' => 'Room 1'),
		//パブリックスペース、別ルーム(room_id=7, プライベートルーム)
		//--日本語
		array('id' => '13', 'language_id' => '2', 'room_id' => '7', 'name' => 'プライベート'),
		//--英語
		array('id' => '14', 'language_id' => '1', 'room_id' => '7', 'name' => 'Private room'),
		//パブリックスペース、サブサブルーム(room_id=8)
		//--日本語
		array('id' => '15', 'language_id' => '2', 'room_id' => '8', 'name' => 'サブサブルーム１'),
		//--英語
		array('id' => '16', 'language_id' => '1', 'room_id' => '8', 'name' => 'Sub Sub room 1'),
	);

}
