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

App::uses('RoomsLibLog', 'Rooms.Lib');

/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Lib
 */
class RoomsLibDataSourceExecute {

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
 * 実行シェル
 *
 * @var Shell
 */
	private $__Shell;

/**
 * コンストラクタ
 *
 * @param Model $Model モデル(当モデルは、MySQLのModelであれば何でも良い)
 * @param Shell|null $Shell 実行シェル
 * @return void
 */
	public function __construct(Model $Model, $Shell = null) {
		$this->__Model = $Model;
		$this->__Shell = $Shell;
		$this->__DataSource = $Model->getDataSource();
		$this->__RoomsLibCache = new RoomsLibCache($Model);
	}

/**
 * テーブルリストをキャッシュから取得する。なければ、最新情報から取得し、キャッシュに保存する
 *
 * @return array
 */
	public function showTables() {
		//キャッシュから取得
		$cacheTables = $this->__RoomsLibCache->readCache('tables', null);
		if ($cacheTables) {
			return $cacheTables;
		}

		$schemaTables = $this->__findAllSchemeFileTables();
		$tablePrefix = $this->__Model->tablePrefix;

		//LIKEで多少の絞り込みを行う。ただし、tablePrefixがない場合は、全テーブルが対象となってしまう。
		$tables = $this->__Model->query("SHOW TABLES LIKE '{$tablePrefix}%s'");

		$retTables = [];
		foreach ($tables as $table) {
			$realTableName = array_shift($table['TABLE_NAMES']);
			$realPrefix = substr($realTableName, 0, strlen($tablePrefix));
			$tableName = substr($realTableName, strlen($tablePrefix));

			if ($tablePrefix !== $realPrefix ||
					! in_array($tableName, $schemaTables)) {
				continue;
			}
			$retTables[$tableName] = $this->showTableColumns($realTableName);
		}

		//キャッシュに登録
		$this->__RoomsLibCache->saveCache('tables', null, $retTables);

		return $retTables;
	}

/**
 * schemaファイルからテーブルリストを取得する
 *
 * @return array
 */
	private function __findAllSchemeFileTables() {
		App::uses('CakeSchema', 'Model');
		$plugins = App::objects('plugins');

		$allTables = [];
		foreach ($plugins as $plugin) {
			$tables = $this->__getSchemeFileTablesByPlugin($plugin);
			if ($tables) {
				$allTables = array_merge($allTables, $tables);
			}
		}

		return $allTables;
	}

/**
 * プラグイン名に対してschemaファイルのテーブルリストを取得する
 *
 * @param string $plugin プラグイン
 * @return array|false
 */
	private function __getSchemeFileTablesByPlugin($plugin) {
		$class = $plugin . 'Schema';
		if (! CakePlugin::loaded($plugin)) {
			return false;
		}
		$filePath = CakePlugin::path($plugin) . 'Config' . DS . 'Schema' . DS . 'schema.php';
		if (! file_exists($filePath)) {
			return false;
		}
		include_once $filePath;
		if (! class_exists($class)) {
			return false;
		}
		$classVars = get_class_vars($class);

		$tables = [];
		foreach ($classVars as $key => $value) {
			if (is_array($value) && !empty($value)) {
				$tables[] = $key;
			}
		}

		return $tables;
	}

/**
 * DBからカラムリストを取得する
 *
 * @param string $realTableName テーブルPrefix付きの実際のテーブル名
 * @return array
 */
	public function showTableColumns($realTableName) {
		$columns = $this->__Model->query('SHOW COLUMNS FROM ' . $realTableName);

		$retColumns = [];
		foreach ($columns as $column) {
			$name = $column['COLUMNS']['Field'];
			$retColumns[$name] = $column['COLUMNS'];
		}
		return $retColumns;
	}

/**
 * SELECTのSQLを実行する
 *
 * @param string $tableName テーブル名
 * @param array $selectFieldNames SELECTのカラム名リスト
 * @param array $wheres WHERE条件リスト
 * @return array
 */
	public function selectQuery($tableName, $selectFieldNames, $wheres) {
		$tablePrefix = $this->__Model->tablePrefix;
		$realTableName = $tablePrefix . $tableName;
		$tableAlias = Inflector::classify($tableName);

		$fields = array_map(function ($fieldName) use ($tableAlias) {
			return $this->__Model->escapeField($fieldName, $tableAlias);
		}, $selectFieldNames);

		$sql = sprintf(
			'SELECT %s FROM `%s` AS `%s` WHERE %s',
			implode(', ', $fields),
			$realTableName,
			$tableAlias,
			$this->__makeWhereSql($tableAlias, $wheres)
		);

		$queryResults = $this->__Model->query($sql);
		return $this->__flattenForDeleteTargetValues($tableName, $tableAlias, $queryResults);
	}

/**
 * SELECT COUNT(*)のSQLを実行する
 *
 * @param string $tableName テーブル名
 * @param array $wheres WHERE条件リスト
 * @return array
 */
	public function countQuery($tableName, $wheres) {
		$tablePrefix = $this->__Model->tablePrefix;
		$realTableName = $tablePrefix . $tableName;
		$tableAlias = Inflector::classify($tableName);

		$sql = sprintf(
			'SELECT COUNT(*) AS count_num FROM `%s` AS `%s` WHERE %s',
			$realTableName,
			$tableAlias,
			$this->__makeWhereSql($tableAlias, $wheres)
		);

		$queryResults = $this->__Model->query($sql);
		if (isset($queryResults[0][0]['count_num'])) {
			$count = (int)$queryResults[0][0]['count_num'];
		} else {
			$count = 0;
		}

		return $count;
	}

/**
 * DELETEのSQLを実行する
 *
 * @param string $tableName テーブル名
 * @param array $wheres WHERE条件リスト
 * @return array
 */
	public function deleteQuery($tableName, $wheres) {
		$tablePrefix = $this->__Model->tablePrefix;
		$realTableName = $tablePrefix . $tableName;

		$sql = sprintf(
			'DELETE FROM `%s` WHERE %s',
			$realTableName,
			$this->__makeWhereSql($realTableName, $wheres)
		);

		RoomsLibLog::infoLog($this->__Shell, $sql, 2);

		$queryResults = $this->__Model->query($sql);

		RoomsLibLog::successLog(
			$this->__Shell,
			'--> AffectedRows = ' . $this->__Model->getAffectedRows(),
			2
		);

		return $queryResults;
	}

/**
 * 外部フィードキーに対して、削除対象データを取得する
 *
 * @param string $tableAlias テーブルのAlias名
 * @param array $wheres WHERE条件リスト
 * @return array
 */
	private function __makeWhereSql($tableAlias, $wheres) {
		$whereConditions = [];

		foreach ($wheres as $fieldName => $values) {
			$whereField = $this->__Model->escapeField($fieldName, $tableAlias);

			if (is_array($values)) {
				$escapeValuesArr = array_map(function ($value) {
					return $this->__DataSource->value($value, 'string');
				}, $values);
				$escapeValues = implode(', ', $escapeValuesArr);
				$whereConditions[] = $whereField . ' IN (' . $escapeValues . ')';
			} else {
				$escapeValues = $this->__DataSource->value($values, 'string');
				$whereConditions[] = $whereField . '=' . $escapeValues;
			}
		}
		return implode(' AND ', $whereConditions);
	}

/**
 * クエリの結果をフラットの配列(一次配列、「.」で連結)にする
 *
 * @param string $tableName テーブル名
 * @param string $tableAlias テーブルのAlias名
 * @param array $queryResults クエリの結果
 * @return array
 */
	private function __flattenForDeleteTargetValues($tableName, $tableAlias, $queryResults) {
		$retResults = [];
		if (empty($queryResults)) {
			return $retResults;
		}

		foreach ($queryResults as $result) {
			foreach ($result[$tableAlias] as $field => $value) {
				if (! isset($retResults[$tableName . '.' . $field])) {
					$retResults[$tableName . '.' . $field] = [];
				}
				$retResults[$tableName . '.' . $field][] = $value;
			}
		}

		return $retResults;
	}

}
