<?php
/**
 * RolesRoomUsers edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->css('/users/css/style.css');
echo $this->NetCommonsHtml->script('/rooms/js/rooms_roles_users.js');
?>

<?php echo $this->element('Rooms.subtitle'); ?>
<?php echo $this->Rooms->spaceTabs($activeSpaceId); ?>
<?php echo $this->RoomForm->settingTabs(); ?>

<?php echo $this->NetCommonsForm->create('Room'); ?>
	<?php echo $this->NetCommonsForm->hidden('Room.id'); ?>

	<div class="user-search-index-head-margin">
		<div class="text-center">
			<?php echo $this->Button->searchLink(__d('users', 'Search for the members'),
					array('plugin' => 'user_manager', 'controller' => 'user_manager', 'action' => 'search')); ?>
		</div>

		<div class="form-group rooms-room-role-select">
			<?php echo $this->RoomsRolesForm->selectDefaultRoomRoles('Role.key', array(
				'empty' => __d('rooms', '(Select room role)'),
				'options' => array('delete' => __d('users', 'Non members')),
				'onchange' => 'submit();'
			)); ?>
		</div>
	</div>

	<table class="table table-condensed" ng-controller="RoomsRolesUsers">
		<thead>
			<tr>
				<th>
					<input class="form-control" type="checkbox" ng-click="allCheck($event)">
				</th>
				<?php echo $this->UserSearch->tableHeaders(); ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($users as $index => $user) : ?>
				<tr>
					<td>
						<?php echo $this->NetCommonsForm->hidden('RolesRoomsUser.' . $user['User']['id'] . '.id'); ?>
						<?php echo $this->NetCommonsForm->hidden('RolesRoomsUser.' . $user['User']['id'] . '.room_id', array('value' => $this->data['Room']['id'])); ?>
						<?php echo $this->NetCommonsForm->input('RolesRoomsUser.' . $user['User']['id'] . '.user_id', array(
							'label' => false,
							'div' => false,
							'type' => 'checkbox',
							'value' => $user['User']['id'],
							'checked' => false,
						)); ?>
					</td>
					<?php echo $this->UserSearch->tableRow($user, false); ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php echo $this->element('NetCommons.paginator'); ?>

	<div class="text-center">
		<?php echo $this->Button->cancel(__d('net_commons', 'Close'),
				$this->NetCommonsHtml->url('/rooms/' . $spaces[$activeSpaceId]['Space']['default_setting_action'])); ?>
	</div>

<?php echo $this->Form->end();
