			<br />
		</div>
		<br />
		<?php if($account): ?>
		<div id="footer">
			<ul class="navigation">
				<!--<li id="footer_button_online_users"><a href="#">online users</a></li>//-->
				<li id="footer_button_bible"><a href="#">bible</a></li>
				<li id="footer_button_notes"><a href="<?=site_url('notes')?>">my notes</a></li>
				<li id="footer_button_settings"><a href="<?=site_url('account/settings')?>">settings</a></li>
				<li id="footer_button_logout"><a href="<?=site_url('account/logout')?>">logout</a></li>
			</ul>
		</div>
		<?php else: ?>
		<div id="footer">
			<ul class="navigation">
				<!--<li id="footer_button_bible"><a href="#">bible</a></li>-->
				<li id="footer_button_logout"><a href="<?=site_url('login?redirect='.uri_string())?>">login</a></li>
				<li id="footer_button_logout"><a href="<?=site_url('register')?>">register</a></li>
			</ul>
		</div>
		<?php endif; ?>
	</body>
</html>