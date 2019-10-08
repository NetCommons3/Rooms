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

App::uses('NetCommonsCache', 'NetCommons.Utility');

/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Lib
 */
class RoomsLibCache {

/**
 * NetCommonsキャッシュオブジェクト
 *
 * @var NetCommonsCache
 */
	private $__NetCommonsCache;

/**
 * コンストラクタ
 *
 * @param Model $Model モデル(当モデルは、MySQLのModelであれば何でも良い)
 * @return void
 */
	public function __construct(Model $Model) {
		$cacheName = 'delete_rooms_' . $Model->useDbConfig;
		$isTest = ($Model->useDbConfig === 'test');
		$this->__NetCommonsCache = new NetCommonsCache($cacheName, $isTest, 'netcommons_model');
	}

/**
 * キャッシュに登録
 *
 * @param string $key キャッシュキー
 * @param array $value キャッシュに保存する値
 * @return array
 */
	public function saveCache($key, $subKey, $value) {
		$this->__NetCommonsCache->write($value, $key, $subKey);
	}

/**
 * カラム名に対するテーブルリストを取得する
 *
 * @param string $key キャッシュキー
 * @param string $subKey キャッシュサブキー
 * @return array
 */
	public function readCache($key, $subKey = null) {
		return $this->__NetCommonsCache->read($key, $subKey);
	}

}
