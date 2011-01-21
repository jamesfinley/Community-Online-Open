var chat = {
	//settings
	_autocollapse: false,
	//variables
	_status: 'off',
	_unread: 0,
	_user_name: null,
	_is_member: null,
	_facilitators_online: false,
	_messages: [],
	_replies: [],
	_messagesHash: null,
	since_id: 0,
	reply_id: 0,
	init: function () {
		//add no facilitator bar and hide it
		$('#chatroom').append('<div id="no_facilitator">This group\'s leader isn\'t online.</a>').find('#no_facilitator').hide();
		
		//if user is logged in, add input for chat
		if (ccc._user) {
			var form = $('<form><input type="text" /></form>').bind('submit', function () {
				var val = $('#chatbar form input').val();
				if (!val) return false;
				
				chat.message.val(val);
				$('#chatbar form input').val('');
			}).appendTo('#chatbar').find('input').width(form.width() - 10).parent().css('background-position', '-239px 5px');
		}
		
		//handle unread to read mouseover
		$('#chatroom .messages').live('mouseover', function () {
			var id = $(this).attr('rel');
			
			chat._unread -= $(this).attr('data-totalunread') ? $(this).attr('data-totalunread') : 0;
			
			if (('localStorage' in window) && window['localStorage'] !== null) {
				//store read statuses
				$(this).attr('data-totalunread', '0');
				$(this).find('.reply').each(function () {
					localStorage['co.chat.reply.' + $(this).attr('rel') + '.isunread'] = 'false';
				});
			}
			
			var reply_count = $(this).find('.reply_count');
			
			setTimeout(function () {
				reply_count.removeClass('unread');
				
				if ($('#chat_tab_button .badge').size() && chat._unread > 0) {
					$('#chat_tab_button .badge').text(chat._unread);
				}
				else {
					$('#chat_tab_button .badge').remove();
				}
			}, 1500);
		});
		
		//initialize collapse buttons
		$('#chatroom .messages .collapse_button').live('click', function () {
			$(this).toggleClass('uncollapse');
			$(this).parents('.message').toggleClass('collapsed');
			$(this).parents('.messages').find('.reply').slideToggle(250);
			
			if (('localStorage' in window) && window['localStorage'] !== null) {
				var id = $(this).parents('.messages').attr('rel');
				localStorage['co.chat.message.'+id+'.iscollapsed'] = $(this).parents('.message').hasClass('collapsed') ? 'true' : 'false';
			}
			
			return false;
		});
		
		//start the chat system
		this.start();
	},
	start: function () {
		this._status = 'on';
		
		//load messages and replies
		this.load();
	},
	pause: function () {
		this._status = 'off';
	},
	process_text: function (text) {
		var ex = new RegExp();
		ex.compile('([A-Za-z]+://[A-Za-z0-9-_]+\.[A-Za-z0-9-_%&?/.=]+)');
		text = text.replace(ex, '<a href="$1" target="_blank">$1</a>');
		
		return text;
	},
	load: function () {
		if (this._status !== 'on') return false;
		
		//load messages and replies
		$.ajax({
			'url': this._api,
			'data': {action: 'messages_and_replies', group_id: c.group_id, service_id: c.service_id, since_id: chat.since_id, reply_id: chat.reply_id},
			'error': function () {
				chat._status = 'on';
			},
			'success': function (data) {
				if (chat._messagesHash === data.hash) {
					chat._status = "on";
					return true;
				}
				chat._messagesHash = data.hash;
				
				// Need to update group_id
				c.group_id = data.group_id;
				
				if (data.items) {
					//process non-message data
					chat._user_name = data.user_name;
					c._group_name = data.group_name;
					var is_member = data.is_member;
					if (chat._is_member !== is_member) {
						if (is_member) {
							$('#member_status').addClass('is_member').removeClass('is_guest').html('<span class="message">Welcome back, '+chat._user_name+'</span><a href="#" id="switch_groups_button">Switch Groups</a>');
						}
						else {
							$('#member_status').addClass('is_guest').removeClass('is_member').html('<span class="message">You are a guest, welcome!</span><span class="options"><!--<a href="#" id="join_group_button">Join</a> &bull;//--> <a href="#" id="switch_groups_button">Switch Groups</a></span>');
							$('#join_group_button').click(function () {
								return false;
							});
						}
						chat._is_member = is_member;
					}
					$('#switch_groups_button').unbind('click').click(function () {
						if (!is_member) {
							$('#switch_groups_button').html($('#switch_groups_button').text() == 'Switch Groups' ? 'close' : 'Switch Groups');
						}
						chat.group_info();
						return false;
					});
					
					var facilitators_online = data.facilitators_online;
					if (chat._facilitators_online !== facilitators_online) {
						$('#no_facilitator').toggle();
						$('#chatroom').toggleClass('no_facilitator');
						
						chat._facilitators_online = facilitators_online;
					}
					
					//process messages
					var messages = data.items.messages;
					var newMessages = 0;
					$('#chatroom .messages.placeholder').remove();
					if (messages.length > 0) {
						var html = '';
						for (var i=0, len=messages.length; i<len; i++) {
							if (this.message_exists(messages[i].id) === false) {
								var name = messages[i].name.split(' ');
									name[name.length - 1] = name[name.length - 1][0] + '.';
									name = name.join(' ');
								    
								if (messages[i].id > chat.since_id) {
									chat.since_id = messages[i].id;
								}
								
								var isCollapsed = chat._autocollapse ? true : false;
								if (('localStorage' in window) && window['localStorage'] !== null) {
									if (localStorage['co.chat.message.'+messages[i].id+'.iscollapsed'] == 'true') {
										isCollapsed = true;
									}
								}
								
								var message = '<div class="messages" rel="'+messages[i].id+'"><div class="message'+(isCollapsed ? ' collapsed' : '')+'" rel="'+messages[i].id+'" title="'+messages[i].date+'"><div class="text"><strong rel="'+messages[i].uid+'"'+(messages[i].is_facilitator ? ' class="facilitator"' : '')+'>'+(messages[i].name ? name : '')+(messages[i].is_guest ? ' (guest)' : '')+':</strong> '+chat.process_text(messages[i].text)+'</div><div class="buttons"><a href="#" class="reply_button">reply</a><span class="reply_count hide"><span>0</span></span><a href="#" class="collapse_button uncollapse hide">collapse</a></div></div></div>';
								html = message + html;
								
								chat._messages.push({
									id: messages[i].id,
									dom: message
								});
								newMessages++;
							}
						}
						html = $(html);
						html.find('.message .text').css('margin-right', function () {
							return $(this).parent().find('.buttons').width();
						});
						html.insertAfter('#chatroom #no_facilitator');
						
						c.log(newMessages+' new messages');
					}
					
					//process replies
					var replies = data.items.replies;
					var newReplies = 0;
					if (replies.length > 0) {
						for (var i=0, len=replies.length; i<len; i++) {
							if (chat.reply(replies[i].id) === false) {
								if (replies[i].id > chat.reply_id) {
									chat.reply_id = replies[i].id;
								}
							
								chat.add_reply(replies[i]);
								newReplies++;
							}
						}
						c.log(newReplies+' new replies');
					}
				}
				
				//hit load again
				chat.load();
			}
		});
	},
	show_reply_form: function () {
		
	},
	message: function (obj) {
		
	},
	reply: function (id, text) {
		if (text) {
			//send to api
			return true;
		}
		else if (id) {
			//return dom object
			for (var i=0, len=this._replies.length; i<len; i++) {
				if (this._replies[i].id === id) {
					return $('#chatroom .reply[rel='+id+']');
				}
			}
			return false;
		}
		
		return false;
	},
	join_group_as_guest: function () {
		
	},
	join_group: function () {
		
	},
	get_online_group_list: function () {
		
	},
	show_group_info: function () {
		
	},
	show_switch_groups: function () {
		
	}
};