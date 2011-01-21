<form method="post">
	<h2>Groups &raquo; <span class="current_page">Add a Group</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<input type="text" name="name" placeholder="Group Name" class="primary" /><br />
	<input type="text" name="slug" placeholder="Group Slug" /><br />
	<fieldset>
		<legend>About</legend>
		<label for="type">Type of Group</label>
		<select name="type" id="type">
			<option value="campus">Campus</option>
			<option value="small_group" selected="true">Small Group</option>
			<option value="ministry">Ministry</option>
		</select>
		<label for="description">Description</label>
		<textarea name="description" id="description" rows="5"></textarea>	
		<label for="service_times">Service Times (If Campus)</label>
		<textarea name="service_times" id="service_times" rows="5"></textarea>	
	</fieldset>
	<fieldset>
		<legend>Sections</legend>
		<label for="hide_news">Hide News</label>
		<input name="hide_news" id="hide_news" type="checkbox" /><br />
		<label for="hide_events">Hide Events</label>
		<input name="hide_events" id="hide_events" type="checkbox" /><br />
		<label for="hide_discussion">Hide Discussion</label>
		<input name="hide_discussion" id="hide_discussion" type="checkbox" /><br />
		<label for="hide_prayers">Hide Prayers</label>
		<input name="hide_prayers" id="hide_prayers" type="checkbox" /><br />
		<label for="hide_qna">Hide Q & A</label>
		<input name="hide_qna" id="hide_qna" type="checkbox" /><br />
	</fieldset>
	<fieldset>
		<legend>Address</legend>
		<input type="text" name="address" placeholder="Address"/><br />
		<input type="text" name="city" placeholder="City" /><br />
		<input type="text" name="zip_code" placeholder="Zip Code" /><br />
		<input type="text" name="state" placeholder="State" /><br />
		<input type="text" name="country" placeholder="Country" /><br />
	</fieldset>
	<fieldset>
		<legend>Coordinates</legend>
		<input type="text" name="latitude" placeholder="Latitude" /><br />
		<input type="text" name="longitude" placeholder="Longitude" /><br />
	</fieldset>

	<fieldset id="save_form">
		<input type="submit" value="Save Group" /> or <a href="<?=site_url('admin/groups')?>">cancel</a>
	</fieldset>
</form>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/groups/add')?>" class="selected">Add Group</a></li>
		<li><a href="<?=site_url('admin/groups')?>">View Groups</a></li>
	</ul>
</div>