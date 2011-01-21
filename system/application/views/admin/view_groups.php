<div class="table_view">
	<h2><span class="current_page">Groups</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<table id="group_list" cellspacing="0">
		<thead>
			<tr>
				<th>Group Name</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($groups->result() as $group): ?>
				<tr>
					<td class="name"><a href="<?=site_url('admin/groups/view/'.$group->id)?>"><?=$group->name?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/groups/add')?>">Add Group</a></li>
		<li><a href="<?=site_url('admin/groups')?>" class="selected">View Groups</a></li>
	</ul>
</div>