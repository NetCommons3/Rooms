<?php
/**
 * RoomRolePermission4testFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomRolePermissionFixture', 'Rooms.Test/Fixture');

/**
 * RoomRolePermission4testFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Test\Fixture
 */
class RoomRolePermission4testFixture extends RoomRolePermissionFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'RoomRolePermission';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'room_role_permissions';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		////パブリックスペース
		////--ルーム管理者
		//array('id' => '1', 'roles_room_id' => '1', 'permission' => 'block_editable', 'value' => '1'),
		array('id' => '2', 'roles_room_id' => '1', 'permission' => 'content_comment_creatable', 'value' => '1'),
		//array('id' => '3', 'roles_room_id' => '1', 'permission' => 'content_comment_editable', 'value' => '1'),
		//array('id' => '4', 'roles_room_id' => '1', 'permission' => 'content_comment_publishable', 'value' => '1'),
		//array('id' => '5', 'roles_room_id' => '1', 'permission' => 'content_creatable', 'value' => '1'),
		//array('id' => '6', 'roles_room_id' => '1', 'permission' => 'content_editable', 'value' => '1'),
		array('id' => '7', 'roles_room_id' => '1', 'permission' => 'content_publishable', 'value' => '1'),
		//array('id' => '8', 'roles_room_id' => '1', 'permission' => 'content_readable', 'value' => '1'),
		//array('id' => '9', 'roles_room_id' => '1', 'permission' => 'page_editable', 'value' => '1'),
		//array('id' => '10', 'roles_room_id' => '1', 'permission' => 'html_not_limited', 'value' => '1'),
		//array('id' => '11', 'roles_room_id' => '1', 'permission' => 'mail_content_receivable', 'value' => '1'),
		////--編集長
		//array('id' => '12', 'roles_room_id' => '2', 'permission' => 'block_editable', 'value' => '1'),
		array('id' => '13', 'roles_room_id' => '2', 'permission' => 'content_comment_creatable', 'value' => '1'),
		//array('id' => '14', 'roles_room_id' => '2', 'permission' => 'content_comment_editable', 'value' => '1'),
		//array('id' => '15', 'roles_room_id' => '2', 'permission' => 'content_comment_publishable', 'value' => '1'),
		//array('id' => '16', 'roles_room_id' => '2', 'permission' => 'content_creatable', 'value' => '1'),
		//array('id' => '17', 'roles_room_id' => '2', 'permission' => 'content_editable', 'value' => '1'),
		array('id' => '18', 'roles_room_id' => '2', 'permission' => 'content_publishable', 'value' => '1'),
		//array('id' => '19', 'roles_room_id' => '2', 'permission' => 'content_readable', 'value' => '1'),
		//array('id' => '20', 'roles_room_id' => '2', 'permission' => 'page_editable', 'value' => '1'),
		//array('id' => '21', 'roles_room_id' => '2', 'permission' => 'html_not_limited', 'value' => '0'),
		//array('id' => '22', 'roles_room_id' => '2', 'permission' => 'mail_content_receivable', 'value' => '1'),
		////--編集者
		//array('id' => '23', 'roles_room_id' => '3', 'permission' => 'block_editable', 'value' => '0'),
		array('id' => '24', 'roles_room_id' => '3', 'permission' => 'content_comment_creatable', 'value' => '1'),
		//array('id' => '25', 'roles_room_id' => '3', 'permission' => 'content_comment_editable', 'value' => '1'),
		//array('id' => '26', 'roles_room_id' => '3', 'permission' => 'content_comment_publishable', 'value' => '0'),
		//array('id' => '27', 'roles_room_id' => '3', 'permission' => 'content_creatable', 'value' => '1'),
		//array('id' => '28', 'roles_room_id' => '3', 'permission' => 'content_editable', 'value' => '1'),
		array('id' => '29', 'roles_room_id' => '3', 'permission' => 'content_publishable', 'value' => '0'),
		//array('id' => '30', 'roles_room_id' => '3', 'permission' => 'content_readable', 'value' => '1'),
		//array('id' => '31', 'roles_room_id' => '3', 'permission' => 'page_editable', 'value' => '0'),
		//array('id' => '32', 'roles_room_id' => '3', 'permission' => 'html_not_limited', 'value' => '0'),
		//array('id' => '33', 'roles_room_id' => '3', 'permission' => 'mail_content_receivable', 'value' => '1'),
		////--一般
		//array('id' => '34', 'roles_room_id' => '4', 'permission' => 'block_editable', 'value' => '0'),
		array('id' => '35', 'roles_room_id' => '4', 'permission' => 'content_comment_creatable', 'value' => '1'),
		//array('id' => '36', 'roles_room_id' => '4', 'permission' => 'content_comment_editable', 'value' => '0'),
		//array('id' => '37', 'roles_room_id' => '4', 'permission' => 'content_comment_publishable', 'value' => '0'),
		//array('id' => '38', 'roles_room_id' => '4', 'permission' => 'content_creatable', 'value' => '1'),
		//array('id' => '39', 'roles_room_id' => '4', 'permission' => 'content_editable', 'value' => '0'),
		array('id' => '40', 'roles_room_id' => '4', 'permission' => 'content_publishable', 'value' => '0'),
		//array('id' => '41', 'roles_room_id' => '4', 'permission' => 'content_readable', 'value' => '1'),
		//array('id' => '42', 'roles_room_id' => '4', 'permission' => 'page_editable', 'value' => '0'),
		//array('id' => '43', 'roles_room_id' => '4', 'permission' => 'html_not_limited', 'value' => '0'),
		//array('id' => '44', 'roles_room_id' => '4', 'permission' => 'mail_content_receivable', 'value' => '1'),
		////--ゲスト
		//array('id' => '45', 'roles_room_id' => '5', 'permission' => 'block_editable', 'value' => '0'),
		array('id' => '46', 'roles_room_id' => '5', 'permission' => 'content_comment_creatable', 'value' => '0'),
		//array('id' => '47', 'roles_room_id' => '5', 'permission' => 'content_comment_editable', 'value' => '0'),
		//array('id' => '48', 'roles_room_id' => '5', 'permission' => 'content_comment_publishable', 'value' => '0'),
		//array('id' => '49', 'roles_room_id' => '5', 'permission' => 'content_creatable', 'value' => '0'),
		//array('id' => '50', 'roles_room_id' => '5', 'permission' => 'content_editable', 'value' => '0'),
		array('id' => '51', 'roles_room_id' => '5', 'permission' => 'content_publishable', 'value' => '0'),
		//array('id' => '52', 'roles_room_id' => '5', 'permission' => 'content_readable', 'value' => '1'),
		//array('id' => '53', 'roles_room_id' => '5', 'permission' => 'page_editable', 'value' => '0'),
		//array('id' => '54', 'roles_room_id' => '5', 'permission' => 'html_not_limited', 'value' => '0'),
		//array('id' => '55', 'roles_room_id' => '5', 'permission' => 'mail_content_receivable', 'value' => '0'),
		//パブリックスペース、別ルーム(room_id=4)
		//--ルーム管理者
		//array('id' => '56', 'roles_room_id' => '6', 'permission' => 'block_editable', 'value' => '1'),
		array('id' => '57', 'roles_room_id' => '6', 'permission' => 'content_comment_creatable', 'value' => '1'),
		//array('id' => '58', 'roles_room_id' => '6', 'permission' => 'content_comment_editable', 'value' => '1'),
		//array('id' => '59', 'roles_room_id' => '6', 'permission' => 'content_comment_publishable', 'value' => '1'),
		//array('id' => '60', 'roles_room_id' => '6', 'permission' => 'content_creatable', 'value' => '1'),
		//array('id' => '61', 'roles_room_id' => '6', 'permission' => 'content_editable', 'value' => '1'),
		array('id' => '62', 'roles_room_id' => '6', 'permission' => 'content_publishable', 'value' => '1'),
		//array('id' => '63', 'roles_room_id' => '6', 'permission' => 'content_readable', 'value' => '1'),
		//array('id' => '64', 'roles_room_id' => '6', 'permission' => 'page_editable', 'value' => '1'),
		array('id' => '65', 'roles_room_id' => '6', 'permission' => 'html_not_limited', 'value' => '1'),
		//array('id' => '66', 'roles_room_id' => '6', 'permission' => 'mail_content_receivable', 'value' => '1'),
		//--編集長
		array('id' => '67', 'roles_room_id' => '11', 'permission' => 'content_publishable', 'value' => '1'),
		array('id' => '68', 'roles_room_id' => '11', 'permission' => 'html_not_limited', 'value' => '0'),
		//--編集者
		array('id' => '69', 'roles_room_id' => '12', 'permission' => 'content_publishable', 'value' => '0'),
		array('id' => '70', 'roles_room_id' => '12', 'permission' => 'html_not_limited', 'value' => '0'),
		//--一般
		array('id' => '71', 'roles_room_id' => '13', 'permission' => 'content_publishable', 'value' => '0'),
		array('id' => '72', 'roles_room_id' => '13', 'permission' => 'html_not_limited', 'value' => '0'),
		//--ゲスト
		array('id' => '73', 'roles_room_id' => '14', 'permission' => 'content_publishable', 'value' => '0'),
		array('id' => '74', 'roles_room_id' => '14', 'permission' => 'html_not_limited', 'value' => '0'),
		//プライベートスペース
		//--ルーム管理者
		array('id' => '75', 'roles_room_id' => '20', 'permission' => 'block_editable', 'value' => '1'),
		array('id' => '76', 'roles_room_id' => '20', 'permission' => 'content_comment_creatable', 'value' => '1'),
		array('id' => '77', 'roles_room_id' => '20', 'permission' => 'content_comment_editable', 'value' => '1'),
		array('id' => '78', 'roles_room_id' => '20', 'permission' => 'content_comment_publishable', 'value' => '1'),
		array('id' => '79', 'roles_room_id' => '20', 'permission' => 'content_creatable', 'value' => '1'),
		array('id' => '80', 'roles_room_id' => '20', 'permission' => 'content_editable', 'value' => '1'),
		array('id' => '81', 'roles_room_id' => '20', 'permission' => 'content_publishable', 'value' => '1'),
		array('id' => '82', 'roles_room_id' => '20', 'permission' => 'content_readable', 'value' => '1'),
	);

}
