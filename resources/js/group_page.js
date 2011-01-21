if (!groups) var groups = new Array();

var group_page = {
	init: function () {
	
		$('.stream_actions .delete').click(function () {
			var c = confirm('Are you sure you want to delete this item?');
			return c;
		});
		
		//
		if ($('#group_page').attr('data-description') || ($('#group_page').attr('rel') != ',' && $('#group_page').attr('rel') != '0,0')) {
			$('#group_more_info').click(function () {
				var m = new ccc.utils.Modal();
				if ($('#group_page').attr('data-description')) {
					m.append('<div class="description">'+$('#group_page').attr('data-description')+'</div>');
				}
				if ($('#group_page').attr('rel') != ',' && $('#group_page').attr('rel') != '0,0') {
					m.append('<div class="map"></div>');
				}
				m.attach(true);
				if ($('#group_page').attr('rel') != ',') {
					$('.model .map').append('<img src="http://maps.google.com/maps/api/staticmap?sensor=false&size=480x290&center='+$('#group_page').attr('rel')+'&markers='+$('#group_page').attr('rel')+'" />');
				}
				return false;
			});
		}
		else {
			$('#group_more_info').remove();
		}
		
		//setup filters
		$('#filter_stream li a').click(function () {
			$(this).parent().parent().find('li').removeClass('selected');
			$(this).parent().addClass('selected');
			switch ($(this).parent().attr('id')) {
				case 'filter_all_items':
					group_page.filter_by('type', 'all');
					break;
				case 'filter_news':
					group_page.filter_by('type', 'news');
					break;
				case 'filter_events':
					group_page.filter_by('type', 'event');
					break;
				case 'filter_discussion':
					group_page.filter_by('type', 'discussion');
					break;
				case 'filter_prayers':
					group_page.filter_by('type', 'prayer');
					break;
				case 'filter_qandas':
					group_page.filter_by('type', 'qna');
					break;
			}
			return false;
		});
		
		$('#group_page .reply_box textarea').live('focus', function () {
			$(this).height(100);
			$(this).parent().find('input[type=submit]').show();
		}).live('blur', function () {
			var textarea = $(this);
			setTimeout(function () {
				textarea.height(20);
				textarea.parent().find('input[type=submit]').hide();
			}, 5000);
		});
		
		group_page.initStreamItems();
	},
	initStreamItems: function () {
		$('#group_page .reply_box input[type=submit]').hide();
		
		if ($('#post_to_stream').size()) {
			$('#post_to_stream').before('<div id="post_to_stream_show"><span class="info">You have access to post to this stream.</span><input type="submit" value="Make a Post" /><br /></div>');
			$('#post_to_stream_show input').click(function () {
				$('#post_to_stream').fadeIn(350);
				$('#post_to_stream_show').hide();
				return false;
			});
			$('#post_to_stream').hide();
		}

		//hide post form and display button
		if ($('#post_to_stream').size()) {
			//$('#post_to_stream').hide();
			
			//$('<div id="post_to_stream_button_box"><a href="#" id="post_to_stream_button">'+($('#post_to_stream h2').html())+'</a></div>').insertBefore('#post_to_stream');
			
			/*$('#post_to_stream_button').click(function () {
				if ($('#post_to_stream').css('display') === 'none') {
					$(this).html('Cancel Posting');
					$('#post_to_stream').show();
				}
				else {
					$(this).html($('#post_to_stream h2').html());
					$('#post_to_stream').hide();
				}
				
				return false;
			});*/
			
			/*$('#post_to_stream select').change(function () {
				if ($(this).val() !== 'prayer') {
					$('#post_to_stream input[name=subject]').show();
				}
				else {
					$('#post_to_stream input[name=subject]').hide();
				}
			});
			if ($('#post_to_stream select').val() !== 'prayer') {
				$('#post_to_stream input[name=subject]').show();
			}
			else {
				$('#post_to_stream input[name=subject]').hide();
			}*/
			$('#post_to_stream select').change(function () {
				var value 		= $('#post_to_stream select').val();
				var verb		= $('#post_to_stream select option:selected').attr('data-verb');
				var preposition = $('#post_to_stream select option:selected').attr('data-preposition');
				
				$('#post_to_stream h2 .verb').text(verb);
				$('#post_to_stream h2 .preposition').text(preposition);
				
				if ($(this).val() !== 'prayer') {
					$('#subject_set').show();
				}
				else {
					$('#subject_set').hide();
				}
				if ($('#post_to_stream select').val() === 'event') {
					var d  = new Date();
					var m  = d.getMonth() + 1;
						m  = m < 10 ? '0'+m : m;
					var da = d.getDate();
						da = da < 10 ? '0'+da : da;
					var y  = d.getFullYear();
					$('#subject_set').after('<div id="event_date_set"><label for="event_date_field">Event Date</label><input type="text" name="event_date" id="event_date_field" value="'+m+'/'+da+'/'+y+'" /></div>');
					$('#event_date_field').date_selector();
					var closeOnBodyClick = null;
					closeOnBodyClick = function () {
						$('.jf_date_selector').remove();
						$('body').unbind('click', closeOnBodyClick);
					};
					$('#event_date_field').bind('click', function () {
						return false;
					});
					$('#event_date_field').bind('focus', function () {
						$('body')/*.unbind('click', closeOnBodyClick)*/.bind('click', closeOnBodyClick);
						return false;
					});
				}
				else {
					$('#event_date_set').remove();
				}
				if ($('#post_to_stream select').val() == 'news' || $('#post_to_stream select').val() == 'event') {
					$('#file_set').show();
				}
				else {
					$('#file_set').hide();
				}
			});
			
			var value 		= $('#post_to_stream select').val();
			var verb		= $('#post_to_stream select option:selected').attr('data-verb');
			var preposition = $('#post_to_stream select option:selected').attr('data-preposition');
			
			$('#post_to_stream h2 .verb').text(verb);
			$('#post_to_stream h2 .preposition').text(preposition);
			
			if ($('#post_to_stream select').val() !== 'prayer') {
				$('#subject_set').show();
			}
			else {
				$('#subject_set').hide();
			}
			if ($('#post_to_stream select').val() == 'news' || $('#post_to_stream select').val() == 'event') {
				$('#file_set').show();
			}
			else {
				$('#file_set').hide();
			}
			if ($('#post_to_stream select').val() === 'event') {
				$('#subject_set').after('<div id="event_date_set"><label for="event_date_field">Event Date</label><input type="text" name="event_date" id="event_date_field" /></div>');
			}
			else {
				$('#event_date_set').remove();
			}
		}
		
		$('.share_post').click(function () {
			
			var stream_item = $(this).parents('.stream_item');
			var post_id     = parseInt(stream_item.attr('data-id'));
			var post_title  = stream_item.find('.post_title').text();
			var post_url    = stream_item.attr('data-url');
			var post_bitly  = stream_item.attr('data-bitly');
			var post_type   = stream_item.attr('data-type');
			var post_user   = stream_item.find('.footer .user').text();
			
			var tweetURL    = 'http://twitter.com/share?url='+escape(post_bitly)+'&via=getcommunity&text='+escape('Check out this post on Community Online: '+(post_type == 'prayer' ? 'Prayer from '+post_user : '"'+post_title+'"'));
			var facebookURL = 'http://www.facebook.com/sharer.php?u='+escape(post_bitly)+'&t='+escape(post_type == 'prayer' ? 'Prayer from '+post_user : '"'+post_title+'"');
			
			if ($('.share_post_menu').size()) {
				var ret = true;
				if ($('.share_post_menu').attr('data-id') == post_id) {
					ret = false;
				}
				$('.share_post_menu').remove();
				if (ret === false) {
					return false;
				}
			}
			
			/*
			var ul = $('<ul class="share_post_menu" data-id="'+post_id+'"></ul>');
			for (var i=0, len=groups.length; i<len; i++) {
				if (groups[i].open === false) {
					if (((post_type == 'news' || post_type == 'event') && groups[i].is_facilitator) || (post_type != 'news' && post_type != 'event')) {
						ul.append('<li><a href="#" data-id="'+groups[i].id+'">'+groups[i].name+'</a></li>');
					}
				}
			}*/
			var has_groups = false;
			for (var i=0, len=groups.length; i<len; i++) {
				if (groups[i].open === false) {
					if (((post_type == 'news' || post_type == 'event') && groups[i].is_facilitator) || (post_type != 'news' && post_type != 'event')) {
						has_groups = true;
					}
				}
			}
			var ul = $('<ul class="share_post_menu" data-id="'+post_id+'"></ul>');
			if (has_groups) ul.append('<li class="share"><a href="#" data-type="share">Share to Group!</a></li>');
			ul.append('<li class="tweet"><a href="'+tweetURL+'" data-type="tweet">Tweet This!</a></li>');
			ul.append('<li class="facebook"><a href="'+facebookURL+'" data-type="facebook">Share This!</a></li>');
			
			var remove_menu = null;
			remove_menu = function () {
				ul.remove();
				$('body').unbind('click', remove_menu);
			};
			$('body').bind('click', remove_menu);
			
			ul.find('li a').click(function () {
				var type = $(this).attr('data-type');
				if (type == 'share') {
					ul.find('li').remove();
					ul.find('li a').unbind('click');
					
					for (var i=0, len=groups.length; i<len; i++) {
						if (groups[i].open === false) {
							if (((post_type == 'news' || post_type == 'event') && groups[i].is_facilitator) || (post_type != 'news' && post_type != 'event')) {
								ul.append('<li class="'+groups[i].type+'"><a href="#" data-id="'+groups[i].id+'">'+groups[i].name+'</a></li>');
							}
						}
					}
					
					ul.find('li a').click(function () {
						var group_id = parseInt($(this).attr('data-id'));
						$.ajax({
							'url': '/api/connect/share_post/'+post_id+'/'+group_id,
							'dataType': 'json',
							'success': function (data) {
								
							}
						});
						
						remove_menu();
						return false;
					});
					return false;
				}
				else if (type == 'tweet') {
					var newWindow = window.open($(this).attr('href'), 'tweet_this', 'width=500, height=400');
					remove_menu();
					return false;
				}
				else if (type == 'facebook') {
					var newWindow = window.open($(this).attr('href'), 'facebook_this', 'width=500, height=400');
					remove_menu();
					return false;
				}
			});
			
			/*ul.find('li a').click(function () {
				var group_id = parseInt($(this).attr('data-id'));
				$.ajax({
					'url': '/api/connect/share_post/'+post_id+'/'+group_id,
					'dataType': 'json',
					'success': function (data) {
						
					}
				});
				
				remove_menu();
				return false;
			});*/
			
			if (ul.find('li').size() > 0) {
				stream_item.find('.post_actions').eq(0).append(ul);
			}
			
			return false;
		});
	
		/*$('#stream .stream_item').each(function () {
			var link_to, responses = $(this).find('.responses');
			
			if (responses.size()) {
				var show_responses = function () {
					link_to.html('hide responses');
					responses.show();
					link_to.unbind('.my_action').bind('click.my_action', hide_responses);
					return false;
				},
				hide_responses = function () {
					link_to.html('show responses');
					responses.hide();
					link_to.unbind('.my_action').bind('click.my_action', show_responses);
					return false;
				};
				link_to = $('<a href="#" class="show_responses">show responses</a>');
				link_to.bind('click.my_action', show_responses);
				link_to.prependTo($(this).find('.footer').eq(0));
				
				responses.hide();
			}
		});*/
	},
	filter_by: function (field, value) {
		if (field == 'type') {

			var url = location.href.split("#")[0] + '/stream/'+value+'/1';
			$.ajax({
				'url': url,
				dataType: 'html',
				success: function (data) {
					$('#stream').replaceWith(data);
					group_page.initStreamItems();
					
					value = (value == 'all') ? 'discussion' : value;
					value = (value == 'qandas') ? 'qna' : value;
					value = (value == 'prayers') ? 'prayer' : value;
					value = (value == 'events') ? 'event' : value;
					
					//$('#post_to_stream select option[value='+value+']').attr('selected', 'selected');
				}
			});
		}
	}
}
$(function () {
	group_page.init();
});