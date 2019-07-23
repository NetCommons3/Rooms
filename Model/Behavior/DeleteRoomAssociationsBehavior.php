<?php
/**
 * DeleteRoomAssociations Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * DeleteRoomAssociations Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model\Behavior
 */
class DeleteRoomAssociationsBehavior extends ModelBehavior {

/**
 * 外部キーにroom_idおよびrole_room_idがあるテーブルのデータ削除
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @return bool True on success
 */
	public function deleteRoomAssociations(Model $model, $roomId) {
		//外部キーがrole_room_idのデータを削除
		$model->loadModels([
			'RolesRoom' => 'Rooms.RolesRoom',
		]);
		$rolesRoomIds = $model->RolesRoom->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$model->RolesRoom->alias . '.room_id' => $roomId,
			),
		));
		$rolesRoomIds = array_keys($rolesRoomIds);
		$this->queryDeleteAll($model, 'roles_room_id', $rolesRoomIds);

		//外部キーがroom_idのデータを削除
		$this->queryDeleteAll($model, 'room_id', $roomId);

		return true;
	}

/**
 * 外部キーにpage_idがあるテーブルのデータ削除
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function deletePagesByRoom(Model $model, $roomId) {
		$model->loadModels([
			'Page' => 'Pages.Page',
		]);

		//外部キーがpage_idのデータを削除
		$pageIds = $model->Page->find('list', array(
			'recursive' => -1,
			//'fields' => array('id', 'key'),
			'conditions' => array(
				$model->Page->alias . '.room_id' => $roomId,
			),
		));
		$pageIds = array_keys($pageIds);

		//Tree構成の関係で、ページの削除はdeleteAllでやる
		CakeLog::info('[room deleting] Page->deleteAll ' . implode(', ', $pageIds));
		if (!$model->Page->deleteAll(array($model->Page->alias . '.id' => $pageIds), false)) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		$this->queryDeleteAll($model, 'page_id', $pageIds);

		return true;
	}

/**
 * 外部キーにframe_idおよびframe_keyがあるテーブルのデータ削除
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @return bool True on success
 */
	public function deleteFramesByRoom(Model $model, $roomId) {
		$model->loadModels([
			'Frame' => 'Frames.Frame',
		]);

		$frames = $model->Frame->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'key'),
			'conditions' => array(
				$model->Frame->alias . '.room_id' => $roomId,
			),
		));
		$frameIds = array_keys($frames);
		$frameKeys = array_values($frames);

		//外部キーがframe_idのデータを削除
		$this->queryDeleteAll($model, 'frame_id', $frameIds);

		//外部キーがframe_keyのデータを削除
		$this->queryDeleteAll($model, 'frame_key', array_unique($frameKeys));

		return true;
	}

/**
 * 外部キーにblock_idおよびblock_keyがあるテーブルのデータ削除
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $roomId ルームID
 * @return bool True on success
 */
	public function deleteBlocksByRoom(Model $model, $roomId) {
		$model->loadModels([
			'Block' => 'Blocks.Block',
		]);

		$blocks = $model->Block->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'key'),
			'conditions' => array(
				$model->Block->alias . '.room_id' => $roomId,
			),
		));
		$blockIds = array_keys($blocks);
		$blockKeys = array_values($blocks);

		//外部キーがblock_idのデータを削除
		$this->queryDeleteAll($model, 'block_id', $blockIds);

		//外部キーがblock_keyのデータを削除
		$this->queryDeleteAll($model, 'block_key', array_unique($blockKeys));

		return true;
	}

/**
 * フィールドのデータを一括削除
 *
 * @param Model $model Model using this behavior
 * @param int $field フィールド名
 * @param mixed $value 値
 * @return bool True on success
 */
	public function queryDeleteAll(Model $model, $field, $value) {
		if (! $value) {
			return true;
		}

		$db = $model->getDataSource();
		if (is_array($value)) {
			$targets = array();
			foreach ($value as $v) {
				$targets[] = $db->value($v, 'string');
			}
		} else {
			$targets = array($db->value($value, 'string'));
		}

		$tables = $model->query('SHOW TABLES');
		foreach ($tables as $table) {
			$tableName = array_shift($table['TABLE_NAMES']);
			$columns = $model->query('SHOW COLUMNS FROM ' . $tableName);
			if (! Hash::check($columns, '{n}.COLUMNS[Field=' . $field . ']')) {
				continue;
			}

			$sql = 'DELETE FROM ' . $tableName .
					' WHERE ' . $field . ' IN (' . implode(', ', $targets) . ')';
			$model->query($sql);
		}

		return true;
	}

}
