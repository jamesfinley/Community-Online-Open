<form method="post">
	<h2>Groups &raquo; <span class="current_page">Edit a Group</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<input type="text" name="name" placeholder="Group Name" class="primary" value="<?=$group->name?>" /><br />
	<input type="text" name="slug" placeholder="Group Slug"  value="<?=$group->slug?>"/><br />
	<fieldset>
		<legend>About</legend>
		<label for="facilitator">Facilitator</label>
		<select name="facilitator" id="facilitator">
			<option value="-1">This group does not have a facilitator.</option>
			<optgroup label="People">
			<?php foreach($users as $user): ?>
				<option value="<?=$user->id?>" <?=$user->id == $facilitator_id ? 'selected="true"' : NULL ?>><?=$user->first_name?> <?=$user->last_name?></option>
			<?php endforeach; ?>
			</optgroup>
		</select>
		<label for="type">Type of Group</label>
		<select name="type" id="type">
			<option value="small_group" <?php echo $group->type == 'small_group' ? 'selected="true"' : FALSE ?>>Small Group</option>
			<option value="campus" <?php echo $group->type == 'campus' ? 'selected="true"' : FALSE ?>>Campus</option>
			<option value="ministry" <?php echo $group->type == 'ministry' ? 'selected="true"' : FALSE ?>>Ministry</option>
		</select>
		<label for="description">Description</label>
		<textarea name="description" id="description" rows="5"><?=$group->description?></textarea>	
		<label for="service_times">Service Times (If Campus)</label>
		<textarea name="service_times" id="service_times" rows="5"><?=$group->service_times?></textarea>	
	</fieldset>
	<fieldset>
		<legend>Sections</legend>
		<label for="hide_news">Hide News</label>
		<input name="hide_news" id="hide_news" type="checkbox" <?=$group->hide_news ? 'checked="true"' : NULL?> /><br />
		<label for="hide_events">Hide Events</label>
		<input name="hide_events" id="hide_events" type="checkbox" <?=$group->hide_events ? 'checked="true"' : NULL?> /><br />
		<label for="hide_discussion">Hide Discussion</label>
		<input name="hide_discussion" id="hide_discussion" type="checkbox" <?=$group->hide_discussion ? 'checked="true"' : NULL?> /><br />
		<label for="hide_prayers">Hide Prayers</label>
		<input name="hide_prayers" id="hide_prayers" type="checkbox" <?=$group->hide_prayers ? 'checked="true"' : NULL?> /><br />
		<label for="hide_qna">Hide Q & A</label>
		<input name="hide_qna" id="hide_qna" type="checkbox" <?=$group->hide_qna ? 'checked="true"' : NULL?> /><br />
	</fieldset>
	<fieldset>
		<legend>Address</legend>
		<input type="text" name="address" placeholder="Address" value="<?=$group->address?>"/><br />
		<input type="text" name="city" placeholder="City" value="<?=$group->city?>"/><br />
		<input type="text" name="zip_code" placeholder="Zip Code" value="<?=$group->zip_code?>"/><br />
		<input type="text" name="state" placeholder="State" value="<?=$group->state?>"/><br />
		<input type="text" name="country" placeholder="Country" value="<?=$group->country?>"/><br />
	</fieldset>
	<fieldset>
		<legend>Coordinates</legend>
		<input type="text" name="latitude" placeholder="Latitude" value="<?=$group->latitude?>" /><br />
		<input type="text" name="longitude" placeholder="Longitude" value="<?=$group->longitude?>"/><br />
	</fieldset>

	<fieldset id="save_form">
		<input type="submit" value="Save Group" /> or <a href="<?=site_url('admin/groups')?>">cancel</a>
	</fieldset>
</form>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/groups/add')?>">Add Group</a></li>
		<li><a href="<?=site_url('admin/groups')?>" class="selected">View Groups</a></li>
	</ul>
</div>