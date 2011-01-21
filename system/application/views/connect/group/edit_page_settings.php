<div id="group_page" class="container">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
	</div>
	<form action="" method="post" id="settings">
		<ul id="settings_navigation">
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings')?>">General</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/members')?>">Members</a></li>
			<li><a href="<?=site_url($this->groups_model->get_url($group->id).'/settings/pages')?>" class="selected">Pages</a></li>
		</ul>
		<h2>Group Settings &raquo; <span class="current_page">Edit Page</span></h2>
		<fieldset>
			<legend>Name &amp; Settings</legend>
			<div class="field">
				<label for="title_field">Title</label>
				<input type="text" id="title_field" name="title" value="<?=$page->title?>" />
			</div>
			<div class="field">
				<label for="slug_field">Slug</label>
				<input type="text" id="slug_field" name="slug" value="<?=$page->slug?>" />
			</div>
			<div class="field">
				<label for="show_in_sidebar_field">Show in Sidebar</label>
				<input name="show_in_sidebar" id="show_in_sidebar_field" type="checkbox" <?=$page->show_in_sidebar ? 'checked="checked"' : ''?> />
			</div>
		</fieldset>
		<?php if ($page->type == 'page'): ?>
		<fieldset>
			<div class="field">
				<label for="content_field">Content</label>
				<textarea name="content" id="content_field"><?=htmlentities($page->content)?></textarea>
			</div>
		</fieldset>
		<?php elseif ($page->type == 'giving'):
			$page->content = unserialize($page->content);
			print_r($page->content);
		?>
		<fieldset>
			<div class="field">
				<label for="emails_field">emails</label>
				<input type="text" name="content[email]" id="emails_field" value="<?=$page->content['email']?>" />
			</div>
		</fieldset>
		<fieldset>
			<legend>Language</legend>
			<div class="field">
				<label for="gift_field">gift</label>
				<!--<input type="text" name="content[email]" id="emails_field" value="<?=$page->content['email']?>" />-->
				<select name="content[gift_word]">
					<option value="gift"<?=($page->content['gift_word'] == 'gift' ? ' selected="selected"' : '')?>>gift</option>
					<option value="donation"<?=($page->content['gift_word'] == 'donation' ? ' selected="selected"' : '')?>>donation</option>
					<option value="payment"<?=($page->content['gift_word'] == 'payment' ? ' selected="selected"' : '')?>>payment</option>
				</select>
			</div>
			<div class="field">
				<label for="admin_largesubject_field">admin_largesubject</label>
				<input type="text" name="content[language][admin_largesubject]" id="admin_largesubject_field" value="<?=($page->content['language'] && $page->content['language']->admin_largesubject ? $page->content['language']->admin_largesubject : '')?>" />
			</div>
			<div class="field">
				<label for="admin_largeamount_field">admin_largeamount</label>
				<textarea name="content[language][admin_largeamount]" id="admin_largeamount_field"><?=($page->content['language'] && $page->content['language']->admin_largeamount ? $page->content['language']->admin_largeamount : '')?></textarea>
			</div>
			<div class="field">
				<label for="user_largeamount_field">user_largeamount</label>
				<textarea name="content[language][user_largeamount]" id="user_largeamount_field"><?=($page->content['language'] && $page->content['language']->user_largeamount ? $page->content['language']->user_largeamount : '')?></textarea>
			</div>
			<div class="field">
				<label for="arb_subject_field">arb_subject</label>
				<input type="text" name="content[language][arb_subject]" id="arb_subject_field" value="<?=($page->content['language'] && $page->content['language']->arb_subject ? $page->content['language']->arb_subject : '')?>" />
			</div>
			<div class="field">
				<label for="arb_header_field">arb_header</label>
				<input type="text" name="content[language][arb_header]" id="arb_header_field" value="<?=($page->content['language'] && $page->content['language']->arb_header ? $page->content['language']->arb_header : '')?>" />
			</div>
		</fieldset>
		<?php endif; ?>
		<input type="submit" value="Update Page" class="wymupdate" />
		<!--
		<fieldset>
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
		</fieldset>
		
		<input type="submit" name="submit" value="Update Group" />//-->
	</form>
	<br />
</div>
<script>
	$(function () {
		$('#content_field').wymeditor({
			toolsItems: [
				{'name': 'Bold', 'title': 'Strong', 'css': 'wym_tools_strong'}, 
				{'name': 'Italic', 'title': 'Emphasis', 'css': 'wym_tools_emphasis'},
				{'name': 'Undo', 'title': 'Undo', 'css': 'wym_tools_undo'},
				{'name': 'Redo', 'title': 'Redo', 'css': 'wym_tools_redo'},
				{'name': 'CreateLink', 'title': 'Link', 'css': 'wym_tools_link'},
				{'name': 'Unlink', 'title': 'Unlink', 'css': 'wym_tools_unlink'},
				{'name': 'InsertImage', 'title': 'Image', 'css': 'wym_tools_image'},
				{'name': 'Paste', 'title': 'Paste_From_Word', 'css': 'wym_tools_paste'},
				{'name': 'ToggleHtml', 'title': 'HTML', 'css': 'wym_tools_html'}
			],
			boxHtml: "<div class='wym_box'>"
				+ "<div class='wym_area_top'>"
				+ WYMeditor.TOOLS
				+ "</div>"
				+ "<div class='wym_area_left'></div>"
				+ "<div class='wym_area_right'>"
				+ "</div>"
				+ "<div class='wym_area_main'>"
				+ WYMeditor.HTML
				+ WYMeditor.IFRAME
				+ WYMeditor.STATUS
				+ "</div>"
				+ "<div class='wym_area_bottom'>"
				+ "</div>"
				+ "</div>"
		});
	});
</script>