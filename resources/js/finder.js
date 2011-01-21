$(function () {
	$('#small_group_finder_container').addClass('js');
	
	var popoverIsOpen = false;
	
	var group_data = null;
	
	function updateCounts(data) {
		var campus    = [];
		var city      = [];
		var category  = [];
		var day       = [];
		var childcare = [];
		for (var i=0, len=data.length; i<len; i++) {
			if (!campus[data[i]['campus_id']]) campus[data[i]['campus_id']] = 0;
			campus[data[i]['campus_id']]++;
			
			if (data[i]['city']) {
				if (!city[data[i]['city']]) city[data[i]['city']] = 0;
				city[data[i]['city']]++;
			}
			
			if (!category[data[i]['category']]) category[data[i]['category']] = 0;
			category[data[i]['category']]++;
			
			if (!day[data[i]['day_of_week']]) day[data[i]['day_of_week']] = 0;
			day[data[i]['day_of_week']]++;
			
			if (!childcare[data[i]['has_childcare']]) childcare[data[i]['has_childcare']] = 0;
			childcare[data[i]['has_childcare']]++;
		}
		
		for (var i in campus) {
			var text = $('#small_group_finder_field_campus option[value='+i+']').html();
			if (text) {
				text = text.replace(/\([0-9]*\)/, '')+' ('+campus[i]+')';
				$('#small_group_finder_field_campus option[value='+i+']').html(text);
			}
		}
		
		for (var i in city) {
			var text = $('#small_group_finder_field_city option[value='+i+']').html();
			if (text) {
				text = text.replace(/\([0-9]*\)/, '')+' ('+city[i]+')';
				$('#small_group_finder_field_city option[value='+i+']').html(text);
			}
		}
		
		for (var i in category) {
			var text = $('#small_group_finder_field_category option[value='+i+']').html();
			if (text) {
				text = text.replace(/\([0-9]*\)/, '')+' ('+category[i]+')';
				$('#small_group_finder_field_category option[value='+i+']').html(text);
			}
		}
	}
	
	function updateMap() {
		var myOptions = {
			zoom: 9,
			center: new google.maps.LatLng(41.6567, -88.1886),
			disableDefaultUI: true,
			scaleControl: true,
			navigationControl: true,
			navigationControlOptions: {
				style: google.maps.NavigationControlStyle.SMALL
			},
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		$("#small_group_finder_map .map").remove();
		$("#small_group_finder_map").append('<div class="map"></div>');
		var el    = $("#small_group_finder_map .map").get(0);
		var map   = new google.maps.Map(el, myOptions);
		var image = 'https://communitychristian.org/resources/images/map_sg_pin.png';
		
		for (var i=0, len=group_data.length; i<len; i++) {
			var openWindow = null;
			(function (i) {
				var longitude = group_data[i]['longitude'];
				var latitude  = group_data[i]['latitude'];
				var latlng = new google.maps.LatLng(latitude, longitude);
				var info   = new google.maps.InfoWindow({
					content: '<strong><a href="/groups/'+group_data[i].slug+'">'+group_data[i].name+'</a></strong><br />'+(group_data[i].meeting_time ? group_data[i].meeting_time+' on '+group_data[i].day_of_week+'<br />' : '')+group_data[i].description+(group_data[i].description ? '<br />' : '')+'<br />'+group_data[i].address+'<br />'+group_data[i].city+', '+group_data[i].state+' '+group_data[i].zip_code,
					maxWidth: 250
				});
				var marker = new google.maps.Marker({
					'position': latlng,
					'map': map,
					'icon': image,
					'title': group_data[i].name
				});
				group_data[i].info   = info;
				group_data[i].marker = marker;
				group_data[i].map    = map;
				google.maps.event.addListener(marker, 'click', function() {
					if (openWindow !== null) {
						openWindow.close();
					}
					
					info.open(map, marker);
					openWindow = info;
				});
			})(i);
		}
		
		/*for (var i=0, len=locations.length; i<len; i++) {
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
		}*/
	}
	
	function showList() {
		$('#small_group_finder_results_list').remove();
		
		var name, address, city, state, zip_code, slug, meeting_time, day_of_week, li;
		var ul = $('<ul id="small_group_finder_results_list"></ul>');
		ul.append('<li id="small_group_finder_results_list_close"><a href="#">back to filters</a></li>');
		ul.find('#small_group_finder_results_list_close a').click(function () {
			ul.animate({
				left: 231
			}, 350, function () {
				ul.remove();
			});
			return false;
		});
		for (var i=0, len=group_data.length; i<len; i++) {
			if (group_data[i].latitude) {
				name         = group_data[i].name;
				address      = group_data[i].address;
				city         = group_data[i].city;
				state        = group_data[i].state;
				zip_code     = group_data[i].zip_code;
				slug         = group_data[i].slug;
				meeting_time = group_data[i].meeting_time;
				day_of_week  = group_data[i].day_of_week;
				
				li = $('<li><a href="/groups/'+slug+'" class="name">'+name+'</a>'+(meeting_time && day_of_week ? '<div class="time_and_date">'+meeting_time+' on '+day_of_week+'</div>' : '')+'</li>');
				
				li.click((function (group) {
					return function () {
						for (var i=0, len=group_data.length; i<len; i++) {
							group_data[i].info.close();
						}
						group.info.open(group.map, group.marker);
					};
				})(group_data[i]));
				
				ul.append(li);
			}
		}
		ul.appendTo('#small_group_finder_filters_results');
		
		ul.animate({
			left: 0
		}, 350);
	}
	
	function updateFilters() {
		var filters = {};
		$('.small_group_finder_filter').each(function () {
			filters[$(this).find('select').attr('name')] = $(this).find('select').val();
		});
		$.ajax({
			'url': '/api/finder',
			'data': filters,
			'dataType': 'json',
			'success': function (data) {
				group_data = data;
				var count = data.length;
				$('#small_group_finder_results').html('<a href="#">list your '+count+' result(s)</a>');
				$('#small_group_finder_results').find('a').click(function () {
					showList();
					return false;
				});
				updateMap();
				//updateCounts(data);
				//window.console.log(count);
			}
		});
		//window.console.log(filters);
	}
	
	function selectOption(field_id, value, text) {
		$('#'+field_id).val(value);
		
		$('#'+field_id).parent().find('label .small_group_finder_filter_value').animate({
			opacity: 0
		}, 175, function () {
			$('#'+field_id).parent().find('label .small_group_finder_filter_value').text(text);
			$('#'+field_id).parent().find('label .small_group_finder_filter_value').animate({
				opacity: 1
			}, 175);
		});
		
		updateFilters();
	}
	
	function removePopover(popover) {
		popover.animate({
			opacity: 0
		}, 150, function () {
			popover.remove();
		});
		popoverIsOpen = false;
	}
	
	$('body').click(function () {
		if (popoverIsOpen) {
			removePopover($('#small_group_finder_popover').eq(0));
		}
	});
	
	//get all select fields and create text
	$('.small_group_finder_filter').each(function () {					
		var value = $(this).find('select').val() || $(this).find('select option:selected').text();
		var optionsEl = $(this).find('select option');
		var options = [];
		optionsEl.each(function () {
			options.push({
				value: $(this).val(),
				text: $(this).text()
			});
		});
		$(this).find('label').append($('<span></span>', {
			className: 'small_group_finder_filter_value',
			text: value
		}));
		$(this).find('label').click(function () {
			if (popoverIsOpen) {
				if ($('#small_group_finder_popover').attr('rel') === $(this).attr('for')) {
					removePopover($('#small_group_finder_popover').eq(0));
					return false;
				}
				removePopover($('#small_group_finder_popover').eq(0));
			}
			
			var optionsEl = $(this).parent().find('select option');
			var options = [];
			optionsEl.each(function () {
				options.push({
					value: $(this).val(),
					text: $(this).text()
				});
			});
			
			var selected = $(this).parent().find('select').val();
			var popover = $('<div id="small_group_finder_popover" rel="'+$(this).attr('for')+'"></div>');
			var list = $('<ul></ul>');
			popover.append(list);
			for (var i=0, len=options.length; i<len; i++) {
				list.append('<li rel="'+options[i].value+'"><a href="#"'+(selected === options[i].value ? ' class="selected"' : '')+'>'+options[i].text+'</a></li>');
			}
			popover.find('li a').click(function () {
				$(this).parent().parent().find('a').removeClass('selected');
				$(this).addClass('selected');
				var field = $(this).parent().parent().parent().attr('rel');
				var value = $(this).parent().attr('rel');
				var text  = $(this).parent().text();
				/*$('#'+field).val(value);
				$('#'+field).parent().find('label .small_group_finder_filter_value').text(text);*/
				selectOption(field, value, text);
				
				removePopover($('#small_group_finder_popover').eq(0));
				
				return false;
			});
			$('body').append(popover);
			
			popover.css({
				top: $(this).offset().top,
				left: $(this).offset().left + 230
			});
			
			if (popover.find('a.selected').offset().top > 290) {
				var newPos = popover.find('a.selected').offset().top - 255;
				popover.find('ul').scrollTop(newPos);
			}
			
			setTimeout(function () {
				popoverIsOpen = true;
			}, 100);
		});
	});
	updateFilters();
});