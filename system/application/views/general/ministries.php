<script>
	var groups = new Array();
<?php
	if ($groups_for_user)
	{
		foreach ($groups_for_user->result() as $group_for_user)
		{
			echo 'groups.push({id:'.$group_for_user->id.', name:\''.$group_for_user->name.'\', open: false});'."\n";
		}
	}
?>
</script>
<div id="group_page" class="container">
	<div id="group_header">
		<ul id="ministries_big_buttons">
			<li><a href="<?=site_url('ministries/adults')?>" id="ministries_big_buttons_adults"><span class="head">Adults</span> <span class="subhead">Small Groups</span></a></li>
			<li><a href="<?=site_url('ministries/students')?>" id="ministries_big_buttons_students"><span class="head">Students</span> <span class="subhead">Jr. High &amp; High School</span></a></li>
			<li><a href="<?=site_url('ministries/kids')?>" id="ministries_big_buttons_kids"><span class="head">Kids</span> <span class="subhead">Infants &mdash; 5th Grade</span></a></li>
		</ul>
		<ul id="filter_stream">
			<li id="filter_all_items" class="selected"><a href="#">All Items</a></li>
			<li id="filter_news"><a href="#">News</a></li>
			<li id="filter_events"><a href="#">Events</a></li>
			<li id="filter_discussion"><a href="#">Discussion</a></li>
			<li id="filter_prayers"><a href="#">Prayers</a></li>
			<li id="filter_qandas"><a href="#">Q &amp; A</a></li>
		</ul>
	</div>
	<?php $this->load->view('connect/group/stream'); ?>
	<div id="sidebar">
		<div class="group_list">
			<h2>Ministries at <strong>Community Christian Church</strong></h2>
			<ul>
				<?php foreach ($ministries->result() as $ministry): ?>
					<li><a href="<?=site_url($this->groups_model->get_url($ministry->id))?>"><?=$ministry->name?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<br />
</div>