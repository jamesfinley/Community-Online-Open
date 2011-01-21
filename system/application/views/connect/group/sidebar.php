<div id="sidebar">
	<?php if ($account !== null && $group->allows_membership == 1) { ?>
		<?php if ($this->db->where('group_id', $group->id)->where('user_id', $account->id)->where('approved', 0)->get('groups_users')->num_rows()): ?>
		<div class="status_in_group">
			Hey, <?=$this->users->fullname($account->id)?>!<br />
			<strong>You are still waiting for approval.</strong>
		</div>
		<?php elseif ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === false): ?>
		<a href="<?=site_url($this->groups_model->get_url($group->id).'/join')?>" class="status_in_group join">
			You are not a member of this <?=$group->type !== 'master' ? ($group->type !== 'small group' ? $group->type : 'group') : 'church'?>.<br />
			<strong>Do you wish to join?</strong>
		</a>
		<?php elseif ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id)): ?>
		<div class="status_in_group">
			Welcome back, <?=$this->users->fullname($account->id)?>!
			<a href="<?=site_url($this->groups_model->get_url($group->id).'/group_settings')?>">Email Settings for this Group</a>
		</div>
		<?php endif; ?>
	<?php } ?>
	<?php if ($account !== null && has_role('facilitator', $account->id, $group->id)): ?>
		<div id="facilitator_access">
			You are a facilitator of this group.
			<a href="<?=site_url($this->groups_model->get_url($group->id).'/settings')?>">Edit Group Settings</a>
		</div>
	<?php endif; ?>
	<?php if ($pages->num_rows() && ($group->is_public || $this->groups_model->belongs_to_group($group->id, $account->id))): ?>
		<ul class="pages_navigation">
			<?php foreach($pages->result() as $p): ?>
				<?php if ($p->url): ?>
				<li><a href="<?=$p->url?>"><?=$p->title?></a></li>
				<?php else: ?>
				<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/'.$p->slug)?>"><?=$p->title?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php
		$ministries = $this->db->where('campus_id', $group->id)->where('type', 'ministry')->get('groups');
		/*if ($group->type === 'campus')
		{
			echo '<div class="belongs_to">This Campus belongs to <strong><a href="'.site_url('connect/church/'.$church->slug).'">'.$church->name.'</a></strong></div>';
		}*/
		if ($campuses !== null)
		{
			echo '<div class="group_list"><h2>';
			if ($group->type === 'master')
			{
				echo 'Campuses at <strong>'.$group->name.'</strong>';
			}
			else
			{
				echo 'Other Campuses at <strong>'.$church->name.'</strong>';
			}
			?></h2>
			<ul><?php
			foreach ($campuses->result() as $campus)
			{
				?><li><a href="<?=site_url($this->groups_model->get_url($campus->id))?>"><?=$campus->name?></a></li><?php
			}
			?></ul></div><?php
		}
		if ($ministries->num_rows())
		{
			echo '<div class="group_list"><h2>Ministries at <strong>'.$group->name.'</strong></h2>';
			?>
			<ul><?php
			foreach ($ministries->result() as $ministry)
			{
				?><li><a href="<?=site_url($this->groups_model->get_url($ministry->id))?>"><?=$ministry->name?></a></li><?php
			}
			?></ul></div><?php
		}
		if ($small_groups !== null)
		{
			echo '<div class="group_list">';
			?><h2>Small Groups at <strong><?=$group->name?></strong></h2>
			<ul><?php
			foreach ($small_groups->result() as $small_group)
			{
				?><li><a href="<?=site_url($this->groups_model->get_url($small_group->id))?>"><?=$small_group->name?></a></li><?php
			}
			?></ul></div><?php
		}
		if ($group->type === 'small group' && $campus)
		{
			echo '<div class="belongs_to">This Small Group belongs to <strong><a href="'.site_url($this->groups_model->get_url($campus->id)).'">'.$campus->name.'</a></strong></div>';
		}
	?>
	<?php
		$members = $this->db->query('SELECT u.* FROM users u, groups_users gu WHERE u.id = gu.user_id AND gu.group_id = '.$group->id);
	?>
	<?php if ($account !== null && isset($group) && $group->type === 'small group' && $this->groups_model->belongs_to_group($group->id, $account->id) === true): ?>
	<div id="member_list">
		<h2>Members</h2>
		<ul>
			<?php foreach($members->result() as $member): ?>
				<li><?=$member->first_name.' '.$member->last_name?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</div>