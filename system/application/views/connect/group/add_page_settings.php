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
		<h2>Group Settings &raquo; <span class="current_page">Add Page</span></h2>
		<fieldset>
			<legend>Name &amp; Settings</legend>
			<div class="field">
				<label for="title_field">Title</label>
				<input type="text" id="title_field" name="title"<?=(isset($_POST['title']) ? ' value="'.$_POST['title'].'"' : '')?> />
			</div>
			<div class="field">
				<label for="slug_field">Slug</label>
				<input type="text" id="slug_field" name="slug"<?=(isset($_POST['slug']) ? ' value="'.$_POST['slug'].'"' : '')?> />
			</div>
			<div class="field">
				<label for="show_in_sidebar_field">Show in Sidebar</label>
				<input name="show_in_sidebar" id="show_in_sidebar_field" type="checkbox" />
			</div>
		</fieldset>
		<fieldset>
			<div class="field">
				<label for="content_field">Content</label>
				<textarea name="content" id="content_field"><?=(isset($_POST['content']) ? $_POST['content'] : '')?></textarea>
			</div>
		</fieldset>
		<input type="submit" value="Add Page" class="wymupdate" />
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