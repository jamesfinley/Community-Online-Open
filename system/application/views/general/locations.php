
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<div id="locations_page">
	<div id="locations_map"></div>
	<div id="sidebar">
		<h2>Where is the Church?</h2>
		<p class="info">We are a multi-site church: one church that meets in many different locations all across Chicagoland and the Western Suburbs.</p>
		<ul>
			<?php foreach($locations->result() as $location): ?>
			<li>
				<h3><a href="<?=site_url($this->groups_model->get_url($location->id))?>"><?=$location->name?></a></h3>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<script>
		var locations = new Array();
	<?php foreach($locations->result() as $location): ?>
		locations.push({'name': '<?=addslashes($location->name)?>', 'url': '<?=$this->groups_model->get_url($location->id)?>', 'description': '<?=addslashes($location->description)?>', 'times': '<?=addslashes(str_replace("\n", "<br />", str_replace("\r", '', $location->service_times)))?>', 'latitude': '<?=$location->latitude?>', 'longitude': '<?=$location->longitude?>'});
	<?php endforeach; ?>
	</script>
	<script type="text/javascript">
		function initialize() {
			var myOptions = {
				zoom: 10,
				center: new google.maps.LatLng(41.6567, -88.1886),
				disableDefaultUI: true,
				scaleControl: true,
				navigationControl: true,
				navigationControlOptions: {
					style: google.maps.NavigationControlStyle.SMALL
				},
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("locations_map"), myOptions);
			
			var image = '<?=base_url()?>resources/images/map_location_pin.png';
			
			for (var i=0, len=locations.length; i<len; i++) {
				(function (i) {
					var latlng, marker, info;
					info   = new google.maps.InfoWindow({
						content: '<strong><a href="'+locations[i].url+'">'+locations[i].name+'</a></strong><br />'+locations[i].description+'<br /><br /><strong>Service Times:</strong><br />'+locations[i].times,
						maxWidth: 250
					});
					latlng = new google.maps.LatLng(locations[i].latitude, locations[i].longitude);
					marker = new google.maps.Marker({
						'position': latlng,
						'map': map,
						'icon': image,
						'title': locations[i].name
					});
				    google.maps.event.addListener(marker, 'click', function() {
				    	info.open(map, marker);
				    });
			    })(i);
			}
		}
		$(function () {
			initialize();
		});
	</script>
	<br />
</div>