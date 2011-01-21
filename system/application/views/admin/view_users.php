<div class="table_view">
	<h2><span class="current_page">Users</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<table id="users_list" cellspacing="0">
		<thead>
			<tr>
				<th>Name Name</th>
				<th>Email</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users->result() as $user): ?>
				<tr>
					<td class="name"><a href="<?=site_url('admin/users/edit/'.$user->id)?>"><?=$user->first_name?> <?=$user->last_name?></a></td>
					<td class="email"><a href="<?=site_url('admin/users/edit/'.$user->id)?>"><?=$user->email?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/users/add')?>">Add User</a></li>
		<li><a href="<?=site_url('admin/users')?>" class="selected">View Users</a></li>
	</ul>
</div>