<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Community Online</title>
		<link rel="stylesheet" href="<?=base_url()?>resources/css/layout.css" />

		<?php if($this->uri->segment(1) !== 'login'): ?>
		<script type="text/javascript">
			function initFB() {
				FB_RequireFeatures(["XFBML"], function(){
					FB.init('<?=$this->config->item('facebook_connect_api_key')?>', '<?=base_url()?>xd_receiver.htm');
				});
			}
		</script>
		<?php else: ?>
		<script type="text/javascript">
			function initFB() {
				FB.init('<?=$this->config->item('facebook_connect_api_key')?>', '<?=base_url()?>xd_receiver.htm', {"reloadIfSessionStateChanged":true});
			}
		</script>
		<?php endif; ?>
		<script type="text/javascript" src="/resources/js/jquery-1.4.1.min.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.swfobject.min.js"></script>
		<script src="<?=base_url()?>resources/js/ccc.js" type="text/javascript"></script>
		<script src="<?=base_url()?>resources/js/celebrate.js" type="text/javascript"></script>
		<?php if ($this->uri->segment(1) !== 'login' && isset($account)): ?>
		<script type="text/javascript">
			ccc._user = <?=$account->id?>;
		</script>
		<?php endif; ?>
	</head>
	<body>
		<div id="header">
			<div class="container">
				<h1><a href="<?=site_url()?>">Community Online</a></h1>
				<ul id="primary_navigation">
					<li><a href="<?=site_url('connect/church/naperville_yb')?>">Locations</a></li>
					<li><a href="<?=site_url('connect/ministry/stuco')?>">Ministries</a></li>
					<li><a href="<?=site_url('connect/ministry/stuco')?>">Groups</a></li>
				</ul>
				<ul id="secondary_navigation">
					<li><a href="<?=site_url('watch')?>">Watch</a></li>
					<li><a href="<?=site_url('give')?>">Give</a></li>
					<li><a href="<?=site_url('serve')?>">Serve</a></li>
				</ul>
			</div>
		</div>
		<?php if($message): ?>
		<div id="notice"><div class="container"><?=$message?></div></div>
		<?php endif; ?>
		<?php if($error): ?>
		<div id="notice" class="error"><div class="container"><?=$error?></div></div>
		<?php endif; ?>
		<div id="content" class="container">