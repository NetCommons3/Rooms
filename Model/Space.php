<?php
/**
 * Space Model
 *
 * @property Space $ParentSpace
 * @property Room $Room
 * @property Space $ChildSpace
 * @property Language $Language
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoomsAppModel', 'Rooms.Model');

/**
 * Space Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Model
 */
class Space extends RoomsAppModel {

/**
 * インスタンス
 *
 * @var array
 */
	public static $instanceRoom = null;

/**
 * インスタンス
 *
 * @var array
 */
	public static $instanceSpace = null;

/**
 * スペースデータ
 * ※publicにしているのは、UnitTestで使用するため
 *
 * @var array
 */
	public static $spaces;

/**
 * Table name
 *
 * @var string
 */
	public $useTable = 'spaces';

/**
 * Space id
 *
 * @var const
 */
	const
		WHOLE_SITE_ID = '1',
		PUBLIC_SPACE_ID = '2',
		PRIVATE_SPACE_ID = '3',
		COMMUNITY_SPACE_ID = '4';

/**
 * SpaceIdのリスト
 *
 * @var array
 */
	public static $spaceIds = array();

/**
 * DefaultParticipationFixed
 *
 * @var bool
 */
	public $participationFixed = false;

/**
 * Behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.NetCommonsCache',
	);

/**
 * RoomSpaceルームのデフォルト値
 *
 * @param array $data 初期値データ配列
 * @return array RoomSpaceルームのデフォルト値配列
 */
	public function createRoom($data = array()) {
		$this->loadModels([
			'Language' => 'M17n.Language',
			'Room' => 'Rooms.Room',
			'RoomsLanguage' => 'Rooms.RoomsLanguage',
		]);

		$result = $this->Room->create(array_merge(array(
			'id' => null,
			'active' => true,
		), $data));

		$languages = $this->Language->getLanguages();
		foreach ($languages as $i => $language) {
			$roomsLanguage = $this->RoomsLanguage->create(array(
				'id' => null,
				'language_id' => $language['Language']['id'],
				'room_id' => null,
				'name' => '',
			));

			$result['RoomsLanguage'][$i] = $roomsLanguage['RoomsLanguage'];
		}

		return $result;
	}

/**
 * インスタンスの取得
 *
 * @param string $spaceModel モデル名(Migrationで使用)
 * @param array $options ClassRegistryオプション
 * @return object RoomSpaceルームのデフォルト値配列
 */
	public static function getInstance($spaceModel = 'Space', $options = []) {
		$options['class'] = 'Rooms.' . $spaceModel;
		if ($spaceModel === 'Space') {
			if (! self::$instanceSpace) {
				self::$instanceSpace = ClassRegistry::init($options, true);
			}
			return self::$instanceSpace;
		} else {
			if (! self::$instanceRoom) {
				self::$instanceRoom = ClassRegistry::init($options, true);
			}
			return self::$instanceRoom;
		}
	}

/**
 * SpaceのルームIDを取得
 *
 * @param int $spaceId スペースID
 * @param string $spaceModel モデル名(Migrationで使用)
 * @param array $options ClassRegistryオプション
 * @return int
 */
	public static function getRoomIdRoot($spaceId, $spaceModel = 'Space', $options = []) {
		$Space = self::getInstance($spaceModel, $options);
		if ($spaceModel === 'Space') {
			if (! isset(self::$spaceIds['Space'])) {
				$spaces = $Space->cacheFindQuery('list', array(
					'recursive' => -1,
					'fields' => array('id', 'room_id_root'),
				));
				if ($spaces) {
					self::$spaceIds['Space'] = $spaces;
				}
			}
			$spaceIds = [];
			if (isset(self::$spaceIds['Space'])) {
				$spaceIds = self::$spaceIds['Space'];
			}
		} else {
			if (! isset(self::$spaceIds['Room'])) {
				$spaceIds = $Space->find('list', array(
					'recursive' => -1,
					'fields' => array('space_id', 'id'),
					'conditions' => array(
						'space_id' => self::WHOLE_SITE_ID
					),
				));

				$result = $Space->find('list', array(
					'recursive' => -1,
					'fields' => array('space_id', 'id'),
					'conditions' => array(
						'parent_id' => $spaceIds[self::WHOLE_SITE_ID]
					),
				));
				foreach ($result as $key => $item) {
					$spaceIds[$key] = $item;
				}

				self::$spaceIds['Room'] = $spaceIds;
			}
			$spaceIds = [];
			if (isset(self::$spaceIds['Room'])) {
				$spaceIds = self::$spaceIds['Room'];
			}
		}
		if (isset($spaceIds[$spaceId])) {
			return $spaceIds[$spaceId];
		}
		return 0;
	}

/**
 * SpaceのページIDを取得
 *
 * @param int $spaceId スペースID
 * @return int
 */
	public static function getPageIdSpace($spaceId) {
		$Space = ClassRegistry::init('Rooms.Space', true);

		if (! isset(self::$spaceIds['Page'])) {
			$spaces = $Space->cacheFindQuery('list', array(
				'recursive' => -1,
				'fields' => array('id', 'page_id_top'),
			));
			self::$spaceIds['Page'] = $spaces;
		}

		return (string)self::$spaceIds['Page'][$spaceId];
	}

/**
 * スペースデータ取得
 *
 * @param int $spaceId スペースID
 * @return array スペースデータ配列
 */
	public function getSpace($spaceId) {
		$spaces = $this->getSpaces();

		foreach ($spaces as $space) {
			if ($spaceId == $space[$this->alias]['id']) {
				return $space[$this->alias];
			}
		}

		return [];
	}

/**
 * スペースデータ取得
 *
 * @return array スペースデータ配列
 */
	public function getSpaces() {
		if (self::$spaces) {
			return self::$spaces;
		}
		self::$spaces = $this->cacheFindQuery('all', array(
			'recursive' => -1
		));
		return self::$spaces;
	}

}
