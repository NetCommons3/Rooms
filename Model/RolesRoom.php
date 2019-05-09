<?php
/**
 * RolesRoom Model
 *
 * @property Room $Room
 * @property BlockRolePermission $BlockRolePermission
 * @property RoomRolePermission $RoomRolePermission
 * @property User $User
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppModel', 'Rooms.Model');

/**
 * RolesRoom Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 */
class RolesRoom extends RoomsAppModel {

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
		'Room' => array(
			'className' => 'Rooms.Room',
			'foreignKey' => 'room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	//public $hasMany = array(
	//	'BlockRolePermission' => array(
	//		'className' => 'Blocks.BlockRolePermission',
	//		'foreignKey' => 'roles_room_id',
	//		'dependent' => false,
	//		'conditions' => '',
	//		'fields' => '',
	//		'order' => '',
	//		'limit' => '',
	//		'offset' => '',
	//		'exclusive' => '',
	//		'finderQuery' => '',
	//		'counterQuery' => ''
	//	),
	//	'RoomRolePermission' => array(
	//		'className' => 'Rooms.RoomRolePermission',
	//		'foreignKey' => 'roles_room_id',
	//		'dependent' => false,
	//		'conditions' => '',
	//		'fields' => '',
	//		'order' => '',
	//		'limit' => '',
	//		'offset' => '',
	//		'exclusive' => '',
	//		'finderQuery' => '',
	//		'counterQuery' => ''
	//	)
	//);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	//public $hasAndBelongsToMany = array(
	//	'User' => array(
	//		'className' => 'Users.User',
	//		'joinTable' => 'roles_rooms_users',
	//		'foreignKey' => 'roles_room_id',
	//		'associationForeignKey' => 'user_id',
	//		'unique' => 'keepExisting',
	//		'conditions' => '',
	//		'fields' => '',
	//		'order' => '',
	//		'limit' => '',
	//		'offset' => '',
	//		'finderQuery' => '',
	//	)
	//);

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
			'room_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
			'role_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
		));

		return parent::beforeValidate($options);
	}

}
