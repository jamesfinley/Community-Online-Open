<form method="post">
	<h2>Schedule &raquo; <span class="current_page">Edit Service Time &raquo; <?=$time?> on <?=$day_of_week?></span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<fieldset>
		<legend>Time <span class="amp">&amp;</span> Day of the Week</legend>
		<select name="day_of_week">
			<option value="0"<?=($service->day_of_week == 0 ? ' selected="selected"' : '')?>>Sunday</option>
			<option value="1"<?=($service->day_of_week == 1 ? ' selected="selected"' : '')?>>Monday</option>
			<option value="2"<?=($service->day_of_week == 2 ? ' selected="selected"' : '')?>>Tuesday</option>
			<option value="3"<?=($service->day_of_week == 3 ? ' selected="selected"' : '')?>>Wednesday</option>
			<option value="4"<?=($service->day_of_week == 4 ? ' selected="selected"' : '')?>>Thursday</option>
			<option value="5"<?=($service->day_of_week == 5 ? ' selected="selected"' : '')?>>Friday</option>
			<option value="6"<?=($service->day_of_week == 6 ? ' selected="selected"' : '')?>>Saturday</option>
		</select>
		<input type="text" name="start_time" placeholder="Start Time" class="secondary" value="<?=$time?>" />
	</fieldset>
	<fieldset id="save_form">
		<input type="submit" value="Save Service Time" /> or <a href="<?=site_url('admin/schedule')?>">cancel</a>
	</fieldset>
</form>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/schedule/add')?>">Add Service Time</a></li>
		<li><a href="<?=site_url('admin/schedule')?>" class="selected">View Schedule</a></li>
	</ul>
</div>