<div id="group_page" class="container" rel=",">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
		<ul id="filter_stream">
		</ul>
	</div>
	<form action="" method="post" id="settings">
		<h2>Group Settings &raquo; <span class="current_page">Email Notifications</span></h2>
		
		<fieldset>
			<legend>Daily Digest</legend>
			<p>When Daily Digest is turned on for a group, you will only receive one email a day, around 4pm, with all the activity within this group. If the group is super active, this helps cut down on your inbox clutter.</p>
			<input type="checkbox" id="receive_digest_field" name="receive_digest"<?=($receive_digest ? ' checked="checked"' : '')?> /> <label for="receive_digest_field">Turn on Daily Digest</label>
		</fieldset>
		
		<fieldset id="individual_emails">
			<legend>Individual Emails</legend>
			<p>When Daily Digest is turned on for this group, these settings do not apply.</p>
			<input type="checkbox" id="receive_news_field" name="receive_news"<?=($receive_news ? ' checked="checked"' : '')?> /> <label for="receive_news_field">News</label><br />
			<input type="checkbox" id="receive_events_field" name="receive_events"<?=($receive_news ? ' checked="checked"' : '')?> /> <label for="receive_events_field">Events</label><br />
			<input type="checkbox" id="receive_contributions_field" name="receive_contributions"<?=($receive_contributions ? ' checked="checked"' : '')?> /> <label for="receive_events_field">Contribution Opportunities</label><br />
			<input type="checkbox" id="receive_discussions_field" name="receive_discussions"<?=($receive_discussions ? ' checked="checked"' : '')?> /> <label for="receive_discussions_field">Discussions</label><br />
			<input type="checkbox" id="receive_prayers_field" name="receive_prayers"<?=($receive_prayers ? ' checked="checked"' : '')?> /> <label for="receive_prayers_field">Prayers</label><br />
		</fieldset>
		
		<input type="submit" name="submit" value="Update Settings" />
	</form>
	<?php $this->load->view('connect/group/sidebar'); ?>
	<br />
</div>

<script type="text/javascript">
	$(function () {
		$('#receive_digest_field').change(function () {
			if ($(this).attr('checked') == true) {
				$('#individual_emails').css('opacity', '.5');
			}
			else {
				$('#individual_emails').css('opacity', '1');
			}
		});
		if ($('#receive_digest_field').attr('checked') == true) {
			$('#individual_emails').css('opacity', '.5');
		}
		connect.display_images();
	});
</script>