<?php
/**
 * Room Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('Space', 'Rooms.Model');

/**
 * Room Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model\Behavior
 */
class RoomBehavior extends ModelBehavior {

/**
 * スペースデータ
 * ※publicにしているのは、UnitTestで使用するため
 *
 * @var array
 */
	public static $spaces;

/**
 * 参照できるスペースリストデータ
 *
 * @var array
 */
	public static $readableSpaces = [
		Space::PUBLIC_SPACE_ID
	];

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		parent::setup($model, $config);

		$model->loadModels([
			'RolesRoom' => 'Rooms.RolesRoom',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'Room' => 'Rooms.Room',
			'RoomsLanguage' => 'Rooms.RoomsLanguage',
			'Space' => 'Rooms.Space',
		]);
	}

/**
 * ルームデータ取得用の条件取得
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $spaceId SpaceId
 * @param array $options 取得オプション配列
 * @return array ルームデータ取得条件
 */
	public function getRoomsConditions(Model $model, $spaceId, $options = array()) {
		$options = Hash::merge(array(
			'recursive' => 1,
			'conditions' => array(
				$model->Room->alias . '.space_id' => $spaceId,
				$model->Room->alias . '.page_id_top NOT' => null,
				$model->Room->alias . '.in_draft' => false
			),
			'order' => 'Room.sort_key',
		), $options);

		return $options;
	}

/**
 * ルームデータ取得用の条件取得
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $conditions 条件配列
 * @param int|null $userId ユーザID。nullの場合、`Current::read('User.id')`。
 * @return array ルームデータ取得条件
 */
	public function getReadableRoomsConditions(Model $model, $conditions = [], $userId = null) {
		if (is_null($userId)) {
			$userId = Current::read('User.id');
		}

		$spaceIds = self::$readableSpaces;
		if ($userId) {
			$spaceIds[] = Space::COMMUNITY_SPACE_ID;
			$joinType = 'INNER';
		} else {
			$joinType = 'LEFT';
		}
		if (Current::read('User.UserRoleSetting.use_private_room')) {
			$spaceIds[] = Space::PRIVATE_SPACE_ID;
		}
		if (! Current::allowSystemPlugin('rooms')) {
			$conditions = Hash::merge(array('Room.active' => true, 'Room.in_draft' => false), $conditions);
		}

		$communityRoomId = Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID);
		if (array_key_exists('Room.id', $conditions) && $conditions['Room.id'] === $communityRoomId) {
			$conditions = Hash::merge(
				array('OR' => array('Room.id' => $communityRoomId)),
				$conditions
			);
		} elseif (isset($conditions[$model->Room->alias . '.page_id_top NOT'])) {
			$conditions = Hash::merge(array('OR' => array(
				$model->Room->alias . '.page_id_top NOT' => null,
				'Room.id' => Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID),
			)), $conditions);
			unset($conditions[$model->Room->alias . '.page_id_top NOT']);
		} else {
			$conditions = Hash::merge(array($model->Room->alias . '.page_id_top NOT' => null), $conditions);
		}

		$options = Hash::merge(array(
			'recursive' => 1,
			'fields' => array(
				$model->Room->alias . '.*',
				$model->RolesRoom->alias . '.*',
				$model->RolesRoomsUser->alias . '.*',
				//$model->Space->alias . '.*',
			),
			'conditions' => array(
				$model->Room->alias . '.space_id' => $spaceIds,
			),
			'joins' => array(
				array(
					'table' => $model->RolesRoomsUser->table,
					'alias' => $model->RolesRoomsUser->alias,
					'type' => $joinType,
					'conditions' => array(
						$model->RolesRoomsUser->alias . '.room_id' . ' = ' . $model->Room->alias . ' .id',
						$model->RolesRoomsUser->alias . '.user_id' => $userId,
					),
				),
				array(
					'table' => $model->RolesRoom->table,
					'alias' => $model->RolesRoom->alias,
					'type' => $joinType,
					'conditions' => array(
						$model->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $model->RolesRoom->alias . ' .id',
						$model->RolesRoom->alias . '.room_id' . ' = ' . $model->Room->alias . ' .id',
					),
				),
			),
			'order' => 'Room.sort_key',
		), array('conditions' => $conditions));

		return $options;
	}

/**
 * スペースデータ取得
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @return array スペースデータ配列
 */
	public function getSpaces(Model $model) {
		if (self::$spaces) {
			return self::$spaces;
		}

		//スペースデータ取得
		$bindParentRoom = $model->Room->belongsTo['ParentRoom'];
		$bindChildRoom = $model->Room->hasMany['ChildRoom'];
		$model->Room->unbindModel(array(
			'belongsTo' => array('ParentRoom'),
			'hasMany' => array('ChildRoom')
		), false);
		$spaces = $model->Room->find('all', array(
			'recursive' => 1,
			'conditions' => array(
				$model->Room->alias . '.parent_id' => Space::getRoomIdRoot(Space::WHOLE_SITE_ID, 'Room'),
			),
			'order' => 'Room.sort_key'
		));
		// 外したものを戻しておく
		$model->Room->bindModel(array(
			'belongsTo' => array('ParentRoom' => $bindParentRoom),
			'hasMany' => array('ChildRoom' => $bindChildRoom),
		), false);

		$spaces = Hash::combine($spaces, '{n}.Room.id', '{n}');

		$result = $model->RolesRoom->find('all', array(
			'recursive' => -1,
			'fields' => '*',
			'conditions' => array(
				$model->RolesRoom->alias . '.room_id' => array(
					Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID),
					Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID)
				),
			),
			'joins' => array(
				array(
					'table' => $model->RolesRoomsUser->table,
					'alias' => $model->RolesRoomsUser->alias,
					'type' => 'INNER',
					'conditions' => array(
						$model->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $model->RolesRoom->alias . ' .id',
						$model->RolesRoomsUser->alias . '.user_id' => Current::read('User.id'),
					),
				),
			),
		));
		$result = Hash::combine($result, '{n}.RolesRoom.room_id', '{n}');

		self::$spaces = Hash::combine(Hash::merge($spaces, $result), '{n}.Space.id', '{n}');

		return self::$spaces;
	}

/**
 * ロールルームデータの取得
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $conditions 条件配列
 * @return array
 */
	public function getRolesRoomsInDraft(Model $model, $conditions = array()) {
		if (! array_key_exists('Room.id', $conditions)) {
			$conditions = Hash::merge(array(
				'OR' => ['Room.page_id_top NOT' => null, 'Room.in_draft' => true]
			), $conditions);
		}

		$rolesRooms = $model->RolesRoom->find('all', array(
			'recursive' => 0,
			'fields' => array(
				$model->RolesRoom->alias . '.*',
				$model->Room->alias . '.*',
			),
			'conditions' => $conditions,
		));

		return $rolesRooms;
	}

/**
 * ロールルームデータの取得
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $conditions 条件配列
 * @return array
 */
	public function getRolesRoomsNotInDraft(Model $model, $conditions = array()) {
		$conditions = Hash::merge(array(
			'Room.page_id_top NOT' => null, 'Room.in_draft' => false
		), $conditions);

		$rolesRooms = $model->RolesRoom->find('all', array(
			'recursive' => 0,
			'fields' => array(
				$model->RolesRoom->alias . '.*',
				$model->Room->alias . '.*',
			),
			'conditions' => $conditions,
		));

		return $rolesRooms;
	}

}
