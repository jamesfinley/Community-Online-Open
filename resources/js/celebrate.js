var c = ccc.celebrate = {
	volume: function (newVolume) {
		if (('localStorage' in window) && window['localStorage'] !== null) {
			if (newVolume !== undefined) {
				localStorage['co.video.volume'] = newVolume;
			}
			if (localStorage['co.video.volume'] === null) {
				localStorage['co.video.volume'] = 1;
			}
			return parseFloat(localStorage['co.video.volume'] !== undefined ? localStorage['co.video.volume'] : 1) * 100;
		}
		return 100;
	},
	storage_unread: function (id, status) {
		if (status !== undefined) {
			var storage = ccc.utils.storage('co.chat.unread_replies');
			if (storage) {
				storage     = storage.split(',');
				var isupdated = false;
				for (var i=0, len=storage.length; i<len; i++) {
					storage[i] = storage[i].split('=');
					if (storage[i][0] == id) {
						storage[i][1] = status ? 'true' : 'false';
						isupdated = true;
					}
					storage[i] = storage[i].join('=');
				}
				if (isupdated === false) {
					storage.push(id + '=' + (status ? 'true' : 'false'));
				}
				storage = storage.join(',');
			}
			else {
				storage = id + '=' + (status ? 'true' : 'false');
			}
			
			ccc.utils.storage('co.chat.unread_replies', storage);
		}
		else {
			var storage = ccc.utils.storage('co.chat.unread_replies');
			if (storage) {
				storage     = storage.split(',');
				for (var i=0, len=storage.length; i<len; i++) {
					storage[i] = storage[i].split('=');
					if (storage[i][0] == id) {
						return storage[i][1] === 'true' ? true : false;
					}
				}
			}
			return true;
		}
	},
	storage_collapsed: function (id, status) {
		if (status !== undefined) {
			var storage = ccc.utils.storage('co.chat.collapsed');
			if (storage) {
				storage     = storage.split(',');
				var isupdated = false;
				for (var i=0, len=storage.length; i<len; i++) {
					storage[i] = storage[i].split('=');
					if (storage[i][0] == id) {
						storage[i][1] = status ? 'true' : 'false';
						isupdated = true;
					}
					storage[i] = storage[i].join('=');
				}
				if (isupdated === false) {
					storage.push(id + '=' + (status ? 'true' : 'false'));
				}
				storage = storage.join(',');
			}
			else {
				storage = id + '=' + (status ? 'true' : 'false');
			}
			
			ccc.utils.storage('co.chat.collapsed', storage);
		}
		else {
			var storage = ccc.utils.storage('co.chat.collapsed');
			if (storage) {
				storage     = storage.split(',');
				for (var i=0, len=storage.length; i<len; i++) {
					storage[i] = storage[i].split('=');
					if (storage[i][0] == id) {
						return storage[i][1] === 'true' ? true : false;
					}
				}
			}
			return false;
		}
	},
	init: function () {
		if (ccc.utils.storage('co.service_id') != c.service_id) {
			ccc.utils.storage('co.chat.unread_replies', '');
			ccc.utils.storage('co.chat.collapsed', '');
			ccc.utils.storage('co.service_id', c.service_id);
		}
		
		this.resizeSidebar();
		$(window).bind('resize', function () {
			ccc.celebrate.resizeSidebar();
		});
		/*$('#video_player').flash({
			swf: '/player.swf',
			width: 600,
			height: 338
		});*/
		if (ccc._user) {
			$('#twitter_tab_button').addClass('hidden');
			$('#chat_tab_button a').click(function () {
				if (!$(this).hasClass('selected')) {
					$('#chat_tab_button').removeClass('hidden');
					$('#notes_tab').hide();
					$('#twitter_tab').hide();
					$('#twitter_tab_button').addClass('hidden');
					$('#chat_tab').show();
					$('#sidebar_tabs .selected').removeClass('selected');
					$(this).addClass('selected');
					//$('#open_tab').html('<div id="chat_tab"><div id="options"></div><div id="chatroom"></div><div id="chatbar"></div></div>');
				}
	
				//c.twitter.pause();
				//c.chat.start();
	
				return false;
			});
			$('#notes_tab_button a').click(function () {
				if (!$(this).hasClass('selected')) {
					$('#chat_tab_button').addClass('hidden');
					$('#chat_tab').hide();
					$('#twitter_tab').hide();
					$('#twitter_tab_button').addClass('hidden');
					$('#sidebar_tabs .selected').removeClass('selected');
					$(this).addClass('selected');
					if ($('#notes_tab').size()) {
						$('#notes_tab').show();
					}
					else {
						$('#open_tab').append('<form id="notes_tab"><div id="notes_status"><span class="last_update"></span></div><textarea id="notes_textarea"></textarea></form>');
					}
					
					if (!c._notes) {
						$('#notes_textarea').addClass('placeholder');
						$('#notes_textarea').focus(function () {
							if ($('#notes_textarea').val() == 'Feel free to jot down some notes!') {
								$('#notes_textarea').removeClass('placeholder');
								$('#notes_textarea').val('');
							}
						}).blur(function () {
							if ($('#notes_textarea').val() == '') {
								$('#notes_textarea').addClass('placeholder');
								$('#notes_textarea').val('Feel free to jot down some notes!');
							}
						});
					}
					$('#notes_textarea').val(c._notes || 'Feel free to jot down some notes!').focus();
					$('#notes_textarea').change(function () {
						c._notes = $('#notes_textarea').val();
					});
					
					//c.twitter.pause();
					//c.chat.pause();
					
					var makeChange = false,
						changeTimeout;
					$('#notes_textarea').keyup(function () {
						if (makeChange) {
							clearTimeout(changeTimeout);
						}
						makeChange = true;
						changeTimeout = setTimeout(function () {
							c.save_notes();
							makeChange = false;
						}, 3000);
					});
				}
				return false;
			});
			$('#twitter_tab_button a').click(function () {
				if (!$(this).hasClass('selected')) {
					$('#chat_tab_button').addClass('hidden');
					$('#twitter_tab_button').removeClass('hidden');
					$('#chat_tab').hide();
					$('#notes_tab').hide();
					$('#sidebar_tabs .selected').removeClass('selected');
					$(this).addClass('selected');
	
					if ($('#twitter_tab').size()) {
						$('#twitter_tab').show();
					}
					else 
					{
						$('#open_tab').append('<div id="twitter_tab"><div id="tweet_count">No New Tweets!</div><div id="tweets"></div><div id="tweetbar"></div></div');
						c.twitter.load();
					}
	
					//c.chat.pause();
					//c.twitter.start();
				}
	
				return false;
			});
			if (c.group_id !== null) {
				this.chat.init();
			}
			else {
				this.chat.show_select_group();
			}
			$('#open_tab').append('<div id="twitter_tab"><div id="tweet_count">No New Tweets!</div><div id="tweets"></div><div id="tweetbar"></div></div');
			$('#twitter_tab').hide();
			c.twitter.init();
			c.twitter.start();
			c.twitter.load();
		}
		else {
			//user is not logged in
			$('#sidebar_tabs, #open_tab').hide();
			
			var user_steps = $('<div />', {
				id: 'user_login_steps'
			});
			
			user_steps.append('<h2>Welcome to Community Online!</h2>');
			
			var step1 = $('<div />', {
				className: 'step step1'
			});
			step1.appendTo(user_steps);
			step1.append('<p>Small groups are a great way to have conversation and meet new people. By registering, you can experience this service in a group setting and get to know them. And we\'d love for you to come back each week and celebrate with the same group.</p>');
			step1.append('<form method="post" action="/login?redirect=watch"><h3><span>Login to <strong>Community Online</strong></span></h3><label>Email Address</label><br /><input type="text" name="email" id="email_field" /><br /><label>Password</label><br /><input type="password" name="password" id="password_field" /><a href="#" id="register_ribbon"><span>Um... I don\'t have one?</span></a><input type="submit" value="Login" /></form>');
			step1.find('#register_ribbon').click(function () {
				step1.find('form').attr('action', '/register?redirect=watch').submit(function () {
					
					return false;
				});
				step1.find('#register_ribbon span').animate({
					width: 0
				}, 150, function () {
					step1.find('#register_ribbon').animate({
						width: '-=3',
						marginRight: '+=3'
					}, 50, function () {
						step1.find('h3 span').fadeOut(150, function () {
							step1.find('h3 span').html('Register for <strong>Community Online</strong>').fadeIn(150);
							step1.find('input[type=submit]').addClass('register');
						});
						step1.find('#register_ribbon').slideUp(150, function () {
							step1.find('#register_ribbon').remove();
							$('#password_field').replaceWith('<input type="text" id="password_field" />');
							$('<br /><div id="registration_fields"><label class="fifty_fifty">First Name</label><label class="fifty_fifty last">Last Name</label><br /><input type="text" name="first_name" id="first_name_field" class="fifty_fifty" /><input type="text" id="last_name_field" name="last_name" class="fifty_fifty last" /><br /></div>').insertAfter('#password_field');
							$('#registration_fields').hide().slideDown(300);
						});
					});
				});
				return false;
			});
			
			$('#sidebar').append(user_steps);
		}
	},
	twitter: {
		url: 'http://search.twitter.com/search.json?callback=?&q=',
		query: '',
		_interval: false,
		_tweets: [],
		_intervalTime: null,
		_unread: 0,
		rel: function (date) {
			var d = Date.parse(date);
			var dateFunc = new Date();
			var timeSince = dateFunc.getTime() - d;
			var inSeconds = timeSince / 1000;
			var inMinutes = timeSince / 1000 / 60;
			var inHours = timeSince / 1000 / 60 / 60;
			var inDays = timeSince / 1000 / 60 / 60 / 24;
			var inYears = timeSince / 1000 / 60 / 60 / 24 / 365;
			 
			// in seconds
			if(Math.round(inSeconds) == 1){
			return ("1 second ago");
			}
			else if(inMinutes < 1.01){
			return (Math.round(inSeconds) + " seconds ago");
			}
			 
			// in minutes
			else if(Math.round(inMinutes) == 1){
			return ("1 minute ago");
			}
			else if(inHours < 1.01){
			return (Math.round(inMinutes) + " minutes ago");
			}
			 
			// in hours
			else if(Math.round(inHours) == 1){
			return ("1 hour ago");
			}
			else if(inDays < 1.01){
			return (Math.round(inHours) + " hours ago");
			}
			 
			// in days
			else if(Math.round(inDays) == 1){
			return ("1 day ago");
			}
			else if(inYears < 1.01){
			return (Math.round(inDays) + " days ago");
			}
			 
			// in years
			else if(Math.round(inYears) == 1){
			return ("1 year ago");
			}
			else
			{
			return (Math.round(inYears) + " years ago");
			}
		},
		init: function () {
			if (ccc._allowTwitter) {
				var form = $('<form></form>');
				form.append('<input type="text" value="'+c.twitter.query+'" autocomplete="" />');
				form.bind('submit', function () {
					var val = $('#tweetbar form input[type=text]').val();
					$.ajax({
						url: 'twitter/tweet',
						data: {tweet: val},
						type: 'POST',
						success: function (data) {
							$('#tweetbar form input[type=text]').val(c.twitter.query);
						}
					});
					return false;
				});
				
				$('#tweetbar').append(form);
				$('#tweetbar form input[type=text]').width($('#chatbar form').width() - 10);
				$('#tweetbar form').css({
					'background-position': '-239px 5px'
				});
			}
			else {
				$('#tweetbar').append('<a href="/twitter">Click here</a> to authorize Twitter.');
			}
		},
		start: function() {
			c.twitter._interval = setInterval(function () {
				c.twitter.load();
			}, 5000);
			
			if (!this._intervalTime) {
				this._intervalTime = setInterval(function () {
					$('#tweets .tweet .at').each(function () {
						var date = $(this).attr('rel')
						$(this).text(c.twitter.rel(date));
					});
				}, 5000);
			}
		},
		pause: function() {
			clearInterval(c.twitter._interval);
		},
		load: function() {
			$.getJSON(c.twitter.url + escape(c.twitter.query), function(data) {
				var tweets = data.results;
				
				var new_tweet_count = 0;
	
				var tab = $('#tweets');
				
				var newTweets = '';
				
				if ($('#twitter_tab_button').hasClass('hidden') === false) {
					tab.find('.tweet.unread').removeClass('unread');
				}
				
				for (var i=0, len=tweets.length; i<len; i++) {
					if (!c.twitter._tweets[tweets[i].id]) {
						new_tweet_count++;
						
						newTweets += ('<div class="tweet unread"><div>' + tweets[i].text + '</div><strong class="who"><a href="http://twitter.com/' + tweets[i].from_user + '">@' + tweets[i].from_user + '</a> tweeted <span class="at" rel="'+tweets[i].created_at+'">'+c.twitter.rel(tweets[i].created_at)+'</span></strong> </div>');
						c.twitter._tweets[tweets[i].id] = tweets[i];
					}
				}
				tab.prepend(newTweets);
				
				c.twitter._unread = ($('#twitter_tab_button').hasClass('hidden') ? c.twitter._unread : 0) + new_tweet_count;
				
				
				if (c.twitter._unread > 0) {
					if ($('#twitter_tab_button .badge').size() === 0) {
						$('#twitter_tab_button').append('<span class="badge"></span>');
					}
					$('#twitter_tab_button .badge').text(c.twitter._unread);
				}
				else {
					$('#twitter_tab_button .badge').remove();
				}
				
				if ( new_tweet_count > 0 )
				{
					$('#tweet_count').text(new_tweet_count + ' New Tweets!');
				}
				else
				{
					$('#tweet_count').text('No New Tweets!');
				}
			})
		}
	},
	resizeSidebar: function () {
		$('#sidebar').css({width: $('#content .container').width() - 610});
	},
	log: function (message, type) {
		//return ccc.log('Celebrate: ' + message, type);
	},
	save_notes: function () {
		var notes = $('#notes_textarea').val();

		$.ajax({
			url: 'watch/'+c.service_id+'/note/save/',
			type: 'POST',
			data: {
				'content': notes
			},
			success: function () {
				var date = new Date();
				$('#notes_status .last_update').text('Auto-saved at ' + (date.getHours() > 12 ? date.getHours() - 12 : date.getHours()) + ':' + (date.getMinutes() < 10 ? '0' : '') + date.getMinutes() + (date.getHours() < 12 ? 'am' : 'pm'));
			}
		});
	},
	change_dc: function (type, content) {
		if (type == 'image') {
			var newImg = $('<img />', {
				'src': content
			});
			if (content.indexOf('GBTG') >= 0) {
				newImg.click(function () {
					ccc.give.show();
				}).css({
					'cursor': 'pointer'
				});
			}
			newImg.appendTo('#dynamic_content').css('visibility', 'hidden').load(function () {
				newImg.css({
					'position': 'absolute',
					'visibility': 'visible',
					'top': 0,
					'left': 0,
					'opacity': 0
				});
				if ($('#dynamic_content img').size() === 2) {
					newImg.animate({
						'opacity': 1
					}, 350, function () {
						$('#dynamic_content img').eq(0).remove();
					});
				}
				else {
					newImg.css({
						'opacity': 1
					});
				}
			});
		}
		return true;
	},
	chat: {
		_autocollapse: false,
		_refreshrate: 5000,
		_currentroom: 1,
		_unread: 0,
		update_position_of_reply_prompt: function () {
			if ($('#reply_prompt').size() === 0) return false;
			
			setTimeout(function () {
				var messages = $('.messages[rel='+$('#reply_prompt').attr('rel')+']');
				var fromTop  = messages.position().top + 35 + $('#chatroom').scrollTop();
				
				$('#reply_prompt').animate({
					'top': fromTop
				}, 350);
			}, 500);
		},
		init: function () {
			$('#chatroom').addClass('no_facilitator');
			$('#chatroom').append('<div id="no_facilitator">This group\'s leader isn\'t online.</a>');
			$('#no_facilitator').hide();
			//$('#chatroom').append('<div id="chat_messages"></div>');
			if (ccc._user) {
				var form = $('<form></form>');
				form.append('<input type="text" />');
				form.bind('submit', function () {
					var val = $('#chatbar form input[type=text]').val();
					c.chat.message(val);
					$('#chatbar form input[type=text]').val('');
					return false;
				});
				
				$('#chatbar').append(form);
				$('#chatbar form input[type=text]').width($('#chatbar form').width() - 10);
				$('#chatbar form').css({
					'background-position': '-239px 5px'
				});
			}
			this.start();
			
			//hover effect for messages
			/*$('#chatroom .messages').live('mouseover', function () {
				$(this).find('.buttons').css('opacity', 1);
			}).live('mouseout', function () {
				$(this).find('.buttons').css('opacity', 0);
			});*/
			
			$('#chatroom .messages').live('mouseover', function () {
				var id = $(this).attr('rel');
				
				c.chat._unread -= $(this).attr('data-totalunread') ? $(this).attr('data-totalunread') : 0;
				
				$(this).attr('data-totalunread', '0')
				var replies = $(this).find('.reply');
				replies.each(function () {
					var id = $(this).attr('rel');
					c.storage_unread(id, false);
				});
				
				var reply_count = $(this).find('.reply_count');
				
				setTimeout(function () {
					reply_count.removeClass('unread');
					
					if ($('#chat_tab_button .badge').size() && c.chat._unread > 0) {
						$('#chat_tab_button .badge').text(c.chat._unread);
					}
					else {
						$('#chat_tab_button .badge').remove();
					}
				}, 1500);
			});
			
			//initialize collapse buttons
			$('#chatroom .messages .collapse_button').live('click', function () {
				$(this).toggleClass('uncollapse');
				$(this).parents('.messages').toggleClass('collapsed');
				$(this).parents('.message').toggleClass('collapsed');
				var replies = $(this).parents('.messages').find('.reply');
				if ($.browser.safari) {
					replies.toggle();
				}
				else {
					replies.stop(true, true).slideToggle(250);
					/*replies.each(function () {
						$(this).slideToggle(250);
					});*/
				}
				
				var id = $(this).parents('.messages').attr('rel');
				c.storage_collapsed(id, $(this).parents('.message').hasClass('collapsed'));
				
				c.chat.update_position_of_reply_prompt();
				
				return false;
			});
			
			//initialize reply buttons
			$('#chatroom .message .reply_button, #chatroom .messages .add_reply_at_bottom').live('click', function () {
				c.chat.show_reply($(this).parents('.messages').attr('rel'), 'new');
				
				return false;
			});
			
			//this._iscroll = new iScroll('chat_messages', {checkDOMChanges: false});
		},
		_api: 'chat_api.php',
		_user_name: null,
		_is_member: null,
		_facilitators_online: false,
		_messages: [],
		_replies: [],
		_messagesHash: null,
		since_id: 0,
		reply_id: 0,
		message_exists: function (id) {
			for (var i=0, len=this._messages.length; i<len; i++) {
				if (this._messages[i].id === id) {
					return true;
				}
			}
			return false;
		},
		reply_exists: function (id) {
			for (var i=0, len=this._replies.length; i<len; i++) {
				if (this._replies[i].id === id) {
					return true;
				}
			}
			return false;
		},
		add_reply: function (reply) {
			var message = null;
			for (var i=0, len=this._messages.length; i<len; i++) {
				if (this._messages[i].id === reply.reply_to) {
					message = this._messages[i];
				}
			}
			if (message !== null) {
				var name = reply.name;
				    name = name.split(' ');
				var last = name[name.length - 1][0] + '.';
				    name[name.length - 1] = last;
				    name = name.join(' ');
				
				var ex = new RegExp();
				ex.compile('([A-Za-z]+://[A-Za-z0-9-_]+\.[A-Za-z0-9-_%&?/.=]+)');
				reply.text = reply.text.replace(ex, '<a href="$1" target="_blank">$1</a>');
				
				var messageDom = $('#chatroom .messages[rel='+message.id+']');
				messageDom.find('.reply').removeClass('last');
				var dom = $('<div class="reply last" title="'+reply.date+'" rel="'+reply.id+'"><div class="text"><strong rel="'+reply.uid+'">'+name+(reply.is_guest ? ' (guest)' : '')+'</strong>: '+reply.text+'</div></div>');
				/*if ($('.reply_form', messageDom).size() === 0) {
					messageDom.append(dom);
				}
				else {
					dom.insertBefore($('.reply_form', messageDom));
				}*/
				dom.insertBefore(messageDom.find('.add_reply_at_bottom'));
				messageDom.find('.add_reply_at_bottom').show();
				
				messageDom.find('.reply').eq(0).addClass('first');
				if (messageDom.find('.message').hasClass('collapsed') === true) {
					dom.slideUp(0);
				}
				this._replies.push({
					id: reply.id,
					'dom': dom
				});
				
				c.storage_unread(reply.id);
				if (c.storage_unread(reply.id) === true) {
					messageDom.find('.reply_count').addClass('unread');
					c.storage_unread(reply.id, true);
					c.chat._unread++;
					
					messageDom.attr('data-totalunread', (messageDom.attr('data-totalunread') ? parseInt(messageDom.attr('data-totalunread')) : 0) + 1);
				}
				else {
					messageDom.find('.reply_count').addClass('unread');
					c.chat._unread++;
					
					messageDom.attr('data-totalunread', (messageDom.attr('data-totalunread') ? parseInt(messageDom.attr('data-totalunread')) : 0) + 1);
				}
				messageDom.find('.reply_count').removeClass('hide').find('span').html(messageDom.find('.reply').size());
				messageDom.find('.collapse_button').removeClass('hide');
				messageDom.find('.message').each(function () {
					var text    = $(this).find('.text');
					var buttons = $(this).find('.buttons');
					text.css({
						'margin-right': buttons.width()
					});
				})
				
				//figure out position in viewport
				var messageTop   = messageDom.offset().top;
				var scrollOffset = $('#chatroom').scrollTop();
				var adjustedPos  = messageTop - scrollOffset;
				var position     = null;
				if (adjustedPos > 0 && adjustedPos < $('#chatroom').height()) {
					position = 'in';
				}
				else if (adjustedPos > 0) {
					position = 'down';
				}
				else if (adjustedPos < 0) {
					position = 'up';
				}
				
				if (position !== 'in' && ($('#chat_notification').size() === 0 || $('#chat_notification').hasClass(position) === false) && messageDom.find('.reply_count').hasClass('unread')) {
					var notification = $('<div id="chat_notification">new messages</div>');
					notification.addClass(position);
					//notification.css('top', 200);
					notification.appendTo('#chatroom');
					var displayTime = 1500;
					if (position === 'down') {
						notification.css('top', ($('#chatroom').height() - 40) + $('#chatroom').scrollTop());
						setTimeout(function () {
							notification.animate({
								opacity: 0,
								top: '+=40'
							}, function () {
								notification.remove()
							});
						}, displayTime);
					}
					else {
						notification.css('top', 40 + $('#chatroom').scrollTop());
						setTimeout(function () {
							notification.animate({
								opacity: 0,
								top: '-=40'
							}, function () {
								notification.remove()
							});
						}, displayTime);
					}
				}
				
				//update unread badge
				if (c.chat._unread > 0) {
					if ($('#chat_tab_button .badge').size() === 0) {
						$('#chat_tab_button').append('<span class="badge"></span>');
					}
					$('#chat_tab_button .badge').text(c.chat._unread);
				}
				else {
					$('#chat_tab_button .badge').remove();
				}
				
				c.chat.update_position_of_reply_prompt();
			}
		},
		_status: 'on',
		start: function () {
			this._status = 'on';
			
			if (this._status === 'on') {
				this.load_messages_and_replies();
			}
			
			this._messageInterval = setInterval(function () {
				if (c.chat._status === 'on') {
					c.chat._status = 'loading';
					c.chat.load_messages_and_replies();
				}
			}, this._refreshrate);
		},
		pause: function () {
			this._status = 'off';
		},
		load_messages_and_replies: function () {
			this._status = 'loading';
			$.ajax({
				'url': this._api,
				'data': {action: 'messages_and_replies', group_id: c.group_id, service_id: c.service_id, since_id: c.chat.since_id, reply_id: c.chat.reply_id},
				'error': function () {
					c.chat._status = 'on';
				},
				'success': function (data) {
					if (c.chat._messagesHash === data.hash) {
						c.chat._status = "on";
						return true;
					}
					c.chat._messagesHash = data.hash;
					
					// Need to update group_id
					c.group_id = data.group_id;
					
					if (data.items) {
						c.chat._user_name = data.user_name;
						c._group_name = data.group_name;
						var is_member = data.is_member;
						if (c.chat._is_member !== is_member) {
							if (is_member) {
								$('#member_status').html('<span class="message">Welcome back, '+c.chat._user_name+'</span><a href="#" id="switch_groups_button">Switch Groups</a>');
								$('#member_status').addClass('is_member');
								$('#member_status').removeClass('is_guest');
							}
							else {
								$('#member_status').html('<span class="message">You are a guest, welcome!</span><span class="options"><!--<a href="#" id="join_group_button">Join</a> &bull;//--> <a href="#" id="switch_groups_button">Group Info</a></span>');
								$('#join_group_button').click(function () {
									return false;
								});
								$('#member_status').removeClass('is_member');
								$('#member_status').addClass('is_guest');
							}
							c.chat._is_member = is_member;
						}
						$('#switch_groups_button').unbind('click');
						$('#switch_groups_button').click(function () {
							if (!is_member) {
								$('#switch_groups_button').html($('#switch_groups_button').text() == 'Group Info' ? 'close' : 'Group Info');
							}
							c.chat.group_info();
							return false;
						});
						
						var facilitators_online = data.facilitators_online;
						if (c.chat._facilitators_online !== facilitators_online) {
							if (facilitators_online) {
								$('#no_facilitator').hide();
								$('#chatroom').removeClass('no_facilitator');
							}
							else {
								$('#no_facilitator').show();
								$('#chatroom').addClass('no_facilitator');
							}
							c.chat._facilitators_online = facilitators_online;
						}
						
						var messages = data.items.messages;
						var newMessages = 0;
						$('#chatroom .messages.placeholder').remove();
						if (messages.length > 0) {
							var html = '';
							for (var i=0, len=messages.length; i<len; i++) {
								if (this.message_exists(messages[i].id) === false) {
									var name = messages[i].name;
									    name = name.split(' ');
									var last = name[name.length - 1][0] + '.';
									    name[name.length - 1] = last;
									    name = name.join(' ');
									    
									if ( messages[i].id > c.chat.since_id )
									{
										c.chat.since_id = messages[i].id;
									}
									
									var isCollapsed = c.chat._autocollapse ? true : false;
									if (('localStorage' in window) && window['localStorage'] !== null) {
										if (c.storage_collapsed(messages[i].id)) {
											isCollapsed = true;
										}
									}
									
									var ex = new RegExp();
									ex.compile('([A-Za-z]+://[A-Za-z0-9-_]+\.[A-Za-z0-9-_%&?/.=]+)');
									messages[i].text = messages[i].text.replace(ex, '<a href="$1" target="_blank">$1</a>');
									
									var message = '<div class="messages'+(isCollapsed ? ' collapsed' : '')+'" rel="'+messages[i].id+'"><div class="message'+(isCollapsed ? ' collapsed' : '')+'" rel="'+messages[i].id+'" title="'+messages[i].date+'"><div class="buttons"><a href="#" class="reply_button">reply</a><span class="reply_count hide"><span>0</span></span><a href="#" class="collapse_button uncollapse hide">collapse</a></div><div class="text"><strong rel="'+messages[i].uid+'"'+(messages[i].is_facilitator ? ' class="facilitator"' : '')+'>'+(messages[i].name ? name : '')+(messages[i].is_guest ? ' (guest)' : '')+':</strong> '+messages[i].text+'</div></div><div class="add_reply_at_bottom"><a href="#">add reply</a></div></div>';
									html = message + html;
									
									c.chat._messages.push({
										id: messages[i].id,
										dom: message
									});
									newMessages++;
								}
							}
							html = $(html);
							html.find('.add_reply_at_bottom').hide();
							html.find('.message .text').css('margin-right', function () {
								return $(this).parent().find('.buttons').width();
							});
							html.insertAfter('#chatroom #no_facilitator');
							c.chat.update_position_of_reply_prompt();
							//c.chat._iscroll.refresh();
							//html.appendTo('#chat_messages');
							c.log(newMessages+' new messages');
						}
						
						//process replies
						var replies = data.items.replies;
						var newReplies = 0;
						if (replies.length > 0) {
							for (var i=0, len=replies.length; i<len; i++) {
								if (c.chat.reply_exists(replies[i].id) === false) {
									if ( replies[i].id > c.chat.reply_id )
									{
										c.chat.reply_id = replies[i].id;
									}
								
									c.chat.add_reply(replies[i]);
									newReplies++;
								}
							}
							c.log(newReplies+' new replies');
						}
					}
					c.chat._status = 'on';
				},
				context: c.chat
			});
		},
		expand_all: function () {
			var messages = $('#chatroom .messages');
			messages.each(function () {
				if ($(this).find('.message').hasClass('collapsed')) {
					$(this).find('.collapse_button').click();
				}
			});
		},
		collapse_all: function () {
			var messages = $('#chatroom .messages');
			messages.each(function () {
				if (!$(this).find('.message').hasClass('collapsed')) {
					$(this).find('.collapse_button').click();
				}
			});
		},
		message: function (text) {
			if (!text) return false;
			
			$.post(this._api + '?action=post', {'message': text, 'user': ccc._user, 'service_id': c.service_id, 'group_id': c.group_id});
			
			var name = c.chat._user_name;
			    name = name.split(' ');
			var last = name[name.length - 1][0] + '.';
			    name[name.length - 1] = last;
			    name = name.join(' ');
			
			var message = '<div class="messages placeholder"><div class="message'+(c.chat._autocollapse ? ' collapsed' : '')+'"><div class="text"><strong>'+name+(!c.chat._is_member ? ' (guest)' : '')+':</strong> '+text+'</div></div></div>';
			$(message).insertAfter('#chatroom #no_facilitator');
		},
		reply: function (id, text) {
			if (!text) return false;
			
			$.post(this._api + '?action=reply', {'id': id, 'message': text, 'user': ccc._user, 'service_id': c.service_id, 'group_id': c.group_id});
		},
		show_reply: function (message_id, type) {
			switch (type) {
				case 'new':
					var messages = $('#chatroom .messages[rel='+message_id+']');
					
					if (messages.find('.reply_form').size()) {
						messages.find('.reply_form textarea').focus();
					}
					else {
						function remove() {
							messages.find('.reply_form').remove();
							messages.find('.add_reply_at_bottom').removeClass('open');
							messages.find('.reply').eq(-1).addClass('last');
						}
						
						if (messages.find('.message').hasClass('collapsed')) {
							messages.find('.collapse_button').click();
						}
						
						messages.find('.add_reply_at_bottom').addClass('open');
						messages.find('.reply').eq(-1).removeClass('last');
						
						$('<form class="reply_form" rel="'+message_id+'"><textarea></textarea></form>').submit(function () {
							c.chat.reply(message_id, messages.find('textarea').val());
							remove();
							return false;
						}).appendTo(messages).find('textarea').focus().keydown(function (e) {
							if (e.keyCode === 13) {
								$(this).parents('form').submit();
								return false;
							}
						}).blur(function () {
							if (!messages.find('textarea').val()) remove();
						});
						$('.reply_form textarea').width($('.reply_form').width() - 8);
					}
					break;
				default:
					//model
					var reply_button = $('#chatroom .messages[rel='+message_id+'] .reply_button');
					
					var right = 0;
					if (reply_button.parent().find('.reply_count').hasClass('hide') === false) {
						right += reply_button.parent().find('.reply_count').width();
					}
					if (reply_button.parent().find('.collapse_button').hasClass('hide') === false) {
						right += 20;
					}
					if (right > 10) {
						right -= 10;
					}
					
					var id  = message_id;
					
					if ($('#reply_prompt').size()) {
						if ($('#reply_prompt').attr('rel') == id) {
							$('#reply_prompt').remove();
							return false;
						}
						else {
							$('#reply_prompt').remove();
						}
					}
					
					var fromTop = reply_button.parents('.messages').position().top + 35 + $('#chatroom').scrollTop();
					
					$('<form id="reply_prompt" rel="'+id+'"><textarea></textarea></form>').submit(function () {
						c.chat.reply(id, $('#reply_prompt textarea').val());
						$('#reply_prompt').remove();
						return false;
					}).hide().appendTo('#chatroom').fadeIn(350);
					$('#reply_prompt').css('top', fromTop);
					$('#reply_prompt textarea').keydown(function (e) {
						if (e.keyCode === 13) {
							$(this).parents('form').submit();
							return false;
						}
					}).focus();
					$('#reply_prompt').append('<style> #sidebar #chatroom #reply_prompt:after { right: '+right+'px } </style>');
					break;
			}
		},
		join_group_as_guest: function (group_id) {
			if (c.group_id === group_id) return false;
			
			c.group_id = group_id;
			
			//remove messages
			$('#chatroom .messages').remove();
			
			//remove local cache
			c.chat._messages = [];
			c.chat._replies  = [];
			c.chat.messagesHash = '';
			c.chat.since_id = 0;
			c.chat.reply_id = 0;
			
			//load new messages and replies
			c.chat.load_messages_and_replies();
		},
		join_group: function (group_id) {
			//call groups API
		},
		get_online_group_list: function () {
			//call groups API
			
		},
		group_info: function () {
			if ($('#chat_group_info').size()) {
				$('#switch_groups_button').animate({
					top: '-=24'
				}, 150);
				$('#close_switch_groups_button').animate({
					top: '-=24'
				}, 150, function () {
					$('#close_switch_groups_button').remove();
				});
				$('#chat_group_info_content').animate({
					height: 0
				}, 350, function () {
					$('#chat_group_info').remove();
				});
			}
			else {
				$('#member_status').append($('<a />', {
					id: 'close_switch_groups_button',
					href: '#',
					click: function () {
						c.chat.group_info();
						return false;
					}
				}));
				
				var chat_group_info = $('<div />', {
					id: 'chat_group_info'
				});
				var content = $('<div />', {
					id: 'chat_group_info_content',
					css: {
						height: 0
					}
				});
				content.appendTo(chat_group_info);
				
				//add group info and link to switch groups
				$('<div />', {
					id: 'group_info',
					html: '<span class="info">Your Group: <span class="group_name">'+c._group_name+'</span></span><a href="#" id="switch_group_link">Switch Group</a>'
				}).appendTo(content);
				$('#switch_group_link', content).click(function () {
					c.chat.show_switch_groups();
					return false;
				});
				
				$('#chat_tab').append(chat_group_info);
				
				$('#switch_groups_button').animate({
					top: '+=24'
				}, 150);
				$('#close_switch_groups_button').animate({
					top: '+=24'
				}, 150);
				
				//load online users
				$.ajax({
					'url': '/api/connect/users/online/'+c.group_id,
					'dataType': 'json',
					'success': function (data) {
						$('<div id="chat_group_info_content_online"><h2>Online Users</h2><ul class="online_users"></ul></div>').insertAfter('#group_info');
						for (var i=0, len=data.length; i<len; i++) {
							$('.online_users').append('<li>'+data[i].full_name+'</li>');
						}
						setTimeout(function () {
							content.animate({
								height: 434
							}, 350, function () {
								content.hide().show();
							});
						}, 500);
					}
				});
			}
		},
		show_select_group: function () {
			/*$('#member_status').append($('<a />', {
				id: 'close_switch_groups_button',
				href: '#',
				click: function () {
					c.chat.group_info();
					return false;
				}
			}));*/
			
			var chat_group_info = $('<div />', {
				id: 'chat_group_info'
			});
			var content = $('<div />', {
				id: 'chat_group_info_content'
			});
			content.appendTo(chat_group_info);
			
			//add group info and link to switch groups
			/*$('<div />', {
				id: 'group_info',
				html: '<span class="info">Your Group: <span class="group_name">'+c._group_name+'</span></span><a href="#" id="switch_group_link">Switch Group</a>'
			}).appendTo(content);*/
			/*$('#switch_group_link', content).click(function () {
				c.chat.show_switch_groups();
				return false;
			});*/
			
			$('#chat_tab').append(chat_group_info);
			
			/*$('#switch_groups_button').animate({
				top: '+=24'
			}, 150);
			$('#close_switch_groups_button').animate({
				top: '+=24'
			}, 150);*/
			
			$('#chat_group_info_content_online').hide();
			//$('#switch_group_link').text('Cancel');
			
			$('<div id="chat_group_info_content_groups"><h2>Select a Group to Participate With</h2><ul></ul></div>').appendTo('#chat_group_info_content');
			$.ajax({
				'url': '/api/connect/online',
				'dataType': 'json',
				'success': function (data) {
					var ul = $('#chat_group_info_content_groups ul');
					for (var i=0, len=data.length; i<len; i++) {
						$('<li><a href="#" rel="'+data[i].group_id+'">'+data[i].group_name+'</a></li>').find('a').click(function () {
							c.group_id = parseInt($(this).attr('rel'));
							c.chat.init();
							c.chat.group_info();
							//c.chat.join_group_as_guest(group_id);
							//c.chat.group_info();
							return false;
						}).parent().appendTo(ul);
						//ul.append('<li><a href="#">'+data[i].group_name+'</a></li>');
					}
				}
			});
		},
		show_switch_groups: function () {
			if ($('#chat_group_info').size() && $('#switch_group_link').text() === 'Switch Group') {
				$('#chat_group_info_content_online').hide();
				$('#switch_group_link').text('Cancel');
				
				$('<div id="chat_group_info_content_groups"><h2>Online Groups</h2><ul></ul></div>').appendTo('#chat_group_info_content');
				$.ajax({
					'url': '/api/connect/online',
					'dataType': 'json',
					'success': function (data) {
						var ul = $('#chat_group_info_content_groups ul');
						for (var i=0, len=data.length; i<len; i++) {
							$('<li><a href="#" rel="'+data[i].group_id+'">'+data[i].group_name+'</a></li>').find('a').click(function () {
								var group_id = parseInt($(this).attr('rel'));
								c.chat.join_group_as_guest(group_id);
								c.chat.group_info();
								return false;
							}).parent().appendTo(ul);
							//ul.append('<li><a href="#">'+data[i].group_name+'</a></li>');
						}
					}
				});
				//$('#chat_group_info_content');
			}
			else {
				$('#chat_group_info_content_online').show();
				$('#switch_group_link').text('Switch Group');
				$('#chat_group_info_content_groups').remove();
			}
		}
	}
};

$(function () {
	if ($('#video_box').size()) {
		c.init();
		
		$('#footer_button_feedback a').click(function () {
			return confirm('Are you sure that you want to leave this page?');
		});
	}
});