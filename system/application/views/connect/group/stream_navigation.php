<script>
	var groups = new Array();
<?php
	if ($groups_for_user)
	{
		foreach ($groups_for_user->result() as $group_for_user)
		{
			$is_facilitator = has_role('facilitator', $account->id, $group_for_user->id);
			echo 'groups.push({id:'.$group_for_user->id.', type: \''.($group_for_user->type == 'small group' ? 'small_group' : $group_for_user->type).'\', name:\''.addslashes($group_for_user->name).'\''.($group_for_user->id === $group->id ? ', open: true' : ', open: false').', is_facilitator: '.($is_facilitator ? 'true' : 'false').'});'."\n";
		}
	}
?>
</script>
<div id="group_page" class="container" rel="<?=$group->latitude?>,<?=$group->longitude?>" data-description="<?=htmlentities($group->description)?>">
	<?php
		$group_type = 'Church';
		switch ($group->type)
		{
			case 'campus':
				$group_type = 'Church Campus';
				break;
			case 'small group':
				$group_type = 'Small Group';
				break;
			case 'ministry':
				$group_type = 'Ministry';
				break;
		}
	?>
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<?php if ($group->type !== 'campus'): ?>
			<?php if (($account === null || (isset($group->id) ? !$this->groups_model->belongs_to_group($group->id, $account->id) : false)) && isset($group) && ($group->type === 'small group' || $group->type === 'ministry') && $group->description): ?>
			<?php else: ?>
			<a href="#" id="group_more_info">More Info</a>
			<?php endif; ?>
		<?php endif; ?>
		<!--<form id="search_stream" action="" method="post">
			<input type="search" placeholder="Search this <?=$group_type?>" />
		</form>//-->
		<ul id="filter_stream">
			<?php if (!isset($group) || ($account === null && isset($group) && $group->is_public) || ($account !== null && isset($group) && ($this->groups_model->belongs_to_group($group->id, $account->id) || $group->is_public))): ?>
			<li id="filter_all_items" class="selected"><a href="#">All Items</a></li>
			<?php if (in_array('news', $types)): ?><li id="filter_news"><a href="#">News</a></li><?php endif; ?>
			<?php if (in_array('event', $types)): ?><li id="filter_events"><a href="#">Events</a></li><?php endif; ?>
			<?php if (in_array('discussion', $types)): ?><li id="filter_discussion"><a href="#">Discussion</a></li><?php endif; ?>
			<?php if (in_array('prayer', $types)): ?><li id="filter_prayers"><a href="#">Prayers</a></li><?php endif; ?>
			<?php if (in_array('qna', $types)): ?><li id="filter_qandas"><a href="#">Q &amp; A</a></li><?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>
	<?php $this->load->view('connect/group/stream'); ?>
	<?php $this->load->view('connect/group/sidebar'); ?>
	<br />
</div>

<script type="text/javascript">
	$(function () {
		connect.display_images();
	});
</script>