var h = {
	collapse: false,
	init: function() {
		$('#show_adults, #show_kids, #show_students').click(function() {
			$('#show_adults, #show_kids, #show_students').removeClass('selected');
			$(this).addClass('selected');
			
			var category = $(this).attr('id').substring(5);

			var top = $(".big_idea."+category, $('#scroller')).position().top;
			
			$(".big_idea", $('#scroller')).removeClass('selected');
			$(".big_idea."+category, $('#scroller')).addClass('selected');
			
			if ($(this).hasClass('no_animate')) {
				$("#ideas", $('#scroller')).css({
					top: -top+'px',
				});
			}
			else {
	       		$("#ideas", $('#scroller')).animate({
	       			top: -top+'px',
	       		}, 600, 'easeInQuart');
       		}
       		
       		ccc.utils.storage('big_idea_category', category);
       		
       		$(this).removeClass('no_animate');
       		
       		return false;		
		});
		
		$('#collapse_big_ideas, a.collapse_big_ideas').click(function(element) {
			$('#scroller, #big_idea_toggler, #collapse_big_ideas').toggleClass('collapse');
			ccc.utils.storage('big_idea_collapsed', $('#scroller').hasClass('collapse') ? 'true' : 'false');
		});	
		
		if (ccc.utils.storage('big_idea_category')) {
			$('#show_'+ccc.utils.storage('big_idea_category')).addClass('no_animate').click();
		}
		else {
			$(".big_idea").eq(0).addClass('selected');
		}
		if (ccc.utils.storage('big_idea_collapsed') == 'true') {
			$('#collapse_big_ideas').click();
		}
		
		$('#scroller .videos li a').click(function () {
			var videoURL = $(this).attr('href');
			var el = $('<div id="big_idea_video_player"><div id="big_idea_video_player_video"></div></div>');
			el.appendTo('#scroller');
			
			flowplayer('big_idea_video_player_video', '/resources/flash/flowplayer-3.2.5-0.swf', {
				'clip': videoURL
			});
			
			/*el.find('div').flash({
				swf: '/resources/flash/standalone.swf',
				width: 640,
				height: 360,
				flashvars: {
					video: videoURL
				}
			});*/
			el.click(function () {
				el.remove();
			});
			el.find('div').click(function () {
				return false;
			});
			return false;
		});
		
		$('.invite_and_download .invite').click(function () {
			var modal = $('<div class="model"></div>');
			var inner = $('<form id="invite_a_friend_modal" class="inner"></form>');
			inner.append('<label for="invite_a_friend_from_field">Your Email</label><br /><input type="text" id="invite_a_friend_from_field" />');
			inner.append('<label for="invite_a_friend_email_field">Recipient(s) (separate multiple values with a comma)</label><br /><input type="text" id="invite_a_friend_email_field" />');
			inner.append('<label for="invite_a_friend_message_field">Your Message</label><br /><textarea id="invite_a_friend_message_field"></textarea>');
			inner.append('<input type="submit" value="Invite my Friends" /> <input type="button" value="Cancel" />');
			inner.appendTo(modal);
			modal.appendTo('body');
			
			modal.css({
				'margin-top': 0 - (modal.height() / 2)
			});
			
			inner.find('input[type=button]').click(function () {
				modal.animate({
					top: '-=50',
					opacity: 0
				}, 350, function () {
					modal.remove();
				});
			});
			
			inner.submit(function () {
				var big_idea = $(".big_idea.selected").find('.description').clone();
				big_idea.find('.invite_and_download').remove();
				var description = big_idea.text();
				
				$('input[type=submit], input[type=button]', modal).attr('disabled', 'disabled');
				
				$.ajax({
					'url': '/api/email',
					'data': {
						'to': $('#invite_a_friend_email_field').val(),
						'from': $('#invite_a_friend_from_field').val(),
						'message': $('#invite_a_friend_message_field').val(),
						'big_idea': description
					},
					'type': 'post',
					'success': function (data) {
						inner.html(data);
						setTimeout(function () {
							modal.animate({
								top: '-=50',
								opacity: 0
							}, 350, function () {
								modal.remove();
							});
						}, 3000);
					}
				});
				
				return false;
			});
			return false;
		});
	}
};
$(function () {
	h.init();
	
	$('.invite_and_download .download').click(function () {
		var menu = $('.downloads', $(this).parent());
		var remove = null;
		remove = function () {
			menu.hide();
			$('body').unbind('click', remove);
		};
		menu.toggle();
		$('body').bind('click', remove);
		return false;
	});
});