		<?php if($this->facebook_connect->has_account()): ?>
		<div id="footer">
			<ul>
				<li id="footer_button_bible"><a href="#">bible</a></li>
				<li id="footer_button_online_users"><a href="#">online users</a></li>
				<li id="footer_button_logout"><a href="javascript:FB.Connect.logoutAndRedirect('')">logout</a></li>
			</ul>
		</div>
		<?php else: ?>
		<div id="footer">
			<ul>
				<li id="footer_button_bible"><a href="#">bible</a></li>
				<li id="footer_button_logout"><a href="<?=site_url('login')?>">login</a></li>
			</ul>
		</div>
		<?php endif; ?>
	</body>
	<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US" type="text/javascript"></script><script type="text/javascript">FB.init('<?=$this->config->item('facebook_connect_api_key')?>');</script>
</html>