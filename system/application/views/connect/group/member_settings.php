<div id="group_page" class="container" rel=",">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
	</div>
	<form action="" method="post" id="settings">
		<ul id="settings_navigation">
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings')?>">General</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members')?>" class="selected">Members</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages')?>">Pages</a></li>
		</ul>
		<h2>Group Settings &raquo; <span class="current_page">Members</span></h2>
		<table cellspacing="0">
			<?php foreach ($users->result() as $member): ?>
				<tr>
					<td class="primary"><strong><?=$member->first_name.' '.$member->last_name?></strong> (<?=$member->email?>) <?php if (!$member->approved): ?><input type="submit" name="member-<?=$member->id?>" value="Approve" /><?php endif; ?></td>
					<td class="actions">
						<span class="member_actions black_hover_menu">
							<a href="#" class="icon">actions</a>
							<ul>
							<?php if (!$this->users->is_facilitator($member->id, $group->id, true)): ?>
								<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members/facilitator/'.$member->id)?>">make facilitator</a></li>
								&bull;
								<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members/afacilitator/'.$member->id)?>">make apprentice facilitator</a></li>
							<?php elseif ($this->users->is_facilitator($member->id, $group->id)): ?>
								<li><a><strong>facilitator</strong></a></li>
							<?php elseif ($this->users->is_apprentice($member->id, $group->id)): ?>
								<li><a><strong>apprentice facilitator</strong></a></li>
							<?php endif; ?>
							</ul>
						</span>
						<!--<a href="#" class="delete">remove</a>//-->
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<!--<ul>
		<?php foreach ($users->result() as $member): ?>
			<li><strong><?=$member->first_name.' '.$member->last_name?></strong> (<?=$member->email?>)
				<?php if (!$member->approved): ?>
					<input type="submit" name="member-<?=$member->id?>" value="Approve" />
				<?php elseif (!$this->users->is_facilitator($member->id, $group->id, true)): ?>
					<a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members/facilitator/'.$member->id)?>">make facilitator</a>
					&bull;
					<a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members/afacilitator/'.$member->id)?>">make apprentice facilitator</a>
				<?php elseif ($this->users->is_facilitator($member->id, $group->id)): ?>
					<strong>facilitator</strong>
				<?php elseif ($this->users->is_apprentice($member->id, $group->id)): ?>
					<strong>apprentice facilitator</strong>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ul>//-->
		</form>
	</form>
	<br />
</div>