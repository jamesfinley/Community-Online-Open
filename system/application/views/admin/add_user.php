<form method="post">
	<h2>Users &raquo; <span class="current_page">Add a User</span></h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	<input type="text" name="email" placeholder="Email" class="primary" /><br />
	<input type="text" name="password" placeholder="Password" /><br />
	<input type="text" name="first_name" placeholder="First Name" /><br />
	<input type="text" name="last_name" placeholder="Last Name" /><br />

	<fieldset>
		<legend>Roles</legend>
		<label for="campus_pastor">Campus Pastor</label>
		<select name="campus_pastor" id="campus_pastor">			
			<option value="-1">Not a Campus Pastor</option>
			<optgroup label="Campuses">
			<?php foreach($campuses as $campus): ?>
				<option value=<?=$campus->id?>><?=$campus->name?></option>
			<?php endforeach; ?>
			</optgroup>
		</select>
	</fieldset>

	<fieldset id="save_form">
		<input type="submit" value="Save User" /> or <a href="<?=site_url('admin/users')?>">cancel</a>
	</fieldset>
</form>
<div id="sidebar">
	<h2>Navigate Schedule</h2>
	<ul>
		<li><a href="<?=site_url('admin/users/add')?>" class="selected">Add User</a></li>
		<li><a href="<?=site_url('admin/users')?>">View Users</a></li>
	</ul>
</div>