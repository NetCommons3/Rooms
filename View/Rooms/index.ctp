<?php
/**
 * Rooms index template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->css(array(
	'/m17n/css/style.css',
	'/rooms/css/style.css',
	'/plugin_manager/css/style.css',
));

echo $this->NetCommonsHtml->script('/rooms/js/rooms.js');

$roomOrderList = $this->RoomsForm->getRoomOrderList($rooms);
$jsRoomOrderList = NetCommonsAppController::camelizeKeyRecursive($roomOrderList);
?>

<?php
	//ルーム管理全体の説明
	echo $this->MessageFlash->description(
		__d('rooms', 'You can add, edit and delete rooms in your NetCommons. And select the members to join in the rooms.')
	);
	//スペースタブ
	echo $this->Rooms->spaceTabs($activeSpaceId);

	//スペース編集の説明
	echo $this->RoomsForm->editSpaceDescription($activeSpaceId);

	if ($activeSpaceId !== Space::PRIVATE_SPACE_ID) {
		//ルーム作成の説明
		echo $this->RoomsForm->addRoomDescription($activeSpaceId);

		//各ルームの説明
		echo $this->RoomsForm->indexRoomDescription($activeSpaceId);
	}
?>

<?php if ($activeSpaceId !== Space::PRIVATE_SPACE_ID) : ?>
	<article class="rooms-manager"
             ng-controller="RoomsController"
             ng-init="initialize(
             <?php echo $activeSpaceId; ?>,
             <?php echo h(json_encode($jsRoomOrderList)); ?>)">
		<?php echo $this->Rooms->roomsRender($activeSpaceId,
				array(
					'dataElemen' => 'Rooms.Rooms/render_room_index',
					'headElement' => 'Rooms.Rooms/render_header'
				)
			); ?>
	</article>
<?php endif;
