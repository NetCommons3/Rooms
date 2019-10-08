<?php
/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsLibCache', 'Rooms.Lib');
App::uses('RoomsLibDataSourceExecute', 'Rooms.Lib');

/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Lib
 */
class RoomsLibDeleteRoomTables {

/**
 * データソースオブジェクト
 *
 * @var DataSource
 */
	private $__DataSource;

/**
 * 使用するモデル
 *
 * @var Model
 */
	private $__Model;

/**
 * キャッシュオブジェクト
 *
 * @var RoomsLibCache
 */
	private $__RoomsLibCache;

/**
 * キャッシュオブジェクト
 *
 * @var RoomsLibDataSourceExecute
 */
	private $__RoomsLibDataSourceExecute;

/**
 * テーブル情報
 *
 * @var array
 */
	private $__tables = [];

/**
 * 除外するテーブル
 *
 * ※定数扱いだが、php7からconstに配列が使用できるが、php5はconstに配列が使えないためprivateのメンバー変数とする
 *
 * @var array
 */
	private $__defIgnoreTables = [
		'rooms', 'pages', 'blocks', 'frames', 'room_delete_related_tables', 'roles_rooms'
	];

/**
 * コンストラクタ
 *
 * @param Model $Model モデル(当モデルは、MySQLのModelであれば何でも良い)
 * @return void
 */
	public function __construct(Model $Model) {
		$this->__Model = $Model;
		$this->__DataSource = $Model->getDataSource();
		$this->__RoomsLibCache = new RoomsLibCache($Model);
		$this->__RoomsLibDataSourceExecute = new RoomsLibDataSourceExecute($Model);

		$this->__tables = $this->__RoomsLibDataSourceExecute->showTables();
	}

/**
 * テーブルリストを取得する
 *
 * @return array
 */
	public function getTableList() {
		if ($this->__tables) {
			return array_keys($this->__tables);
		} else {
			return [];
		}
	}

/**
 * カラム情報を取得する
 *
 * @param string $tableName テーブル名
 * @return array
 */
	private function __getColumns($tableName) {
		if (isset($this->__tables[$tableName])) {
			return $this->__tables[$tableName];
		} else {
			return [];
		}
	}

/**
 * テーブルに対する関連するテーブルリストを取得する
 *
 * @param string $tableName テーブル名
 * @param string $fieldName カラム名
 * @param string $foreignKey 外部キーフィールド名
 * @return array
 */
	public function findDeleteTargetRelatedTables($tableName, $fieldName, $foreignKey) {
		//キャッシュから取得
		$cacheTables = $this->__RoomsLibCache->readCache(
			'delete_related_tables',
			$tableName . '_' . $fieldName . '_' . $foreignKey
		);
		if ($cacheTables) {
			return $cacheTables;
		}

		//戻り値の関連テーブルリストを取得する
		$retRelatedTables = [];
		$ignoreTables = [];
		if (isset($this->__tables[$tableName][$fieldName])) {
			$this->__setRecursiveReletedTables(
				$tableName, $fieldName, $foreignKey, $retRelatedTables, $ignoreTables
			);
		}

		//キャッシュに登録
		$this->__RoomsLibCache->saveCache(
			'delete_related_tables',
			$tableName . '_' . $fieldName . '_' . $foreignKey,
			$retRelatedTables
		);

		return $retRelatedTables;
	}

/**
 * テーブルに対する関連するテーブルリストを再帰的に取得する
 *
 * @param string $tableName テーブル名
 * @param string $fieldName カラム名
 * @param string $foreignKey 外部キーフィールド名
 * @param array &$retRelatedTables 戻り値の関連テーブルリスト
 * @param array &$ignoreTables 除外するテーブルリスト(一度設定したものを除外するため)
 * @return void
 */
	private function __setRecursiveReletedTables(
				$tableName, $fieldName, $foreignKey, &$retRelatedTables, &$ignoreTables) {
		$relatedTableNames = array_keys($this->__tables);
		foreach ($relatedTableNames as $relatedTableName) {
			if (in_array($relatedTableName, $ignoreTables, true) ||
					in_array($relatedTableName, $this->__defIgnoreTables, true)) {
				continue;
			}

			$columns = $this->__getColumns($relatedTableName);
			if (! isset($columns[$foreignKey])) {
				continue;
			}

			//戻り値の関連テーブルリストにセットする
			if (! isset($retRelatedTables[$tableName][$fieldName])) {
				$retRelatedTables[$tableName][$fieldName] = [];
			}
			$retRelatedTables[$tableName][$fieldName][] = $relatedTableName . '.' . $foreignKey;
			$ignoreTables[] = $relatedTableName;

			//idカラムがある場合、再帰的に取得する
			if (isset($columns['id'])) {
				$relatedForeignKey = $this->__makeForeignKey($relatedTableName, 'id');
				$this->__setRecursiveReletedTables(
					$relatedTableName, 'id', $relatedForeignKey, $retRelatedTables, $ignoreTables
				);
			}
			//keyカラムがある場合、再帰的に取得する
			if (isset($columns['key'])) {
				$relatedForeignKey = $this->__makeForeignKey($relatedTableName, 'key');
				$this->__setRecursiveReletedTables(
					$relatedTableName, 'key', $relatedForeignKey, $retRelatedTables, $ignoreTables
				);
			}
		}

		return $retRelatedTables;
	}

/**
 * 外部キーのテーブル名、カラム名を取得する
 *
 * @param string $fieldName カラム名
 * @return array $ret = [テーブル名, カラム名]
 */
	private function __getTableAndFieldByForeignKey($fieldName) {
		if (substr($fieldName, -3) === '_id') {
			if (substr($fieldName, 0, -3) === 'bbs') {
				return ['bbses', 'id'];
			} else {
				return [Inflector::pluralize(substr($fieldName, 0, -3)), 'id'];
			}
		} elseif (substr($fieldName, -4) === '_key') {
			if (substr($fieldName, 0, -4) === 'bbs') {
				return ['bbses', 'key'];
			} else {
				return [Inflector::pluralize(substr($fieldName, 0, -4)), 'key'];
			}
		} else {
			return [null, null];
		}
	}

/**
 * 外部キーのカラム名を生成する
 *
 * @param string $tableName テーブル名
 * @param string $fieldName カラム名
 * @return string
 */
	private function __makeForeignKey($tableName, $fieldName) {
		$foreignKey = Inflector::singularize($tableName) . '_' . $fieldName;
		if ($tableName === 'bbses') {
			$foreignKey = 'bbs_' . $fieldName;
		} else {
			$foreignKey = Inflector::singularize($tableName) . '_' . $fieldName;
		}
		return $foreignKey;
	}

/**
 * 外部フィールドキーの条件からテーブルリストに展開する
 *
 * @param string $tableName テーブル名
 * @param string $forienConditions 外部フィールドの条件(変換後)
 * @return array
 */
	public function expandToTablesFromForienConditions($tableName, $forienConditions) {
		//キャッシュから取得
		$cacheTables = $this->__RoomsLibCache->readCache('expand_to_tables', $tableName);
		if ($cacheTables) {
			return $cacheTables;
		}

		//外部フィールドキーの条件からテーブルリストに展開する
		$retTables = [];
		foreach ($forienConditions as $fieldName => $conditions) {
			foreach ($conditions as $condition) {
				list($relatedTableName, $foriegnKey) = explode('.', $condition);
				if ($relatedTableName === '*') {
					$retTables = array_merge(
						$retTables,
						$this->findDeleteTargetRelatedTables($tableName, $fieldName, $foriegnKey)
					);
				} else {
					if (! isset($retTables[$tableName][$fieldName])) {
						$retTables[$tableName][$fieldName] = [];
					}
					$retTables[$tableName][$fieldName][] = $relatedTableName . '.' . $foriegnKey;
				}
			}
		}

		//キャッシュに登録
		$this->__RoomsLibCache->saveCache('expand_to_tables', $tableName, $retTables);

		return $retTables;
	}

/**
 * 関連データを削除する
 *
 * @param string $tableName テーブル名
 * @param string $fieldName カラム名
 * @param string $value 値
 * @param string $forienConditions 外部フィールドの条件
 * @return void
 */
	public function deleteRelatedTables($tableName, $fieldName, $value, $foreignConditions) {
		$retRelatedValues = [];

		$targetTableList = $this->expandToTablesFromForienConditions(
			$tableName,
			RoomsLibForeignConditionsParser::invertDbValue($foreignConditions)
		);

		$this->__runDeleteRecursiveRelatedTables(
			$tableName, $fieldName, [$value], $targetTableList
		);
	}

/**
 * 再帰的に削除処理を実行する
 *
 * @param string $tableFieldName テーブル名とカラム名を連結したもの
 * @param array $values 値
 * @param array $targetTableList 対象テーブルリスト
 * @return array
 */
	private function __runDeleteRecursiveRelatedTables(
				$tableName, $fieldName, $values, $targetTableList) {
		//テーブルリストに対象のテーブルがあれば、
		if (! isset($targetTableList[$tableName][$fieldName])) {
			return;
		}
		foreach ($targetTableList[$tableName][$fieldName] as $relatedTableField) {
			list($relatedTableName, $relatedFieldName) = explode('.', $relatedTableField);

			if (isset($targetTableList[$relatedTableName])) {
				//再帰する場合
				$results = $this->__RoomsLibDataSourceExecute->selectQuery(
					$relatedTableName,
					array_keys($targetTableList[$relatedTableName]),
					$relatedFieldName,
					$values
				);

				if ($results) {
					foreach ($results as $recursiveTableField => $recursiveValues) {
						list($recursiveTableName, $recursiveFieldName) = explode('.', $recursiveTableField);
						$this->__runDeleteRecursiveRelatedTables(
							$recursiveTableName,
							$recursiveFieldName,
							$recursiveValues,
							$targetTableList
						);
					}
					$this->__RoomsLibDataSourceExecute->deleteQuery($relatedTableName, $relatedFieldName, $values);
				}
			} else {
				//再帰しない場合
				if ($this->__RoomsLibDataSourceExecute->countQuery($relatedTableName, $relatedFieldName, $values)) {
					$this->__RoomsLibDataSourceExecute->deleteQuery($relatedTableName, $relatedFieldName, $values);
				}
			}

		}
	}

}
