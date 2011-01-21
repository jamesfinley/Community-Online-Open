<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title></title>
		<script type="text/javascript">
			function initFB() {
				FB_RequireFeatures(["XFBML"], function(){
					FB.init('<?=$this->config->item('facebook_connect_api_key')?>', '<?=base_url()?>xd_receiver.htm');
				});
			}
		</script>
	</head>
	<body onload="initFB();">