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
	<a href="#" id="group_more_info">More Info</a>
	<form id="search_stream" action="" method="post">
		<input type="search" placeholder="Search this <?=$group_type?>" />
	</form>
	<ul id="filter_stream">
		<li id="filter_all_items" class="selected"><a href="#">All Items</a></li>
		<li id="filter_news"><a href="#">News</a></li>
		<li id="filter_events"><a href="#">Events</a></li>
		<li id="filter_discussion"><a href="#">Discussion</a></li>
		<li id="filter_prayers"><a href="#">Prayers</a></li>
		<li id="filter_qandas"><a href="#">Q &amp; A</a></li>
	</ul>
	<br />
</div>
<div id="stream">
	<?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id)): ?>
	<form action="<?=site_url('connect/'.$this->uri->segment(2).'/'.$this->uri->segment(3))?>" method="post" id="post_to_stream">
		<h2>Post to <strong><?=$group->name?></strong></h2>
		<input type="text" name="subject" placeholder="Subject" />
		<textarea name="content" cols="85" rows="5"></textarea>
		<select name="type">
			<option value="news">News</option>
			<option value="event">Event</option>
			<option value="discussion" selected="selected">Discussion</option>
			<option value="prayer">Prayer</option>
			<option value="qna">Q &amp; A</option>
		</select>
		<input type="submit" value="Post" />
	</form>
	<?php endif; ?>
	<?php if (count($streams) === 0): ?>
		<em>No one has posted to this stream yet!</em>
	<?php endif; ?>
	<?php foreach($streams as $stream):?>
	<div class="stream_item <?php echo $stream->type; ?>">
		<div class="item">
			<?php if ( $stream->type != 'prayer' ): ?>
			<h3><a href="#"><?php echo $stream->subject; ?></a></h3>
			<?php endif; ?>
			<p><?=$stream->content?></p>
			<div class="footer">
				<?php echo $stream->type; ?> posted by <a href="#"><?php echo $this->users->fullname($stream->user_id); ?></a> <?php echo ago($stream->created_at); ?>
				<br />
			</div>
		</div>
		<?php if ($this->stream_replies_model->items($stream->id, 5)->num_rows() > 0): ?>
		<div class="responses">
			<h4>Responses to <?php echo $stream->subject ? $stream->subject : 'prayer'; ?><!-- &mdash; <span class="show_more">showing 2 of 15 (<a href="#">see all responses</a>)</span>//--></h4>
			<?php foreach($this->stream_replies_model->items($stream->id, 5)->result() as $response):?>
			<div class="response">
				<p><?=$response->content?></p>
				<div class="footer">
					<a href="#"><?php echo $this->users->fullname($response->user_id); ?></a> replied <?php echo ago($response->created_at); ?>
				</div>
			</div>
			<?php endforeach; ?>

		</div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>
</div>
<div id="sidebar">
	<?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id) === false): ?>
	<div class="status_in_group join">
		You are not a member of this <?=$group->type !== 'master' ? ($group->type !== 'small group' ? $group->type : 'group') : 'church'?>.<br />
		<strong>Do you wish to <a href="<?=site_url('connect/'.$this->uri->segment(2).'/'.$group->slug.'/join')?>">join</a>?</strong>
	</div>
	<?php endif; ?>
	<?php if ($account !== null && $this->groups_model->belongs_to_group($group->id, $account->id)): ?>
	<div class="status_in_group">
		Welcome back, <?=$this->users->fullname($account->id)?>!
	</div>
	<?php endif; ?>
	<?php
		if ($group->type === 'campus')
		{
			echo '<div class="belongs_to">This Campus belongs to <strong><a href="'.site_url('connect/church/'.$church->slug).'">'.$church->name.'</a></strong></div>';
		}
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
				?><li><a href="<?=site_url('connect/campus/'.$campus->slug)?>"><?=$campus->name?></a></li><?php
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
				?><li><a href="<?=site_url('connect/small_group/'.$small_group->slug)?>"><?=$small_group->name?></a></li><?php
			}
			?></ul></div><?php
		}
		if ($group->type === 'small group')
		{
			echo '<div class="belongs_to">This Small Group belongs to <strong><a href="'.site_url('connect/campus/'.$campus->slug).'">'.$campus->name.'</a></strong></div>';
		}
	?>
</div>
<br />