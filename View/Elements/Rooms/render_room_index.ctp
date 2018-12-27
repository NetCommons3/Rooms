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

<tr class="<?php echo $this->Rooms->statusCss($room); ?>"
    data-parent="<?php echo $room['Room']['parent_id']; ?>"
    data-room-id="<?php echo $room['Room']['id']; ?>">

	<td>
		<a href="" ng-click="showRoom(<?php echo $room['Space']['id'] . ', ' . $room['Room']['id']; ?>)">
			<span class="rooms-index-room-name">
				<?php echo $this->Rooms->roomName($room, $nest, true); ?>
			</span>
		</a>
		<span class="badge">
			<?php echo Hash::get($rolesRoomsUsersCount, $room['Room']['id']); ?>
		</span>
	</td>

	<td>
		<?php echo $this->Button->editLink('',
				array(
					'action' => 'edit',
					'key' => $room['Space']['id'],
					'key2' => $room['Room']['id']
				),
				array('iconSize' => 'btn-xs')
			); ?>
		<?php echo $this->Button->editLink(__d('rooms', 'Edit the members'),
				array(
					'controller' => 'rooms_roles_users',
					'action' => 'edit',
					'key' => $room['Space']['id'],
					'key2' => $room['Room']['id']
				),
				array('iconSize' => 'btn-xs')
			); ?>
	</td>

	<td>
		<?php
			echo $this->RoomsForm->changeStatus($room);
		?>
	</td>

	<td>
		<?php echo $this->DisplayUser->handleLink($room, array('avatar' => true)); ?>
	</td>

	<td class="text-right">
		<?php
			if ($nest === 0) {
				echo $this->Button->addLink(
					__d('rooms', 'Sub room'),
					array(
						'controller' => 'room_add', 'action' => 'basic',
						'key' => $room['Space']['id'], 'key2' => $room['Room']['id']
					),
					array('iconSize' => 'btn-xs')
				);
			}
		?>
	</td>
</tr>
