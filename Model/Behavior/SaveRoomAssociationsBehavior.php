<?php
/**
 * SaveRoomAssociations Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('Space', 'Rooms.Model');
App::uses('Page', 'Pages.Model');

/**
 * SaveRoomAssociations Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model\Behavior
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class SaveRoomAssociationsBehavior extends ModelBehavior {

/**
 * 関連テーブルの初期値の登録処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param bool $created 作成フラグ
 * @param array $options Model::save()のoptions.
 * @return void
 */
	public function saveDefaultAssociations(Model $model, $created, $options) {
		//デフォルトデータ登録処理
		$room = $model->data;
		if ($created) {
			$model->saveDefaultRolesRoom($room);
			$model->saveDefaultRoomRolePermission($room);
		}

		if (isset($room['Room']['in_draft'])) {
			$inDraft = $room['Room']['in_draft'];
		} else {
			$inDraft = null;
		}
		if (isset($options['preUpdate']['Room']['in_draft'])) {
			$inDraftByPreUpdate = $options['preUpdate']['Room']['in_draft'];
		} else {
			$inDraftByPreUpdate = null;
		}

		if ($created || $inDraft) {
			$model->saveDefaultRolesRoomsUser($room, true);
			$model->saveDefaultRolesPluginsRoom($room);
		}

		if (! $inDraft && ($created || $inDraftByPreUpdate)) {
			$page = $model->saveDefaultPage($room);
			$model->data = Hash::merge($room, $page);
		}
	}

/**
 * RolesRoomのデフォルトデータ登録処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $data Room data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function saveDefaultRolesRoom(Model $model, $data) {
		$model->loadModels([
			'Role' => 'Roles.Role',
			'RolesRoom' => 'Rooms.RolesRoom',
		]);
		$db = $model->getDataSource();

		//多数のデータを一括で登録するためINSERT INTO ... SELECTを使う。
		//--クエリの生成
		$tableName = $model->tablePrefix . $model->RolesRoom->table;
		$values = array(
			'room_id' => $db->value($data['Room']['id'], 'string'),
			'role_key' => $model->RolesRoom->escapeField('role_key'),
			'created' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'created_user' => $db->value(Current::read('User.id'), 'string'),
			'modified' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'modified_user' => $db->value(Current::read('User.id'), 'string'),
		);
		$joins = array(
			$model->tablePrefix . $model->RolesRoom->table . ' AS ' . $model->RolesRoom->alias => null,
		);
		$wheres = array(
			$model->RolesRoom->escapeField('room_id') . ' = ' .
					$db->value($data['Room']['parent_id'], 'string'),
		);

		//--クエリの実行
		$sql = $this->__insertSql(
			$tableName, array_keys($values), array_values($values), $joins, $wheres
		);
		$model->RolesRoom->query($sql);
		$result = $model->RolesRoom->getAffectedRows() > 0;
		if (! $result) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		return true;
	}

/**
 * RolesRoomsUserのデフォルトデータ登録処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $data Room data
 * @param bool $isRoomCreate ルーム作成時かどうか。trueの場合、ルーム作成時に呼ばれ、falseの場合、ユーザ作成時に呼ばれる
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function saveDefaultRolesRoomsUser(Model $model, $data, $isRoomCreate) {
		$model->loadModels([
			'Role' => 'Roles.Role',
			'RolesRoom' => 'Rooms.RolesRoom',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'User' => 'Users.User',
			'UserRoleSetting' => 'UserRoles.UserRoleSetting',
		]);
		$db = $model->getDataSource();

		if (isset($data['RolesRoomsUser']['user_id'])) {
			$userId = $data['RolesRoomsUser']['user_id'];
		} else {
			$userId = Current::read('User.id');
		}

		if ($isRoomCreate) {
			//ルーム作成者をRolesRoomsUsersのルーム管理者で登録する
			$roleKey = Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR;
		} else {
			$roleKey = $data['Room']['default_role_key'];
		}
		$rolesRoom = $model->RolesRoom->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => $data['Room']['id'],
				'role_key' => $roleKey,
			)
		));
		$rolesRoomsUser = array(
			'id' => null,
			'roles_room_id' => $rolesRoom['RolesRoom']['id'],
			'room_id' => $data['Room']['id'],
			'user_id' => $userId
		);
		$model->RolesRoomsUser->create(null);
		if (! $model->RolesRoomsUser->save($rolesRoomsUser)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		if (! $data['Room']['default_participation'] || ! $isRoomCreate) {
			return true;
		}

		//$count = $model->User->find('count', array(
		//	'recursive' => -1,
		//	'conditions' => array(
		//		'id !=' => $userId,
		//	)
		//));
		//if (! $count) {
		//	return true;
		//}

		//多数のデータを一括で登録するためINSERT INTO ... SELECTを使う。
		//--デフォルトのロールを取得
		$rolesRoom = $model->RolesRoom->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => $data['Room']['id'],
				'role_key' => $data['Room']['default_role_key'],
			)
		));
		//--クエリの生成
		$tableName = $model->tablePrefix . $model->RolesRoomsUser->table;
		$values = array(
			'roles_room_id' => $db->value($rolesRoom['RolesRoom']['id'], 'string'),
			'user_id' => $model->RolesRoomsUser->escapeField('user_id'),
			'room_id' => $db->value($data['Room']['id'], 'string'),
			'created' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'created_user' => $db->value(Current::read('User.id'), 'string'),
			'modified' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'modified_user' => $db->value(Current::read('User.id'), 'string'),
		);

		$parentRoomId = $data['Room']['parent_id'];
		$joins = array(
			$model->tablePrefix . $model->RolesRoomsUser->table . ' AS ' .
													$model->RolesRoomsUser->alias => null,
		);
		$wheres = array(
			$model->RolesRoomsUser->escapeField('room_id') . ' = ' . $db->value($parentRoomId, 'string'),
			$model->RolesRoomsUser->escapeField('user_id') . ' != ' . $db->value($userId, 'string'),
		);

		//--クエリの実行
		$sql = $this->__insertSql(
			$tableName, array_keys($values), array_values($values), $joins, $wheres
		);
		$model->RolesRoomsUser->query($sql);
		//$result = $model->RolesRoomsUser->getAffectedRows() > 0;
		//if (! $result) {
		//	throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		//}

		return true;
	}

/**
 * RolesPluginsRoomのデフォルトデータ登録処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $data Room data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function saveDefaultRolesPluginsRoom(Model $model, $data) {
		$model->loadModels([
			'RolesRoom' => 'Rooms.RolesRoom',
			'PluginsRoom' => 'PluginManager.PluginsRoom',
			'Space' => 'Rooms.Space',
		]);

		$space = $model->Space->getSpace($data['Room']['space_id']);
		if (! empty($space['after_user_save_model'])) {
			return true;
		}

		$db = $model->getDataSource();

		//多数のデータを一括で登録するためINSERT INTO ... SELECTを使う。
		//--クエリの生成
		$tableName = $model->tablePrefix . $model->PluginsRoom->table;
		$values = array(
			'room_id' => $db->value($data['Room']['id'], 'string'),
			'plugin_key' => $model->PluginsRoom->escapeField('plugin_key'),
			'created' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'created_user' => $db->value(Current::read('User.id'), 'string'),
			'modified' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'modified_user' => $db->value(Current::read('User.id'), 'string'),
		);
		$joins = array(
			$model->tablePrefix . $model->PluginsRoom->table . ' AS ' . $model->PluginsRoom->alias => null,
		);
		$wheres = array(
			$model->PluginsRoom->escapeField('room_id') . ' = ' .
						$db->value($data['Room']['parent_id'], 'string'),
		);

		//--クエリの実行
		$sql = $this->__insertSql(
			$tableName, array_keys($values), array_values($values), $joins, $wheres
		);
		$model->PluginsRoom->query($sql);
		$result = $model->PluginsRoom->getAffectedRows() > 0;
		if (! $result) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		return true;
	}

/**
 * RoomRolePermissionのデフォルトデータ登録処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $data Room data
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function saveDefaultRoomRolePermission(Model $model, $data) {
		$model->loadModels([
			'Role' => 'Roles.Role',
			'DefaultRolePermission' => 'Roles.DefaultRolePermission',
			'RolesRoom' => 'Rooms.RolesRoom',
			'RoomRolePermission' => 'Rooms.RoomRolePermission',
		]);
		$db = $model->getDataSource();

		//多数のデータを一括で登録するためINSERT INTO ... SELECTを使う。
		//--クエリの生成
		$tableName = $model->tablePrefix . $model->RoomRolePermission->table;
		$values = array(
			'roles_room_id' => $model->RolesRoom->escapeField('id'),
			'permission' => $model->DefaultRolePermission->escapeField('permission'),
			'value' => $model->DefaultRolePermission->escapeField('value'),
			'created' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'created_user' => $db->value(Current::read('User.id'), 'string'),
			'modified' => $db->value(date('Y-m-d H:i:s'), 'string'),
			'modified_user' => $db->value(Current::read('User.id'), 'string'),
		);

		$joins = array();
		$joins[$model->tablePrefix . $model->Role->table . ' AS ' . $model->Role->alias] = null;
		$key = $model->tablePrefix . $model->RolesRoom->table . ' AS ' . $model->RolesRoom->alias;
		$joins[$key] = array(
			$model->Role->escapeField('key') . ' = ' . $model->RolesRoom->escapeField('role_key')
		);
		$key = $model->tablePrefix . $model->DefaultRolePermission->table . ' AS ' .
					$model->DefaultRolePermission->alias;
		$joins[$key] = array(
			$model->Role->escapeField('key') . ' = ' . $model->DefaultRolePermission->escapeField('role_key')
		);

		$wheres = array();
		$wheres[] = $model->Role->escapeField('type') . ' = ' .
					$db->value(Role::ROLE_TYPE_ROOM, 'string');
		$wheres[] = $model->Role->escapeField('language_id') . ' = ' .
					$db->value(Current::read('Language.id'), 'string');
		$wheres[] = $model->RolesRoom->escapeField('room_id') . ' = ' .
					$db->value($data['Room']['id'], 'string');
		$wheres[] = $model->DefaultRolePermission->escapeField('type') . ' = ' .
					$db->value(DefaultRolePermission::TYPE_ROOM_ROLE, 'string');

		//--クエリの実行
		$sql = $this->__insertSql(
			$tableName, array_keys($values), array_values($values), $joins, $wheres
		);
		$model->RoomRolePermission->query($sql);
		$result = $model->RoomRolePermission->getAffectedRows() > 0;
		if (! $result) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		return true;
	}

/**
 * Pageのデフォルトデータ登録処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $data Room data
 * @return array ページ
 * @throws InternalErrorException
 */
	public function saveDefaultPage(Model $model, $data) {
		$model->loadModels([
			'Page' => 'Pages.Page',
			'Room' => 'Rooms.Room',
		]);

		$parentPageId = $this->_getParentPageId($model, $data);
		$slug = Hash::get(
			$data, 'Page.slug', OriginalKeyBehavior::generateKey('Page', $model->useDbConfig)
		);
		$permalink = Hash::get(
			$data, 'Page.permalink', $this->_getParentRoomPermalink($model, $parentPageId) . $slug
		);
		$page = Hash::merge($data, array(
			'Page' => array(
				'slug' => $slug,
				'permalink' => $permalink,
				'room_id' => $data['Room']['id'],
				'root_id' => $parentPageId,
				'parent_id' => $parentPageId
			),
			//'PagesLanguage' => array(
			//	'language_id' => Current::read('Language.id'),
			//	'name' => __d('rooms', 'Top')
			//),
		));

		//ルームのBoxを生成する
		$model->Page->saveBox(array(
			'Room' => $data['Room'],
			'Page' => array(
				'id' => null,
				'room_id' => $data['Room']['id'],
			),
		));

		//ページ生成
		$model->Page->create(false);
		$page = $model->Page->savePage($page, array('atomic' => false));
		if (! $page) {
			CakeLog::error(var_export($model->Page->validationErrors, true));
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		if (! $model->Room->updateAll(
			array($model->Room->alias . '.page_id_top' => $page['Page']['id']),
			array($model->Room->alias . '.id' => $data['Room']['id'])
		)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$page[$model->Room->alias]['page_id_top'] = $page['Page']['id'];
		return $page;
	}
/**
 * 親ページPermalinkを取得する
 *
 * @param Model $model 呼び出し前のモデル
 * @param int $parentPageId 親ページID
 * @return string
 */
	protected function _getParentRoomPermalink(Model $model, $parentPageId) {
		$model->loadModels(['Page' => 'Pages.Page']);
		$page = $model->Page->find('first', array(
			'conditions' => array('id' => $parentPageId),
			'recursive' => -1
		));
		$parentPermalink = Hash::get($page, 'Page.permalink', '');
		if (! empty($parentPermalink)) {
			$parentPermalink = $parentPermalink . DS;
		}
		return $parentPermalink;
	}
/**
 * 親ページIDを取得する
 *
 * @param Model $model 呼び出し前のモデル
 * @param array $page ページデータ
 * @return string
 */
	protected function _getParentPageId(Model $model, $page) {
		$model->loadModels(['Room' => 'Rooms.Room']);

		if (Hash::get($page, 'Room.parent_id') &&
				! in_array((string)Hash::get($page, 'Room.parent_id'), Room::getSpaceRooms(), true)) {
			$parentRoomId = Hash::get($page, 'Room.parent_id');
			$parentRoom = $model->Room->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $parentRoomId)
			));
			return Hash::get($parentRoom, 'Room.page_id_top');
		} else {
			$spaceId = Hash::get($page, 'Room.space_id');
			return Space::getPageIdSpace($spaceId);
		}
	}

/**
 * INSERT INTO ... SELETEのSQL生成
 *
 * @param string $tableName Table name
 * @param array $fields Insert query fields
 * @param array $values Select query fields
 * @param array $joins Join table. Null on from, other than inner join.
 * @param array $where Query where
 * @return string Query sql
 */
	private function __insertSql($tableName, $fields, $values, $joins, $where) {
		$sql = 'INSERT INTO ' . $tableName . '(' . implode(', ', $fields) . ') ' .
				'SELECT ' . implode(', ', $values) . ' ';
		foreach ($joins as $table => $onWhere) {
			if (! isset($onWhere)) {
				$sql .= 'FROM ' . $table . ' ';
			} else {
				$sql .= 'INNER JOIN ' . $table . ' ON (' . implode(' AND ', $onWhere) . ') ';
			}
		}
		$sql .= 'WHERE ' . implode(' AND ', $where);
		return $sql;
	}

/**
 * 1ユーザに対するRolesRoomsUserのデフォルトデータ取得処理
 *
 * @param Model $model 呼び出し元のモデル
 * @return array
 */
	public function getDefaultRolesRoomsUser(Model $model) {
		$model->loadModels([
			'Room' => 'Rooms.Room',
			'RolesRoom' => 'Rooms.RolesRoom',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
		]);

		$rooms = [];
		$roomIds = [];
		$result = $model->Room->find('all', [
			'recursive' => 0,
			'conditions' => [
				'Room.id = Space.room_id_root'
			],
		]);
		foreach ($result as $room) {
			$roomId = $room['Room']['id'];
			$rooms[$roomId] = $room;
			$roomIds[] = $roomId;
		}
		$this->__setDefaultParticipationRooms($model, $rooms, $roomIds, $roomIds);

		if (! Configure::read('NetCommons.installed')) {
			$rooms = Hash::insert(
				$rooms, '{n}.Room.default_role_key', Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR
			);
		}

		$rolesRooms = $model->RolesRoom->find('list', array(
			'recursive' => -1,
			'fields' => array('role_key', 'id', 'room_id'),
			'conditions' => array(
				'room_id' => $roomIds,
			)
		));

		$rolesRoomsUsers = array();
		foreach ($rooms as $room) {
			$roomId = $room['Room']['id'];
			$model->RolesRoomsUser->create(false);

			$roleKey = $room['Room']['default_role_key'];
			if (isset($rolesRooms[$roomId][$roleKey])) {
				$rolesRoomId = $rolesRooms[$roomId][$roleKey];
				$rolesRoomsUser = $model->RolesRoomsUser->create([
					'id' => null,
					'roles_room_id' => $rolesRoomId,
					'user_id' => null,
					'room_id' => $roomId,
				]);
				$rolesRoomsUsers[$roomId] = $rolesRoomsUser['RolesRoomsUser'];
			}
		}

		return $rolesRoomsUsers;
	}

/**
 * 1ユーザに対するRolesRoomsUserのデフォルトデータ取得処理
 *
 * @param Model $model 呼び出し元のモデル
 * @param array &$rooms セットするルームデータ配列
 * @param array &$roomIds セットするルームIDリスト
 * @param array $whereIds 取得する条件のルームIDリスト
 * @return array
 */
	private function __setDefaultParticipationRooms(Model $model, &$rooms, &$roomIds, $whereIds) {
		$result = $model->Room->find('all', [
			'recursive' => -1,
			'conditions' => [
				'default_participation' => true,
				'parent_id' => $whereIds
			],
		]);

		$nextRoomIds = [];
		foreach ($result as $room) {
			$roomId = $room['Room']['id'];
			$rooms[$roomId] = $room;
			$roomIds[] = $roomId;
			$nextRoomIds[] = $roomId;
		}

		if ($nextRoomIds) {
			$this->__setDefaultParticipationRooms($model, $rooms, $roomIds, $nextRoomIds);
		}
	}

/**
 * Room.page_id_topに対するページ名を編集できるかどうか
 * スペースのroom_id_rootのroom_idに対しては編集不可とする
 *
 * @param Model $model 呼び出し前のモデル
 * @param array $room ルームデータ
 * @return string
 */
	protected function _hasEditablePage(Model $model, $room) {
		$model->loadModels([
			'Room' => 'Rooms.Room'
		]);

		$spaces = $model->getSpaces();
		$roomIds = Hash::extract($spaces, '{n}.Space.room_id_root');

		return !(bool)in_array($room['Room']['id'], $roomIds, true);
	}

/**
 * ルームに対応したのページ登録処理
 *
 * 呼び出しもとでトランザクションを開始する
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $room received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function savePageLanguage(Model $model, $room) {
		$model->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
		]);

		if (! $this->_hasEditablePage($model, $room)) {
			return true;
		}

		$roomLanguages = Hash::get($room, 'RoomsLanguage', array());
		foreach ($roomLanguages as $roomLanguage) {
			$pageLanguage = $model->PagesLanguage->find('first', array(
				'recursive' => -1,
				'fields' => array('id', 'page_id', 'language_id'),
				'conditions' => array(
					'page_id' => Hash::get($room, 'Room.page_id_top'),
					'language_id' => $roomLanguage['language_id'],
				)
			));
			if (! $pageLanguage) {
				$pageLanguage['PagesLanguage'] = array(
					'id' => null,
					'page_id' => Hash::get($room, 'Room.page_id_top'),
					'language_id' => $roomLanguage['language_id'],
					'is_origin' => ($roomLanguage['language_id'] == Current::read('Language.id'))
				);
			}
			$pageLanguage['PagesLanguage']['name'] = $roomLanguage['name'];

			$model->PagesLanguage->Behaviors->disable('M17n');
			$model->PagesLanguage->create(false);
			$pageLanguage = $model->PagesLanguage->save($pageLanguage);
			if (! $pageLanguage) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$model->PagesLanguage->Behaviors->enable('M17n');
		}

		return true;
	}

/**
 * プライベートルームの登録処理
 *
 * 呼び出しもとでトランザクションを開始する
 *
 * @param Model $model 呼び出し元のモデル
 * @param array $room received post data
 * @return bool True on success, false on validation errors
 * @throws InternalErrorException
 */
	public function savePrivateSpaceRoom(Model $model, $room) {
		$model->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
			'RoomsLanguage' => 'Rooms.RoomsLanguage',
		]);

		$db = $model->getDataSource();

		$roomId = Hash::get($room, 'Room.id');

		//プライベートのIDを取得
		$rooms = $model->Room->children($roomId, false, ['Room.id', 'Room.page_id_top'], 'Room.rght');
		$roomIds = Hash::extract($rooms, '{n}.Room.id');
		$pageIds = Hash::extract($rooms, '{n}.Room.page_id_top');

		$roomLanguages = Hash::get($room, 'RoomsLanguage', array());
		foreach ($roomLanguages as $roomLanguage) {
			$update = array(
				'RoomsLanguage.name' => $db->value($roomLanguage['name'], 'string'),
			);
			$conditions = array(
				'RoomsLanguage.language_id' => $roomLanguage['language_id'],
				'RoomsLanguage.room_id' => $roomIds
			);
			if (! $model->RoomsLanguage->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$update = array(
				'PagesLanguage.name' => $db->value($roomLanguage['name'], 'string'),
			);
			$conditions = array(
				'PagesLanguage.language_id' => $roomLanguage['language_id'],
				'PagesLanguage.page_id' => $pageIds
			);
			if (! $model->PagesLanguage->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

}
