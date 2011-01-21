function rad(deg) {
	return deg*Math.PI/180;
}
function distance(lat1, lon1, lat2, lon2, units) {
	lat1 = rad(lat1); lon1 = rad(lon1);
	lat2 = rad(lat2); lon2 = rad(lon2);
	r = 0;
	switch (units){
		case "miles": r = 3963.1; break;
		case "nmiles": r = 3443.9; break;
		case "kilo": r = 6378; break;
	}
	return Math.acos(Math.sin(lat1)*Math.sin(lat2) + Math.cos(lat1)*Math.cos(lat2)*Math.cos(lon2-lon1)) * r;
}

var connect = {
	location: {
		p: null,
		_processing: false,
		_callback: null,
		_fail_callback: null,
		is_processing: function (b) {
			this._processing = b;
			if (!b) {
				this._fail_callback();
			}
			//window.console.log(b ? 'loading groups' : 'done loading');
		},
		find_closest_groups: function (type, miles, limit, callback, fail_callback) {
			this._callback = callback;
			this._fail_callback = fail_callback;
			
			if (navigator.geolocation) {
				connect.location.is_processing(true);
				if (connect.location.p) {
					connect.location.process_location(connect.location.p, type, miles, limit);
				}
				else {
					navigator.geolocation.getCurrentPosition(function (p) {
						connect.location.p = p;
						connect.location.process_location(p, type, miles, limit);
					}, function () {
						connect.location.is_processing(false);
					});
				}
				return true;
			}
			return false;
		},
		process_location: function (position, type, miles, limit) {
			var latitude  = position.coords.latitude,
				longitude = position.coords.longitude;
			
			$.ajax({
				'url': '/connect/find/geolocation/'+type,
				'data': {
					'latitude': latitude,
					'longitude': longitude,
					'miles': miles,
					'limit': limit
				},
				'type': 'POST',
				'dataType': 'json',
				'success': function (data) {
					connect.location.process_result(data, latitude, longitude);
				},
				'error': function () {
					connect.location.is_processing(false);
				}
			});
		},
		process_result: function (data, latitude, longitude) {
			var groups = [];
			for (var i=0, len=data.length; i<len; i++) {
				var url = '/connect/'+(data[i].type == 'master' ? 'church' : (data[i].type == 'small group' ? 'small_group' : data[i].type))+'/'+data[i].slug;
				groups.push({'url': url, 'name': data[i].name, 'slug': data[i].slug, 'type': data[i].type, 'address': data[i].address+', '+data[i].city+', '+data[i].state+' '+data[i].zip_code, 'distance': Math.round(distance(latitude, longitude, data[i].latitude, data[i].longitude, 'miles') * 10) / 10});
			}
			connect.location.is_processing(false);
			this._callback(groups);
		}
	},
	display_images: function () {
		var url = (typeof thisPage != 'undefined' ? thisPage : pageURL) + '/images';
		$.ajax({
			'url': url,
			dataType: 'json',
			success: function (data) {
				if (data !== false && data.length) {
					if ($('#group_info_and_photos').size()) {
						var container = $('#group_info_and_photos');
						//container.height(0);
						var div = container.find('.container');
					}
					else {
						var container = $('<div />', {
							id: 'group_photos'/*,
							height: 0*/
						})
						var div = $('<div />', {
							className: 'container'
						});
						div.append('<div id="groups_photo_banner_gradient"></div>').appendTo(container);
						container.insertAfter('#header');
					}
					for (var i=0, len=data.length; i<len; i++) {
						div.append($('<img />', {
							src: data[i]
						}));
					}
					
					var current = 0;
					var images  = $('img', div).filter(function () {
						return $(this).attr('id') !== 'groups_photo_banner_image';
					});
					images.hide();
					images.eq(current).show();
					current++;
					setInterval(function () {
						images.eq(current - 1).fadeOut(1000);
						if (images.eq(current).size() === 0) {
							current = 0;
						}
						images.eq(current).fadeIn(1000);
						current++;
					}, 10000);
					
					/*container.animate({
						height: 200
					}, 350);*/
				}
			}
		});
	}
};