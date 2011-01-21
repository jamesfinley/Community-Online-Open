<html>
	<head>
		<title>Community Online &raquo; <?=($group->type === 'master' ? 'Church' : ($group->type === 'campus' ? $church->name : ($group->type === 'small group' ? $campus->name : 'Ministry')))?> &raquo; <?=$group->name?></title>
		<link rel="stylesheet" href="<?=base_url()?>resources/css/layout.css" />
		<link rel="stylesheet" href="<?=base_url()?>resources/css/group_page.css" />
		<!--<script src="http://maps.google.com/maps?file=api&amp;v=3&amp;sensor=false&amp;key=ABQIAAAAnkoB733Y-MTSisgUhFN9wRTibt1s9cKS-4sbFymcibSkNxvdpxQmTbJ75lhUcWsTCDCqOOtu445w3g" type="text/javascript"></script>-->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>resources/js/googlemaps.js"></script>
		<script type="text/javascript" src="<?=base_url()?>resources/js/ccc.js"></script>
		<script type="text/javascript" src="<?=base_url()?>resources/js/connect.js"></script>
		<script type="text/javascript" src="<?=base_url()?>resources/js/group_page.js"></script>
		<script type="text/javascript">
			$(function () {
				connect.display_images();
			});
		</script>
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
		<?php if($message): ?>
		<div id="notice"><div class="container"><?=$message?></div></div>
		<?php endif; ?>
		<?php if($error): ?>
		<div id="notice" class="error"><div class="container"><?=$error?></div></div>
		<?php endif; ?>
		<div id="group_page" class="container" rel="<?=$group->latitude?>,<?=$group->longitude?>">