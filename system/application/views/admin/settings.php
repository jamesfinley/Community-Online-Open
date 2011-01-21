<form method="post">
	<h2>Settings</h2>
	<?php if ($this->session->flashdata('message')): ?><div id="system_message"><?=$this->session->flashdata('message')?></div><?php endif; ?>
	
	<label for="twitter_query">Twitter Query</label>
	<input type="text" name="twitter_search" placeholder="Twitter Query" class="primary" id="twitter_query" value="<?=$twitter_query?>" /><br />

	<fieldset id="save_form">
		<input type="submit" value="Save Settings" /> or <a href="<?=site_url('admin/settings')?>">cancel</a>
	</fieldset>
</form>