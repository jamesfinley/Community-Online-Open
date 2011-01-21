<html>
	<head>
		<title>Community Online &raquo; Notes</title>
		<link rel="stylesheet" href="<?=base_url()?>resources/css/layout.css" />
		<link rel="stylesheet" href="<?=base_url()?>resources/css/notes.css" />
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