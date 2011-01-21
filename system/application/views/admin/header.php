<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Community Online &raquo; <?=$title?></title>
		<link rel="stylesheet" href="/resources/css/admin_services.css" />
		<link rel="stylesheet" href="/resources/css/admin_groups.css" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<?php
			if (isset($scripts)) {
				for ($i=0; $i < count($scripts); $i++) {
					?><script type="text/javascript" src="<?=$scripts[$i]?>"></script><?php
				}
			}
		?>
	</head>
	<body>
		<div id="header">
			<ul>
				<li><a href="/admin"<?=($page == 'services' ? ' class="current"' : '')?>>Services</a></li>
				<li><a href="/admin/schedule"<?=($page == 'schedule' ? ' class="current"' : '')?>>Service Schedule</a></li>
				<!--<li><a href="/admin/videos"<?=($page == 'videos' ? ' class="current"' : '')?>>Videos</a></li>-->
				<li><a href="/admin/groups"<?=($page == 'groups' ? ' class="current"' : '')?>>Groups</a></li>
				<li><a href="/admin/big_idea"<?=($page == 'big_idea' ? ' class="current"' : '')?>>Big Idea</a></li>
				<li><a href="/admin/users"<?=($page == 'users' ? ' class="current"' : '')?>>Users</a></li>
				<li><a href="/admin/settings"<?=($page == 'settings' ? ' class="current"' : '')?>>Settings</a></li>
			</ul>
			<br />
		</div>
		<div id="container">