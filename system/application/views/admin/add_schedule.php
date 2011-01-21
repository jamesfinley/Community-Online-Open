<form method="post">
	<h2>Schedule &raquo; <span class="current_page">Add a Service Time</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<fieldset>
		<legend>Time <span class="amp">&amp;</span> Day of the Week</legend>
		<select name="day_of_week">
			<option value="0">Sunday</option>
			<option value="1">Monday</option>
			<option value="2">Tuesday</option>
			<option value="3">Wednesday</option>
			<option value="4">Thursday</option>
			<option value="5">Friday</option>
			<option value="6">Saturday</option>
		</select>
		<input type="text" name="start_time" placeholder="Start Time" class="secondary" value="09:00 AM" />
	</fieldset>
	<fieldset id="save_form">
		<input type="submit" value="Save Service Time" /> or <a href="<?=site_url('admin/schedule')?>">cancel</a>
	</fieldset>
</form>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/schedule/add')?>" class="selected">Add Service Time</a></li>
		<li><a href="<?=site_url('admin/schedule')?>">View Schedule</a></li>
	</ul>
</div>