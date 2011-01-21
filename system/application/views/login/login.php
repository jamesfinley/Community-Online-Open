<!DOCTYPE html>
<html>
	<head>
		<title>Community Online &raquo; Login</title>
		<link rel="stylesheet" href="<?=base_url()?>resources/css/login.css" />
	</head>
	<body>
<!--<script type="text/javascript">
	function fbConnect() {
		FB.init("", "xd_receiver.htm",{"reloadIfSessionStateChanged":true});
	}
</script>
Login with Facebook: <fb:login-button length="long" background="light" size="medium"></fb:login-button>//-->

<form method="post" action="" id="login_form">
	<h2>Login to <strong>Community Online</strong></h2>
	<?php if ($error): ?>
		<div id="notice" class="error"><?=$error?></div>
	<?php endif; ?>
	<label for="email_field">Email:</label><br />
	<input type="text" name="email" id="email_field" value="" /><br />
	<label for="password_field">Password:</label><br />
	<input type="password" name="password" id="password_field" value="" /><br />
	<input type="submit" value="Login" />
	<a href="<?=site_url('reset')?>">I forgot my password?</a>
</form>