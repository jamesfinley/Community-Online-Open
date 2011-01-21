<div id="group_page" class="container" rel=",">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
	</div>
	<form action="" id="settings" method="post">
		<ul id="settings_navigation">
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings')?>" class="selected">General</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members')?>">Members</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages')?>">Pages</a></li>
		</ul>
		<h2>Group Settings &raquo; <span class="current_page">General</span></h2>
		
		<fieldset>
			<legend>About</legend>
			<div class="field">
				<label for="name_field">Group Name</label><br />
				<input name="name" id="name_field" type="text" value="<?=$group->name?>" />
			</div>
			<div class="field">
				<label for="slug_field">Group URL</label><br />
				<input name="slug" id="slug_field" type="text" value="<?=$group->slug?>" /> <?=site_url($this->groups_model->get_url($group->id))?>
			</div>
			<div class="field">
				<label for="description">Description</label>
				<textarea name="description" id="description" rows="5"><?=$group->description?></textarea>
			</div>
			<div class="field">
				<label for="service_times">Service Times (If Campus)</label>
				<textarea name="service_times" id="service_times" rows="5"><?=$group->service_times?></textarea>
			</div>
		</fieldset>
		<!--<fieldset>
			<legend>Sections</legend>
			<div class="field">
				<label for="hide_news">Hide News</label>
				<input name="hide_news" id="hide_news" type="checkbox" <?=$group->hide_news ? 'checked="true"' : NULL?> />
			</div>
			<div class="field">
				<label for="hide_events">Hide Events</label>
				<input name="hide_events" id="hide_events" type="checkbox" <?=$group->hide_events ? 'checked="true"' : NULL?> />
			</div>
			<div class="field">
				<label for="hide_discussion">Hide Discussion</label>
				<input name="hide_discussion" id="hide_discussion" type="checkbox" <?=$group->hide_discussion ? 'checked="true"' : NULL?> />
			</div>
			<div class="field">
				<label for="hide_prayers">Hide Prayers</label>
				<input name="hide_prayers" id="hide_prayers" type="checkbox" <?=$group->hide_prayers ? 'checked="true"' : NULL?> />
			</div>
			<div class="field">
				<label for="hide_qna">Hide Q & A</label>
				<input name="hide_qna" id="hide_qna" type="checkbox" <?=$group->hide_qna ? 'checked="true"' : NULL?> />
			</div>
		</fieldset>//-->
		
		<input type="submit" name="submit" value="Update Group" />
	</form>
	<br />
</div>