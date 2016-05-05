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
?>

<tr class="<?php echo $this->Rooms->statusCss($room); ?>">
	<td>
		<?php echo $this->Rooms->roomName($room, $nest); ?>

		<?php echo $this->Button->editLink('',
				array($room['Space']['id'], $room['Room']['id']),
				array('iconSize' => 'btn-xs')
			); ?>

		<?php
			if ($nest !== 0) {
				echo $this->RoomForm->changeStatus($room);
			}
		?>
	</td>

	<td>
		<?php echo $this->DisplayUser->handleLink($room, array('avatar' => true)); ?>
	</td>
	<td>
		<?php echo $this->Rooms->roomMembers(Hash::get($rolesRoomsUsers, $room['Room']['id'])); ?>
	</td>

	<td class="text-right">
		<?php
			if ($nest === 0) {
				echo $this->Button->addLink(__d('rooms', 'Add new subroom'),
						array('action' => 'add', $room['Space']['id'], $room['Room']['id']),
						array('iconSize' => 'btn-xs')
					);
			}
		?>
	</td>
</tr>
