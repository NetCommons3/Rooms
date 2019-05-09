<?php
/**
 * RoomRolePermission Model
 *
 * @property RolesRoom $RolesRoom
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppModel', 'Rooms.Model');

/**
 * RoomRolePermission Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 */
class RoomRolePermission extends RoomsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'RolesRoom' => array(
			'className' => 'Rooms.RolesRoom',
			'foreignKey' => 'roles_room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = ValidateMerge::merge($this->validate, array(
			'roles_room_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'on' => 'update', // Limit validation to 'create' or 'update' operations
				),
			),
			'permission' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
					//'on' => 'update', // Limit validation to 'create' or 'update' operations
				),
			),
			'value' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * パーミッションデータ登録処理
 *
 * @param bool $created 作成フラグ
 * @param array $room ルーム
 * @return array
 * @throws InternalErrorException
 */
	public function saveRoomRolePermission($created, $room) {
		if ($created) {
			$roomRolePermissions = $this->find('all', array(
				'recursive' => 0,
				'conditions' => array(
					'RolesRoom.room_id' => $room['Room']['id'],
					'RoomRolePermission.permission' => array_keys($room['RoomRolePermission'])
				)
			));
			$roomRolePermissions = Hash::combine($roomRolePermissions,
				'{n}.RolesRoom.role_key',
				'{n}.RoomRolePermission',
				'{n}.RoomRolePermission.permission'
			);
			$room['RoomRolePermission'] = Hash::remove($room['RoomRolePermission'], '{s}.{s}.id');
			$room['RoomRolePermission'] = Hash::merge(
				$roomRolePermissions, $room['RoomRolePermission']
			);
		}

		foreach ($room['RoomRolePermission'] as $permission) {
			if (! $this->saveMany($permission, ['validate' => false])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return $room['RoomRolePermission'];
	}
}
