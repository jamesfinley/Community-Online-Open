<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Community Online</title>
		<link rel="stylesheet" href="<?=base_url()?>resources/css/layout.css" />
		<link rel="stylesheet" href="<?=base_url()?>resources/css/celebrate.css" />
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
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.swfobject.min.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.easing.js"></script>
		<script src="<?=base_url()?>resources/js/ccc.js" type="text/javascript"></script>
		<script src="<?=base_url()?>resources/js/celebrate.js" type="text/javascript"></script>
		<script src="<?=base_url()?>resources/js/home.js" type="text/javascript"></script>

		<script type="text/javascript">
			$(function () {
				h.init();
			});
		</script>
	</head>
	<body onload="initFB();">
<!--		<div id="header">
			<div class="container">
				<h1>Community Online</h1>
				<ul>
					<li><a href="<?=site_url('')?>" class="selected">Celebrate</a></li>
					<li><a href="<?=site_url('connect/james_finley_s_group')?>">Connect</a></li>
					<li><a href="#">Contribute</a></li>
				</ul>
			</div>
		</div>-->
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
				<?php if($account === null): ?>
					<ul id="login_register">
						<li><a href="<?=site_url('account/login')?>">login</a></li>
						<li><a href="account/register">register</a></li>
					</ul>
				<?php else: ?>
					<a href="<?=site_url('account')?>" id="account_button"><?=$this->users->fullname($account->id)?></a>
					<ul id="account_menu">
						<li class="header">My Groups</li>
						<?php foreach ($groups_for_user->result() as $group_for_user): ?>
							<li class="group"><a href="<?=site_url('connect/'.($group_for_user->type == 'master' ? 'church' : ($group_for_user->type == 'small group' ? 'small_group' : $group_for_user->type)).'/'.$group_for_user->slug)?>"><?=$group_for_user->name?></a></li>
						<?php endforeach; ?>
						<li id="local_search_link"><a href="#">Search for Local Groups</a></li>
						<li id="logout_button"><a href="<?=site_url('account/logout')?>">Logout of Community</a></li>
					</ul>
				<?php endif; ?>
			</div>
		</div>