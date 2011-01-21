<div id="group_page" class="container">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
	</div>
	<form action="" method="post" id="settings">
		<ul id="settings_navigation">
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings')?>">General</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members')?>">Members</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages')?>" class="selected">Pages</a></li>
		</ul>
		<h2>Group Settings &raquo; <span class="current_page">Pages</span></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages/add')?>">Add New Page</a>
		<br />
		<table cellspacing="0">
			<?php foreach ($pages->result() as $page): ?>
				<tr>
					<td class="primary">
						<?=$page->title?>
					</td>
					<td class="actions">
						<a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages/'.$page->id)?>" class="edit">edit</a>
						<a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages/'.$page->id.'/delete')?>" class="delete">edit</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	</form>
	<br />
</div>