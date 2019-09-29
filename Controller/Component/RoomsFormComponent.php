<?php
/**
 * Rooms Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * Rooms Component
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Controller\Component
 */
class RoomsFormComponent extends Component {

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param Controller $controller Controller with components to startup
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
 */
	public function startup(Controller $controller) {
		$controller->helpers[] = 'Rooms.RoomsForm';

		//コンポーネント内でPaginatorを使うため、Paginatorがロードされている必要がある
		$controller->Paginator = $controller->Components->load('Paginator');
		//Modelの呼び出し
		$controller->Room = ClassRegistry::init('Rooms.Room');

		$this->controller = $controller;
	}

/**
 * RoomsFormHelper::checkboxRooms()のためデータをセット
 *
 * @param array $conditions findのconditionsオプション
 * @param array $options findのオプション
 * @return void
 */
	public function setRoomsForCheckbox($conditions = array(), $options = array()) {
		$controller = $this->controller;

		//ルームデータ取得
		$controller->Paginator->settings =
			Hash::merge($controller->Room->getReadableRoomsConditions($conditions), $options);
		$rooms = $controller->Paginator->paginate('Room');
		$rooms = Hash::combine($rooms, '{n}.Room.id', '{n}');
		$controller->set('rooms', $rooms);

		$roomIds = array_keys($rooms);

		//Treeリスト取得
		$roomTreeList = $controller->Room->generateTreeList(
			array('Room.id' => $roomIds),
			null,
			null,
			Room::$treeParser
		);
		$controller->set('roomTreeList', $roomTreeList);
	}

}
