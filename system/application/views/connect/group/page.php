<div id="group_page" class="container static_page" rel="<?=$group->latitude?>,<?=$group->longitude?>">
	<div id="group_header">
		<h2><?=character_limiter($group->name, 45)?></h2>
		<a href="<?=site_url($this->groups_model->get_url($group->id))?>" id="">back to group</a>
		<ul id="filter_stream">
		</ul>
	</div>
	<div id="page">
		<h2 class="page_title"><?=$page->title?></h2>
		<?=$page->content?>
	</div>
	<?php $this->load->view('connect/group/sidebar'); ?>
	<br />
</div>

<script type="text/javascript">
	$(function () {
		connect.display_images();
	});
</script>