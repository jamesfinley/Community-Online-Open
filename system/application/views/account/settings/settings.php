<div id="container">
<form method="post" action="" id="user_account" enctype="multipart/form-data">
	<h2>Account Settings</h2>
	<label for="email_field">Email:</label>
	<input type="text" name="email" id="email_field" class="primary" value="<?=$account->email?>" />
	<label for="first_name_field">First Name:</label>
	<input type="text" name="first_name" id="first_name_field" value="<?=$account->first_name?>"  />
	<label for="last_name_field">Last Name:</label>
	<input type="text" name="last_name" id="last_name_field" value="<?=$account->last_name?>"  />
	<fieldset>
		<legend>Change Your Password</legend>
		<label for="password_field">New Password:</label>
		<input type="password" name="password" id="password_field" />
		<label for="confirm_password">Confirm New Password:</label>
		<input type="password" name="confirm_password" id="confirm_password_field"  />
	</fieldset>
	<!--<fieldset>
		<legend>Avatar</legend>
		<label>Current Avatar:</label>
		<img src="<?=base_url()?>images/square/75/avatars/<?=$account->avatar?>" />
		<label for="avatar_field">New Avatar:</label>
		<input type="file" name="avatar" id="avatar_field" />
	</fieldset>//-->
	
	<input type="submit" value="Update Information" />
</form>
</div>