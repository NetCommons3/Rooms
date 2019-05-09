<?php
/**
 * Room Model
 *
 * @property Space $Space
 * @property Room $ParentRoom
 * @property Room $ChildRoom
 * @property Language $Language
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppModel', 'Rooms.Model');
App::uses('Role', 'Roles.Model');
App::uses('Space', 'Rooms.Model');
App::uses('BlockSettingBehavior', 'Blocks.Model/Behavior');

/**
 * Room Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Room extends RoomsAppModel {

/**
 * TreeParser
 * __constructでセットする
 *
 * @var array
 */
	public static $treeParser;

/**
 * デフォルトロールキー
 *
 * @var array
 */
	public static $defaultRoleKeyList = array(
		Role::ROOM_ROLE_KEY_EDITOR,
		Role::ROOM_ROLE_KEY_GENERAL_USER,
		Role::ROOM_ROLE_KEY_VISITOR,
	);

/**
 * スペースルームIDのリスト
 * __constructでセットする
 *
 * @var array
 */
	public static $spaceRooms = array();

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'PrivateSpace.PrivateSpace',
		'Rooms.DeleteRoomAssociations',
		'Rooms.Room',
		'Rooms.SaveRoomAssociations',
		//'Tree',
		'NetCommons.NetCommonsTree',
	);

/**
 * 削除の子ルームID
 * beforeDeleteで取得し、aftereDeleteで使用する
 *
 * @var array
 */
	protected $_childRoomIds = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Space' => array(
			'className' => 'Rooms.Space',
			'foreignKey' => 'space_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentRoom' => array(
			'className' => 'Rooms.Room',
			'foreignKey' => 'parent_id',
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
	public $hasMany = array(
		'ChildRoom' => array(
			'className' => 'Rooms.Room',
			'foreignKey' => 'parent_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'RoomsLanguage' => array(
			'className' => 'Rooms.RoomsLanguage',
			'foreignKey' => 'room_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		self::$treeParser = chr(9);
	}

/**
 * スペースのルームIDのリストを取得
 *
 * @return array
 */
	public static function getSpaceRooms() {
		if (! self::$spaceRooms) {
			self::$spaceRooms = array(
				Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID, 'Room'),
				Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID, 'Room'),
				Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID, 'Room'),
			);
		}
		return self::$spaceRooms;
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = ValidateMerge::merge($this->validate, array(
			'space_id' => array(
				'numeric' => array(
					'rule' => array('numeric'), 'required' => true,
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'page_id_top' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'on' => 'update', // Limit validation to 'create' or 'update' operations
				),
			),
			'active' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'need_approval' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'default_participation' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'page_layout_permitted' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			//TreeBehaviorで使用
			'parent_id' => array(
				'numeric' => array(
					'rule' => array('numeric'), 'required' => false,
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => true,
					'on' => 'update', // Limit validation to 'create' or 'update' operations
				),
			),
			//'lft' => array(
			//	'numeric' => array(
			//		'rule' => array('numeric'), 'required' => false,
			//		'message' => __d('net_commons', 'Invalid request.'),
			//		'on' => 'update', // Limit validation to 'create' or 'update' operations
			//	),
			//),
			//'rght' => array(
			//	'numeric' => array(
			//		'rule' => array('numeric'), 'required' => false,
			//		'message' => __d('net_commons', 'Invalid request.'),
			//		'on' => 'update', // Limit validation to 'create' or 'update' operations
			//	),
			//),
		));

		if (Hash::get($this->data, 'Room.space_id') === Space::PRIVATE_SPACE_ID) {
			$this->validate['default_role_key'] = array(
				'inList' => array(
					'rule' => array('inList', [Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR]),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true
				),
			);
		} else {
			$this->validate['default_role_key'] = array(
				'inList' => array(
					'rule' => array('inList', self::$defaultRoleKeyList),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true
				),
			);
		}

		// * RoomsLanguageのバリデーション
		if (isset($this->data['RoomsLanguage'])) {
			$roomsLanguages = $this->data['RoomsLanguage'];
			if (! $this->RoomsLanguage->validateMany($roomsLanguages)) {
				$this->validationErrors = Hash::merge(
					$this->validationErrors, $this->RoomsLanguage->validationErrors
				);
				return false;
			}
		}
		// * RoomRolePermissionのバリデーション
		if (isset($this->data['RoomRolePermission'])) {
			$this->loadModels(array('RoomRolePermission' => 'Rooms.RoomRolePermission'));
			foreach ($this->data[$this->RoomRolePermission->alias] as $permission => $data) {
				$data = Hash::insert($data, '{s}.permission', $permission);
				if (! $this->RoomRolePermission->validateMany($data)) {
					$this->validationErrors = Hash::merge(
						$this->validationErrors, $this->RoomRolePermission->validationErrors
					);
					return false;
				}
			}
		}

		return parent::beforeValidate($options);
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 * @throws InternalErrorException
 */
	public function beforeSave($options = array()) {
		$room = Hash::get($this->data, 'Room');

		if (isset($room['in_draft'])) {
			$inDraft = $room['in_draft'];
		} else {
			$inDraft = null;
		}
		if (isset($room['default_participation'])) {
			$defaultPart = $room['default_participation'];
		} else {
			$defaultPart = null;
		}
		if (isset($options['preUpdate']['Room']['default_participation'])) {
			$preDefaultPart = $options['preUpdate']['Room']['default_participation'];
		} else {
			$preDefaultPart = null;
		}

		if (isset($room['id']) &&
				$inDraft &&
				$defaultPart !== $preDefaultPart) {
			$this->loadModels([
				'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			]);

			$conditions = array($this->RolesRoomsUser->alias . '.room_id' => $room['id']);
			if (! $this->RolesRoomsUser->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created 作成フラグ
 * @param array $options Model::save()のoptions.
 * @return void
 * @throws InternalErrorException
 * @link http://book.cakephp.org/2.0/ja/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = array()) {
		//RoomsLanguage登録
		if (isset($this->data['RoomsLanguage'])) {
			$roomsLanguages = Hash::insert(
				$this->data['RoomsLanguage'], '{n}.room_id', $this->data['Room']['id']
			);
			foreach ($roomsLanguages as $index => $roomsLanguage) {
				$this->RoomsLanguage->create(false);
				if (! $result = $this->RoomsLanguage->save($roomsLanguage, false, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				$this->data['RoomsLanguage'][$index] = $result['RoomsLanguage'];
			}
		}

		//デフォルトデータ登録処理
		$this->saveDefaultAssociations($created, $options);

		//パーミッションデータ登録処理
		if (isset($this->data['RoomRolePermission'])) {
			$this->loadModels([
				'RoomRolePermission' => 'Rooms.RoomRolePermission'
			]);
			$this->data['RoomRolePermission'] =
				$this->RoomRolePermission->saveRoomRolePermission($created, $this->data);
		}

		//使用できるプラグインデータの登録
		if (isset($this->data['PluginsRoom'])) {
			$this->loadModels([
				'PluginsRoom' => 'PluginManager.PluginsRoom'
			]);

			//エラーの場合、throwになる
			$this->PluginsRoom->savePluginsRoomsByRoomId(
				$this->data['Room']['id'],
				$this->data['PluginsRoom']['plugin_key']
			);
		}

		//ルーム承認する場合、BlockSettingの use_workflow, use_comment_approval を 1 に更新
		$needApproval = Hash::get($this->data, 'Room.need_approval');
		if ($needApproval) {
			$this->loadModels([
				'BlockSetting' => 'Blocks.BlockSetting'
			]);
			$fields = array('BlockSetting.value' => '1');
			$conditions = array(
				'BlockSetting.field_name' => array(
					BlockSettingBehavior::FIELD_USE_WORKFLOW,
					BlockSettingBehavior::FIELD_USE_COMMENT_APPROVAL,
				),
				'BlockSetting.room_id' => $this->data['Room']['id'],
			);
			if (! $this->BlockSetting->updateAll($fields, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		parent::afterSave($created, $options);
	}

/**
 * Called before every deletion operation.
 *
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforedelete
 * @throws InternalErrorException
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function beforeDelete($cascade = true) {
		$children = $this->children($this->id, false, 'Room.id', 'Room.rght');
		$this->_childRoomIds = Hash::extract($children, '{n}.Room.id');
		$deleteRoomIds = $this->_childRoomIds;
		$deleteRoomIds[] = $this->id;

		foreach ($deleteRoomIds as $childRoomId) {
			//frameデータの削除
			$this->deleteFramesByRoom($childRoomId);

			//pageデータの削除
			$this->deletePagesByRoom($childRoomId);

			//blockデータの削除
			$this->deleteBlocksByRoom($childRoomId);
		}

		return parent::beforeDelete($cascade);
	}

/**
 * Called after every deletion operation.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterdelete
 * @throws InternalErrorException
 */
	public function afterDelete() {
		$deleteRoomIds = $this->_childRoomIds;
		$deleteRoomIds[] = $this->id;

		//子Roomデータの削除
		if (! $this->deleteAll(array($this->alias . '.id' => $this->_childRoomIds), false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		//Roomの関連データの削除
		foreach ($deleteRoomIds as $childRoomId) {
			$this->deleteRoomAssociations($childRoomId);
		}
	}

/**
 * ルームの登録処理
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveRoom($data) {
		$this->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
			'RoomsLanguage' => 'Rooms.RoomsLanguage',
		]);

		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			$this->rollback();
			return false;
		}

		if (Hash::get($data, 'Room.id')) {
			$preUpdate = $this->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => Hash::get($data, 'Room.id'))
			));
		} else {
			$preUpdate = null;
		}

		try {
			//登録処理
			$room = $this->save(null, ['validate' => false, 'preUpdate' => $preUpdate]);
			if (! $room) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			if (Hash::get($room, 'Room.page_id_top')) {
				$this->savePageLanguage($room);
			}

			if (Hash::get($room, 'Room.id') === Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID)) {
				$this->savePrivateSpaceRoom($room);
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return $room;
	}

/**
 * 状態の登録処理
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveActive($data) {
		//トランザクションBegin
		$this->begin();

		try {
			//登録処理
			$this->id = $data['Room']['id'];
			if (! $this->saveField('active', (bool)$data['Room']['active'], ['callbacks' => false])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * テーマの登録処理
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function saveTheme($data) {
		//トランザクションBegin
		$this->begin();

		try {
			//登録処理
			$this->id = $data['Room']['id'];
			if (! $this->saveField('theme', $data['Room']['theme'], array('callbacks' => false))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * 移動
 *
 * @param array $room 移動させるルームデータ room_id, page_id_topは必須
 * @param string $moveMethod 移動名（そのまま関数名 moveUp|moveDownのいずれか）
 * @param int $moveStep 移動数
 * @return bool
 * @throws InternalErrorException
 */
	public function saveMove($room, $moveMethod, $moveStep = 1) {
		//トランザクションBegin
		$this->begin();

		try {
			$result = $this->$moveMethod($room['Room']['id'], $moveStep);
			if (!$result) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->loadModels(array('Page' => 'Pages.Page'));
			$result = $this->Page->$moveMethod($room['Room']['page_id_top'], $moveStep);
			if (!$result) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();

		} catch (Exception $ex) {
			$this->rollback($ex);
		}

		return true;
	}

/**
 * ルームの削除処理
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function deleteRoom($data) {
		$this->loadModels([
			'RoomsLanguage' => 'Rooms.RoomsLanguage',
		]);

		//トランザクションBegin
		$this->begin();

		try {
			//Roomデータの削除
			if (! $this->delete($data['Room']['id'], false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

}
