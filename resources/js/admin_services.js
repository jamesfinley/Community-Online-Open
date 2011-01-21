var video_base_url = 'rtmp://s24bi3mxewgpap.cloudfront.net/cfx/st/';

function get_files (type) {
	type = type || 'all';
	
	if (type === 'all') {
		return files;
	}
	
	var a = [];
	for (var i=0, len=files.length; i<len; i++) {
		if (files[i].type === type) {
			a.push(files[i]);
		}
	}
	return a;
}

function rel_to_obj (t) {
	t = t.split(' ');
	var match, obj = {};
	for (var i=0, len=t.length; i<len; i++) {
		match = t[i].match(/([a-z]*)\(([^\)]*)\)/);
		if (match) {
			obj[match[1]] = match[2];
		}
	}
	return obj;
}

function seconds_to_stamp (seconds) {
	seconds     = Math.round(seconds);
	
	var hours   = 0,
		minutes = Math.floor(seconds / 60),
		seconds = seconds - (minutes * 60);
		
	if (minutes >= 60) {
		hours   = Math.floor(minutes / 60),
		minutes = minutes - (hours * 60);
	}
	
	return (hours ? (hours < 10 ? '0' : '') + hours + ':' : '') + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
}

function stamp_to_seconds (stamp) {
	stamp = stamp.split(':');
	if (stamp.length === 2) {
		stamp[0] = stamp[0].indexOf('0') === 0 ? stamp[0][1] : stamp[0];
		var minutes = parseInt(stamp[0]),
			seconds = (minutes * 60) + parseInt(stamp[1]);
		
		return seconds;
	}
	return false;
}

var video_schedule = {
	_schedule: [],
	init: function () {
		this.show_hide_add_video();
		
		//these methods change the page title based on the "week title" and "series name" fields
		$('input[name=big_idea]').keyup(function () {
			var week_title   = ($('input[name=big_idea]').hasClass('placeholder') ? false : $('input[name=big_idea]').val()) || null,
				series_title = ($('input[name=series_title]').hasClass('placeholder') ? false : $('input[name=series_title]').val()) || null;
			
			if (week_title) {
				$('title').html('Community Online &raquo; Add Service &raquo; ' + week_title + (series_title ? ' (' + series_title + ')' : ''));
			}
		});
		$('input[name=series_title]').keyup(function () {
			var week_title   = ($('input[name=big_idea]').hasClass('placeholder') ? false : $('input[name=big_idea]').val()) || null,
				series_title = ($('input[name=series_title]').hasClass('placeholder') ? false : $('input[name=series_title]').val()) || null;
			
			if (week_title) {
				$('title').html('Community Online &raquo; Add Service &raquo; ' + week_title + (series_title ? ' (' + series_title + ')' : ''));
			}
		});
		
		//set-up data on videos
		$('#schedule ol>li ul li').each(function () {
			if (!$(this).hasClass('add_video')) {
				var id = $('input[type=hidden]', this).val();
				for (var i=0, len=files.length; i<len; i++) {
					if (files[i].id == id) {
						$(this).data(files[i]);
					}
				}
			}
		});
		
		//set-up events for video list buttons
		$('#schedule ol>li ul li.add_video a').live('click', function () {
			video_schedule.show_add_video_modal({'group': $(this).parent().parent().parent(), 'type': rel_to_obj($(this).parent().parent().parent().attr('rel')).type});
			return false;
		});
		$('#schedule ol>li ul li .preview_video').live('click', function () {
			video_schedule.preview_video($(this).parent().data('location'), $(this).parent().data('title'), $(this).parent().data('start'));
			return false;
		});
		$('#schedule ol>li ul li .remove').live('click', function () {
			if (confirm('Are you sure you want to remove this video?')) {
				$(this).parent('li').remove();
				video_schedule.show_hide_add_video();
				video_schedule.update_times();
			}
			return false;
		});
		
		//update the video times
		this.update_times();
	},
	during: function (s) {
		for (var i=0, len=this._schedule.length; i<len; i++) {
			if (this._schedule[i].start <= s && this._schedule[i].end >= s) {
				return this._schedule[i];
			}
		}
		return false;
	},
	show_hide_add_video: function () {
		$('fieldset ol>li').each(function () {
			if ($(this).find('ul li').size() > 1 && $(this).find('ul li').size() > parseInt(rel_to_obj($(this).attr('rel')).max)) {
				$(this).find('ul li.add_video').hide();
			}
			else {
				$(this).find('ul li.add_video').show();
			}
		});
	},
	seconds_to_stamp: function (seconds) {
		return seconds_to_stamp(seconds);
	},
	update_times: function () {
		var total = 0;
		video_schedule._schedule = [];
		$('#schedule ol>li ul li').each(function () {
			var time  = $(this).find('.time'),
				start = video_schedule.seconds_to_stamp(total),
				end   = video_schedule.seconds_to_stamp(total + parseInt(time.attr('rel')));
			
			if (time.size()) {
				if ($(this).data() === null) {
					var obj = {'start': total, 'end': total + parseInt(time.attr('rel')), 'title': $(this).find('.title').text(), 'length': parseInt($(this).find('.time').attr('rel'))};
					$(this).data(obj);
				}
				else {
					$(this).data('start', total).data('end', total + parseInt(time.attr('rel')));
				}
				video_schedule._schedule.push({'start': total, 'end': total + parseInt(time.attr('rel')), 'data': $(this).data()});
				total    += parseInt(time.attr('rel'));
				time.html(start + ' - ' + end);
			}
		});
		this._total_time = total;
		
		if ($('form input[name=end_at]').size() === 0) {
			$('form').append('<input type="hidden" name="end_at" />');
		}
		$('form input[name=end_at]').val(this._total_time);
	},
	show_add_video_modal: function (s) {
		if ($('.modal').size()) { return false; }
		
		s = s || {};
		
		//get settings
		var type  = s.type || 'all';
		var group = s.group || $('#schedule ol>li').eq(0);
		
		//create modal
		var modal = $('<div />', {id: 'add_video_modal', className: 'modal', css: {'opacity': 0}});
		
		$('<h2 />', {text: 'Add Video'}).appendTo(modal);
		$('<a />', {href: '#', text: 'close', className: 'close', click: function () {
			modal.animate({
				top: 75,
				opacity: 0
			}, 300, function () {
				modal.remove();
			});
			return false;
		}}).appendTo(modal);
		$('<input />', {type: 'search', placeholder: 'Search Videos...', keyup: function () {
			var q = $(this).val();
			fileList.find('li').each(function () {
				if (q) {
					if ($(this).data('title').toLowerCase().indexOf(q.toLowerCase()) === 0) {
						$(this).show();
					}
					else {
						$(this).hide();
					}
				}
				else {
					$(this).show();
				}
			})
		}}).appendTo(modal);
		var fileList = $('<ul />', {className: 'files'});
		var files    = get_files(type);
		for (var i=0, len=files.length; i<len; i++) {
			$('<li rel="' + files[i].id + '"><a href="#" class="add_video">add</a> <span>' + files[i].title + ' (' + this.seconds_to_stamp(files[i].length) + ')</span> <a href="#" class="preview_video">preview</a><br /></li>').data({
				'id': files[i].id,
				'title': files[i].title,
				'length': files[i].length,
				'type': files[i].type,
				'location': files[i].location
			}).appendTo(fileList);
		}
		
		fileList.appendTo(modal);
		fileList.find('.add_video').bind('click', function () {
			video_schedule.add_video(group, $(this).parent('li').data());
			$(this).parents('.modal').find('.close').click();
			return false;
		});
		fileList.find('.preview_video').bind('click', function () {
			video_schedule.preview_video($(this).parent().data('location'), $(this).parent().data('title'));
			return false;
		});
		
		//append and animate in
		modal.appendTo('body').css('left', ($(window).width() - modal.width()) / 2).animate({
			top: 100,
			opacity: 1
		}, 300).find('input').focus();
	},
	add_video: function (group, data) {
		var group_name = group.find('h3').text().replace(/none/, '0').replace(/ \(select [0-9][-]?[0-9]? video[s]?\)/, '').toLowerCase().replace(/ /g, '_');
		$('<li><span class="time ' + data.type + '" rel="' + data.length + '"></span> <span class="title">' + data.title + '</span> <a href="#" class="preview_video">preview</a> <a href="#" class="remove">remove</a><input type="hidden" name="video['+group_name+'_'+(new Date()).getTime()+']" value="' + data.id + '" /><br /></li>').data(data).insertBefore(group.find('ul li').eq(-1));
		this.show_hide_add_video();
		this.update_times();
		dynamic_content.update();
	},
	preview_video: function (video_location, title, service_time) {
		if ($('#add_dynamic_modal').size()) { return false; }
		
		service_time = service_time === 0 || service_time ? service_time : null;
		
		//create modal
		var modal = $('<div />', {id: 'preview_video_modal', className: 'modal', css: {'opacity': 0}});
		
		$('<a />', {href: '#', text: 'close', className: 'close', click: function () {
			modal.animate({
				top: 75,
				opacity: 0
			}, 300, function () {
				modal.remove();
			});
			return false;
		}}).appendTo(modal);
		
		//append and animate in
		modal.appendTo('body').css('left', ($(window).width() - modal.width()) / 2).animate({
			top: 90,
			opacity: 1
		}, 300);
		
		modal.flash({width: 480, height: 270, swf: '/mini_player.swf', flashvars: service_time !== null ? {video_path: video_base_url + video_location, 'title': title, 'service_time': service_time} : {video_path: video_base_url + video_location, 'title': title}});
	}
};

var dynamic_content = {
	init: function () {
		$('#dynamic_content a.add_dc').bind('click', function () {
			dynamic_content.show_add();
			return false;
		});
		$('#dynamic_content a.edit').live('click', function () {
			
			return false;
		});
		$('#dynamic_content a.remove').live('click', function () {
			if (confirm('Are you sure you want to remove this content?')) {
				$(this).parent().parent().parent().remove();
			}
			return false;
		});
		this.update();
	},
	update: function () {
		var contents = $('#dynamic_content ol>li');
		contents.each(function () {
			var data   = rel_to_obj($(this).find('span.time').attr('rel')),
				during = video_schedule.during(parseInt(data.start)),
				during_title = (during.data && during.data.title) ? during.data.title : '',
				during_type  = (during.data && during.data.type) ? during.data.type : '';
				
			$(this).find('span strong').text('During "' + during_title + '"');
			$(this).find('.time').attr('class', 'time ' + during_type);
		});
	},
	add_content: function (s, intelliupdate) {
		//get settings
		s = s || {};
		intelliupdate = intelliupdate || false;
		
		var start_time   = s.start || 0,
			end_time     = s.end   || start_time + 30,
			content_type = (s.type && s.type in ['Flash', 'image', 'verse', 'HTML']) ? s.type : '',
			content      = s.content || '',
			serialized   = s.serialized || serialize({}),
			during       = video_schedule.during(start_time),
			during_title = (during.data && during.data.title) ? during.data.title : '',
			during_type  = (during.data && during.data.type) ? during.data.type : '';
			
		var dom = $('<li><ul><li><span rel="start(' + start_time + ') end(' + end_time + ')" class="time ' + (during_type) + '">' + (video_schedule.seconds_to_stamp(start_time) + ' - ' + video_schedule.seconds_to_stamp(end_time)) + '</span> <span><strong>During "' + (during_title) + '"</strong></span> <a href="#" class="edit">edit</a> <a href="#" class="remove">remove</a><input type="hidden" name="content[]" value="'+serialized.replace(/\"/g, '\'')+'" /><br /></li></ul></li>');
		
		var contents = $('#dynamic_content ol>li')
			content  = null,
			i        = contents.size(),
			data     = {};
		while (i--) {
			data = rel_to_obj(contents.eq(i).find('span.time').attr('rel'));
			if (parseInt(data.start) > end_time) {
				content = contents.eq(i);
				if (intelliupdate) {
					data = rel_to_obj(contents.eq(-1).find('span.time').attr('rel'));
					contents.eq(i - 1).find('span.time').attr('rel', 'start('+data.start+') end('+start_time+')').text(seconds_to_stamp(data.start) + ' - ' + seconds_to_stamp(start_time));
				}
				i = 0;
			}
		}
		
		if (content) {
			dom.insertBefore(content);
		}
		else {
			if (intelliupdate && contents.size()) {
				data = rel_to_obj(contents.eq(-1).find('span.time').attr('rel'));
				contents.eq(-1).find('span.time').attr('rel', 'start('+data.start+') end('+start_time+')').text(seconds_to_stamp(data.start) + ' - ' + seconds_to_stamp(start_time))
			}
			dom.appendTo('#dynamic_content ol');
		}
	},
	show_add: function () {
		if ($('.modal').size()) { return false; }
		
		//get settings
		var start = $('#dynamic_content ol>li').size() ? parseInt(rel_to_obj($('#dynamic_content ol>li').eq(-1).find('.time').attr('rel')).end) : 0,
			end   = start + 30;
		
		//create modal
		var modal = $('<div />', {id: 'add_dynamic_modal', className: 'modal', css: {'opacity': 0}});
		
		$('<h2 />', {text: 'Add Content'}).appendTo(modal);
		$('<a />', {href: '#', text: 'close', className: 'close', click: function () {
			modal.animate({
				top: 75,
				opacity: 0
			}, 300, function () {
				modal.remove();
			});
			return false;
		}}).appendTo(modal);
		
		//add form
		var form = $('<form />', {method: 'post', submit: function () {
			var start_time = stamp_to_seconds(form.find('#start_time_field').val()),
				end_time   = stamp_to_seconds(form.find('#end_time_field').val()),
				type       = form.find('#content_type_field').val(),
				content    = form.find('#content_field').val(),
				serialized = serialize({'start': start_time, 'end': end_time, 'type': type, 'content': content});
			
			dynamic_content.add_content({'start': start_time, 'end': end_time, 'type': type, 'content': content, 'serialized': serialized});
			
			$(this).parent().find('.close').click();
			
			return false;
		}});
		form.append('<label for="start_time_field">Time:</label>');
		form.append('<input type="text" id="start_time_field" placeholder="Start Time" value="' + (video_schedule.seconds_to_stamp(start)) + '" /> to ');
		form.append('<input type="text" id="end_time_field" placeholder="End Time" value="' + (video_schedule.seconds_to_stamp(end)) + '" />');
		form.append('<label for="content_type_field">Type:</label>');
		form.append('<select id="content_type_field"><!--<option value="Flash">Flash</option>//--><option value="image">image</option><option value="verse">verse</option><!--<option value="HTML">HTML</option>//--></select>');
		form.append('<label for="content_field">Content:</label>');
		form.append('<input type="text" id="content_field" placeholder="Content" />');
		form.append('<span class="save_form"><input type="submit" value="Add Content" /> or <a href="#" class="cancel">Cancel</a></span>');
		form.find('#start_time_field').keyup(function () {
			var seconds = stamp_to_seconds($(this).val());
			if (stamp_to_seconds(form.find('#end_time_field').val()) < seconds) {
				form.find('#end_time_field').val(seconds_to_stamp(seconds + 30));
			}
		});
		form.find('#end_time_field').keyup(function () {
			var seconds = stamp_to_seconds($(this).val());
			if (stamp_to_seconds(form.find('#start_time_field').val()) > seconds) {
				form.find('#start_time_field').val(seconds_to_stamp(seconds - 30));
			}
		});
		form.find('.cancel').click(function () {
			$(this).parent().parent().parent().find('.close').click();
			return false;
		});
		form.appendTo(modal);
		
		//append and animate in
		modal.appendTo('body').css('left', ($(window).width() - modal.width()) / 2).animate({
			top: 100,
			opacity: 1
		}, 300);
	}
};

$(function () {
	//style up some ampersands
	$('#container form').html(function (i, html) {
		return html.replace(/&amp;/, '<span class="amp">&amp;</span>');
	});
	
	if ($('input[name=big_idea]').size()) {
		//init placeholders for non-webkit browsers
		$('input[type=text]').placeholder();
		
		//init video schedule
		video_schedule.init();
		
		//init dynamic content
		dynamic_content.init();
		
		$(document).keyup(function (e) {
			if ($('.modal').size() && e.keyCode === 27) {
				if ($('.modal#preview_video_modal').size()) {
					$('.modal#preview_video_modal .close').click();
				}
				else {
					$('.modal .close').click();
				}
			}
		});
	}
	if ($('#service_list').size()) {
		$('#service_list tbody tr').each(function () {
			var link = $('td.big_idea a', this).attr('href');
			$('td.big_idea', this).html($('td.big_idea', this).text());
			$(this).click(function () {
				location.href = link;
			});
		});
	}
	if ($('#schedule_list').size()) {
		$('#schedule_list tbody tr').each(function () {
			var link = $('td.day_of_week a', this).attr('href');
			$('td.day_of_week', this).html($('td.day_of_week', this).text());
			$(this).click(function () {
				location.href = link;
			});
		});
	}
});