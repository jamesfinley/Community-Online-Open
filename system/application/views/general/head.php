<?php
	$agent = $this->agent->agent_string();
	$is_mobile = false;
	if (preg_match('/AppleWebKit/', $agent))
	{
		//is webOS
		$is_webos   = preg_match('/webOS/', $agent) > 0 ? true : false;
		
		//is Android
		$is_android = preg_match('/Android/', $agent) > 0 ? true : false;
		
		//is Mobile
		$is_mobile  = preg_match('/Mobile/', $agent) > 0 ? true : false;
		
		if ($is_mobile || $is_android || $is_webos)
		{
			$is_mobile = true;
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<title>Community Christian Church <?=isset($title) ? '&raquo; ' . $title : NULL ?></title>
		<!--<meta name="viewport" content="initial-scale=1.0, width=device-width, maximum-scale=1.0" />-->
		<?php for ($i=0, $len=count($css_files); $i<$len; $i++): ?>
		<link rel="stylesheet" href="<?=$css_files[$i].'?'.time()?>" />
		<?php endfor; ?>
		<?php if ($this->uri->segment(1) == 'notes'): ?>
		<link rel="stylesheet" href="/resources/css/notes_print.css?<?=time()?>" media="print" />
		<?php endif; ?>
		<?php for ($i=0, $len=count($js_files); $i<$len; $i++): ?>
		<script src="<?=$js_files[$i].'?'.time()?>"></script>
		<?php endfor; ?>
		<?php if (isset($rss_files)): ?>
		<?php for ($i=0, $len=count($rss_files); $i<$len; $i++): ?>
		<link rel="alternate" type="application/rss+xml" title="RSS" href="<?=$rss_files[$i]?>" />
		<?php endfor; ?>
		<?php endif; ?>
		<?php if ($is_mobile): ?>
			<link rel="stylesheet" href="/resources/css/mobile.css?<?=time()?>" />
			<script src="<?=base_url()?>resources/js/mobile.js<?='?'.time()?>"></script>
			<meta name="viewport" content="initial-scale=1.0, width=device-width, maximum-scale=1.0" />
			<meta name="apple-mobile-web-app-capable" content="yes" />
			<meta name="apple-mobile-web-app-status-bar-style" content="black" />
			<link rel="apple-touch-icon" href="<?=base_url()?>resources/images/mobile_icon.png" />
		<?php endif; ?>
		<meta property="og:site_name" content="Community Christian Church" />
		<?php if (isset($stream)): ?>
		<meta property="og:title" content="<?=$stream->subject?>" />
		<?php 
			if ( $stream->image )
			{
				$path = 'user_images/post_images/'.$stream->image;
				
				if ( file_exists($path) )
				{
					$display_width = 125;
				
					list($width, $height) = getimagesize($path);
						
					$display_height = round(($height * $display_width) / $width);	
											
					$image_url = site_url(array('images', 'rect', $display_width, $display_height, 'post_image', $stream->image));							
					echo '<meta property="og:image" content="'.$image_url.'" />';
				}
			}
		?>
		<?php endif; ?>
		<script>
			var is_mobile = <?=$is_mobile ? 'true' : 'false'?>;
		</script>
		<!--[if IE]>
		<link rel="stylesheet" href="/resources/css/ie.css<?='?'.time()?>" />
		<![endif]-->
		<link href="<?=base_url()?>favicon.ico" rel="shortcut icon" type="image/x-icon" />
		
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try{
		var pageTracker = _gat._getTracker("UA-4375281-1");
		pageTracker._trackPageview();
		} catch(err) {}
		</script>
		<?php if (isset($thisPage)): ?>
		<script>
			var thisPage = '<?=$thisPage?>';
		</script>
		<?php endif; ?>
	</head>
	<body>
		<?php if ($is_mobile === false): ?>
		<!--[if lt IE 8]>
				<div id="if_ie"><div class="container">During beta testing, we cannot guarantee the best user experience in Internet Explorer. Please use Firefox instead.</div></div>
			<![endif]-->
			<?php if (base_url() != 'https://communitychristian.org/' && base_url() != 'http://communitychristian.org/'): ?>
				<div id="if_ie"><div class="container">This is a test server. <a href="https://communitychristian.org">Please click here to continue to our live site.</a></div></div>
			<?php endif; ?>
			<div id="header"<?=((isset($hide_navigation) && $hide_navigation === true)) ? ' class="hide_navigation"' : ''?>>
				<div class="container">
					<h1><a href="<?=site_url()?>">Community Christian Church</a></h1>
					<?php if ((isset($hide_navigation) && $hide_navigation === false) || !isset($hide_navigation)): ?>
					<ul id="primary_navigation">
						<li id="locations_navigation">
							<a href="<?=site_url('locations')?>">Locations</a>
							<ul>
								<li><a href="<?=site_url('locations/naperville-yb')?>">Naperville Yellow Box</a></li>
								<li><a href="<?=site_url('locations/naperville-downtown')?>">Naperville Downtown</a></li>
								<li><a href="<?=site_url('locations/eastaurora')?>">East Aurora</a></li>
								<li><a href="<?=site_url('locations/romeoville')?>">Romeoville</a></li>
								<li><a href="<?=site_url('locations/shorewood')?>">Shorewood</a></li>
								<li><a href="<?=site_url('locations/montgomery')?>">Montgomery</a></li>
								<li><a href="<?=site_url('locations/carillon')?>">Carillon</a></li>
								<li><a href="<?=site_url('locations/yorkville')?>">Yorkville</a></li>
								<li><a href="<?=site_url('locations/plainfield')?>">Plainfield</a></li>
								<li><a href="<?=site_url('locations/lemont')?>">Lemont</a></li>
								<li><a href="<?=site_url('locations/lincolnsquare')?>">Lincoln Square</a></li>
								<li><a href="<?=site_url('ministries/online')?>">Community Online</a></li>
							</ul>
						</li>
						<li id="ministries_navigation">
							<a href="<?=site_url('ministries')?>">Ministries</a>
							<ul>
							<!--
							<?php
								$groups = $this->groups_model->items(0, 'ministry');
								foreach ($groups->result() as $g): ?>
							<li><a href="<?=site_url('ministries/'.$g->slug)?>"><?=$g->name?></a></li>
							<?php endforeach; ?>
							//-->
								<li><a href="<?=site_url('ministries/adults')?>">Adult Small Groups</a></li>
								<li><a href="<?=site_url('ministries/students')?>">Student Community</a></li>
								<li><a href="<?=site_url('ministries/kids')?>">Kids' City</a></li>
								<li><a href="<?=site_url('ministries/community412')?>">Compassion and Justice</a></li>
								<li><a href="<?=site_url('ministries/arts')?>">Creative Arts</a></li>
								<li><a href="<?=site_url('supportandrecovery')?>">Support and Recovery</a></li>
							</ul>
						</li>
						<li id="groups_navigation">
							<a href="<?=site_url('groups')?>">Groups</a>
							<ul>
								<?php if ($account !== null && $groups_for_user->num_rows() !== 0): ?>
									<?php foreach ($groups_for_user->result() as $g): ?>
										<li class="<?=($g->type !== 'small group' ? $g->type : 'sg')?>"><a href="<?=site_url($this->groups_model->get_url($g->id))?>"><?=$g->name?></a></li>
									<?php endforeach; ?>
									<li class="finder"><a href="<?=site_url('groups')?>">Small Group Finder</a></li>
								<?php else: ?>
									<li class="finder"><a href="<?=site_url('groups')?>">Small Group Finder</a></li>
								<?php endif; ?>
							</ul>
						</li>
					</ul>
					<ul id="secondary_navigation">
						<?php
							$service = $this->services_model->next_service();
							if ($service): ?>
						<li><a href="<?=site_url('watch')?>" class="live_service">Live Now</a></li>
						<?php else: ?>
							<li><a href="<?=site_url('pages/video')?>">Watch</a></li>
						<?php endif; ?>
						<li><a href="<?=site_url('give', true)?>" id="nav_give">Give</a></li>
						<li><a href="<?=site_url('pages/serve')?>">Serve</a></li>
					</ul>
					<?php if($account): ?>
						<?php
							$notifications = $this->notifications_model->unread($account->id);
						?>
						<div id="notifications_ribbon">
							<div id="notification_count">
								<?=$notifications->num_rows()?>
							</div>
							<div id="notification_window">
								<h2>Notifications <a href="#">mark all as read</a></h2>
								<ul>
									<?php foreach($notifications->result() as $notification): ?>
										<?php
											if ($notification->message)
											{
												$notification->message = unserialize($notification->message);
											}
										?>
										<li class="unread" data-id="<?=$notification->id?>" data-link="<?=($notification->message ? $notification->message['link'] : '')?>"><a href="<?=($notification->message ? $notification->message['link'] : '#')?>"><?=preg_replace('/\<a/', '<strong', preg_replace('/\<\/a\>/', '</strong>', $notification->short_message))?> <span class="created_at"><?=ago($notification->created_at)?></span></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php else: /* mobile header */ ?>
			<div id="header"<?=((isset($hide_navigation) && $hide_navigation === true)) ? ' class="hide_navigation"' : ''?>>
				<div class="container">
					<h1><a href="<?=base_url()?>">Community Online</a></h1>
					<?php if ((isset($hide_navigation) && $hide_navigation === false) || !isset($hide_navigation)): ?>
					<ul id="primary_navigation">
						<li>
							<a href="<?=site_url('locations')?>">Locations</a>
							<select>
								<option value=""></option>
								<option value="<?=site_url('locations')?>">select a location</option>
								<?php
									$groups = $this->groups_model->items(0, 'campus');
									foreach ($groups->result() as $g): ?>
									<option value="<?=site_url('locations/'.$g->slug)?>"><?=$g->name?></option>
								<?php endforeach; ?>
								<option value="<?=site_url('ministries/online')?>">Community Online</option>
							</select>
						</li>
						<li>
							<a href="<?=site_url('ministries')?>">Ministries</a>
							<select>
								<option value=""></option>
								<option value="<?=site_url('ministries')?>">All Ministries</option>
								<option value="<?=site_url('ministries/adults')?>">Adults</option>
								<option value="<?=site_url('ministries/students')?>">Students</option>
								<option value="<?=site_url('ministries/kids')?>">Kid's City</option>
								<option value="<?=site_url('ministries/community412')?>">Compassion and Justice</option>
								<option value="<?=site_url('ministries/arts')?>">Creative Arts</option>
								<option value="<?=site_url('supportandrecovery')?>">Support and Recovery</option>
							</select>
						</li>
						<li>
							<a href="<?=site_url('groups')?>">Groups</a>
							<select>
								<option value=""></option>
								<?php if ($account !== null && $groups_for_user->num_rows() !== 0): ?>
									<?php foreach ($groups_for_user->result() as $g): ?>
										<option value="<?=site_url($this->groups_model->get_url($g->id))?>"><?=$g->name?></option>
									<?php endforeach; ?>
									<option value="<?=site_url('groups')?>">Find a Group</option>
								<?php else: ?>
									<option value="<?=site_url('groups')?>">Find a Group</option>
								<?php endif; ?>
							</select>
						</li>
						<li><a href="<?=site_url('give', true)?>">Give</a></li>
						<li><a href="<?=site_url('pages/serve')?>">Serve</a></li>
					</ul>
					<?php if($account !== null): ?>
						<?php
							$notifications = $this->notifications_model->unread($account->id);
						?>
						<div id="notifications_ribbon">
							<div id="notification_count">
								<?=$notifications->num_rows()?>
							</div>
							<div id="notification_window">
								<h2>Notifications</h2>
								<ul>
									<?php foreach($notifications->result() as $notification): ?>
										<li class="unread" data-id="<?=$notification->id?>" data-link="<?=$notification->link?>"><?=$notification->short_message?> <span class="created_at"><?=ago($notification->created_at)?></span></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if (isset($group) && (isset($streams) || isset($stream)) && $group->type === 'campus'): ?>
		<div id="group_info_and_photos">
			<div class="container">
				<div id="groups_photo_banner_gradient"></div>
				<div id="group_info">
					<div class="description"><?=$group->description?></div>
					<?php if ($group->address): ?>
					<div class="address">
						<?=$group->address?><br />
						<?=$group->city?>, <?=$group->state?> <?=$group->zip_code?><br />
						<?php if (isset($group) && $group->id == 13): ?>
						<a href="http://maps.google.com/maps?q=<?=urlencode($group->latitude)?>+<?=urlencode($group->longitude)?>">Get Directions</a>
						<?php else: ?>
						<a href="http://maps.google.com/maps?q=<?=urlencode($group->address)?>+<?=urlencode($group->city)?>+<?=urlencode($group->state)?>">Get Directions</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if($group->service_times): ?>
						<div class="times">
							<h3>Service Times</h3>
							<?=nl2br($group->service_times)?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php elseif(isset($group) && $group->type === 'ministry' && $group->ministry_logo): ?>
		<div id="group_info_and_photos">
			<div class="container">
				<img id="groups_photo_banner_image" src="<?=base_url()?>user_images/group_images/<?=$group->ministry_logo?>" />
				<div id="groups_photo_banner_gradient"></div>
			</div>
		</div>
		<?php endif; ?>
		<?php if(isset($message) && $message): ?>
		<div id="notice"><div class="container"><?=$message?></div></div>
		<?php endif; ?>
		<?php if(isset($error) && $error): ?>
		<div id="notice" class="error"><div class="container"><?=$error?></div></div>
		<?php endif; ?>
		<div id="content" class="container">