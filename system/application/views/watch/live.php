<?php if ($account): ?>
<script type="text/javascript">
	ccc._user = <?=($account ? $account->id : 'null')?>;
	ccc._allowTwitter = <?=$this->twitter_model->has_access($account->id) ? 1 : 0?>;
</script>
<?php endif; ?>
<?php if (isset($service)): ?>
<script type="text/javascript">
	c._notes = "<?=addslashes(str_replace(array("\n\r", "\n", "\r"), "\\n", $notes));?>";

	c.group_id = <?=($group ? $group->id : 'null')?>;
	c.service_id = <?=$service->id?>;
	c.twitter.query = "<?=$query?>";
	$(function () {
		$('#video_player').flash({'width': 600, 'height': 338, 'swf': '/resources/flash/video_player.swf', 'FlashVars': {'service_id': <?=$service->id?>}, allowfullscreen: true, wmode: 'transparent'});
	});
</script>
<?php endif; ?>

<div class="container">
	<div id="video_box">
		<h2><?=$service->big_idea?> <span class="series"><?=$service->series_title?></span></h2>
		<div id="video_player">
			<div id="no_video_player">
				<h3>Hey, if you see this, we'd like to help you help us.</h3>
				<ol>
					<li><span>Do you have <a href="http://adobe.com/go/getflashplayer">Flash Player</a> installed?</span></li>
					<li><span>Are you using Firefox with Ad Blocker? Make sure you have restrictions turned off for us.</span></li>
					<li><span>Do you have JavaScript turned off? Make sure that JavaScript is enabled in your browser, as Community Online and many other sites require this.</span></li>
					<li><span class="final">Dude, all of these are good, but still no video!?! Email us with your browser (Firefox, Safari, Opera, Chrome, Internet Explorer, etc.) and OS (Mac or Windows) and we'll help troubleshoot.</span></li>
				</ol>
			</div>
		</div>
		<div id="dynamic_content"></div>
	</div>
	<div id="sidebar">
		<ul id="sidebar_tabs">
			<li id="chat_tab_button"><a href="#" class="selected">chat</a></li>
			<li id="notes_tab_button"><a href="#">notes</a></li>
			<li id="twitter_tab_button"><a href="#">twitter</a></li>
		</ul>
		<div id="open_tab">
			<div id="chat_tab">
				<div id="member_status"></div>
				<div id="chatroom"></div>
				<div id="chatbar"></div>
			</div>
		</div>
	</div>
</div>