var pageURL = location.href.split('#')[0];

var ccc = {
	init: function () {
		$('#account_button').click(function () {
			$('#account_menu').toggle();
			return false;
		});
		
		if (!navigator.geolocation && $('#local_search_link').size()) {
			$('#local_search_link').hide();
		}
		else if (navigator.geolocation && $('#local_search_link').size()) {
			$('#local_search_link').click(function () {
				$('#local_search_link a').text('Searching for Local Groups...');
				connect.location.find_closest_groups('all', 5, 10, function (groups) {
					$('#local_search_link').hide();
					
					$('<li class="header local">Local Groups (<a href="#">close</a>)</li>').insertBefore('#logout_button');
					for (var i=0, len=groups.length; i<len; i++) {
						$('<li class="local group"><a href="'+groups[i].url+'">'+groups[i].name+' ('+groups[i].distance+'mi)</a></li>').insertBefore('#logout_button');
					}
					$('#account_menu .header.local a').click(function () {
						$('#account_menu li.local').remove();
						$('#local_search_link').show();
						return false;
					});
					$('#local_search_link a').text('Search for Local Groups');
				}, function () {
					$('#local_search_link a').text('Search for Local Groups');
				});
				return false;
			});
		}
	},
	utils: {
		storage: function (variable, value) {
			if (('localStorage' in window) && window['localStorage'] !== null) {
				if (value !== undefined) {
					localStorage[variable] = value;
				}
				return localStorage[variable];
			}
			return undefined;
		},
		animate: function (el, animation, duration, callback) {
			duration = duration ? duration : 500;
			
			if ($.browser.webkit) {
				
			}
			el.animate(animation, duration, callback);
		},
		Modal: function (el) {
			var s = $('<div class="model_shadow"></div>');
			var w = $('<div class="model"><div class="inner"></div></div>');
			var m = w.find('.inner'), attach, detach, append;
			
			if (el) {
				m.append(el);
			}
			
			attach = this.attach = function (withShadow) {
				//if withShadow, create shadow
				s.click(function () {
					detach();
					return false;
				});
				s.appendTo('body');
				
				if (!withShadow) {
					s.addClass('transparent');
				}
				
				//attach model to body
				w.appendTo('body');
				
				//set height of model and center it
				var height = m.height() > 50 ? m.height() : 50;
				m.height(height);
				w.height(height).css({
					'margin-top': 0 - (height / 2)
				});
			};
			
			detach = this.detach = function () {
				ccc.utils.animate(w, {
					'margin-top': '-=100',
					opacity: 0
				}, 500, function () {
					w.remove();
					s.remove();
				});
				ccc.utils.animate(s, {
					opacity: 0
				});
			};
			
			
			append = this.append = function (el) {
				m.append(el);
			};
		}
	},
	notifications: {
		_count: 0,
		init: function () {
			if ($('#notification_count').size() === 0) return false;
			
			if (parseInt($('#notification_count').text()) !== 0) {
				this._count = parseInt($('#notification_count').text());
				this.show_ribbon();
			}
			$('body').click(function () {
				ccc.notifications.hide_window();
			});
			$('#notification_count').click(function () {
				ccc.notifications.show_window();
				return false;
			});
			$('#notification_window ul li a').click(function (e) {
				var id = $(this).parent().attr('data-id');
				$(this).parent().removeClass('unread');
				var href = $(this).parent().attr('data-link');
				ccc.notifications.mark_as_read(id, function () {
					if (href) {
						location.href = href;
					}
				});
				e.stopPropagation();
				
				return false;
			});
			
			setInterval(function () {
				ccc.notifications.check_for_notifications();
			}, 15000);
			
			$('#notification_window h2 a').click(function () {
				var ids = [];
				$('#notification_window ul li').each(function () {
					ids.push($(this).attr('data-id'));
				});
				ccc.notifications.mark_all_read(ids.join(','), function () {
					ccc.notifications.hide_window();
				});
				return false;
			});
		},
		mark_all_read: function (ids, callback) {
			this._count = 0;
			$('#notification_count').text(this._count);
			if (this._count === 0) {
				this.hide_ribbon();
			}
			$.ajax({
				'url': '/api/notifications/mark_all_read',
				'data': {
					'ids': ids
				},
				'type': 'post',
				'dataType': 'json',
				'success': function (data) {
					if (callback) callback();
				}
			});
		},
		mark_as_read: function (id, callback) {
			this._count--;
			$('#notification_count').text(this._count);
			if (this._count === 0) {
				this.hide_ribbon();
			}
			$.ajax({
				'url': '/api/notifications/mark_as_read/'+id,
				'dataType': 'json',
				'success': function (data) {
					if (callback) callback();
				}
			});
		},
		show_window: function () {
			if (this._count > 0) {
				$('#notification_window').show();
			}
		},
		hide_window: function () {
			$('#notification_window').hide();
			
			//rebuild
			$('#notification_count').text(this._count);
			if (this._notifications) {
				this.rebuild_list();
			}
			else {
				$('#notification_window li').each(function () {
					if (!$(this).hasClass('unread')) $(this).remove();
				});
			}
		},
		show_ribbon: function () {
			$('#notifications_ribbon').css({
				'display': 'block',
				'height': 0
			}).animate({
				'height': 40
			}, 350);
		},
		hide_ribbon: function () {
			if ($('#notifications_ribbon').css('display') == 'none') {
				$('#notifications_ribbon').css('height', 0);
			}
			else {
				$('#notifications_ribbon').animate({
					'height': 0
				}, 350, function () {
					$('#notifications_ribbon').css('display', 'none');
				});
			}
		},
		rebuild_list: function () {
			$('#notification_window ul li').remove();
			for (var i=0, len=this._notifications.length; i<len; i++) {
				$('#notification_window ul').append('<li class="unread" data-id="'+this._notifications[i].id+'" data-link="'+this._notifications[i].link+'"><a href="'+(this._notifications[i].link ? this._notifications[i].link : '#')+'">'+this._notifications[i].short_message+' <span class="created_at">'+this._notifications[i].created_at+'</span></a></li>');
			}
			/*$('#notification_window ul li').click(function (e) {
				var id = $(this).attr('data-id');
				$(this).removeClass('unread');
				ccc.notifications.mark_as_read(id);
				e.stopPropagation();
				return true;
			});*/
		},
		check_for_notifications: function () {
			$.ajax({
				'url': '/api/notifications/unread',
				'dataType': 'json',
				'success': function (data) {
					if (data) {
						var count = parseInt(data.count);
						var results = data.results;
						ccc.notifications._count = count;
						ccc.notifications._notifications = results;
						if ($('#notification_window').css('display') == 'none') {
							//rebuild list
							ccc.notifications.rebuild_list();
							
							//update count
							ccc.notifications._count = count;
							$('#notification_count').text(count);
							if (count === 0) {
								ccc.notifications.hide_ribbon();
							}
							else if ($('#notifications_ribbon').css('display') === 'none') {
								ccc.notifications.show_ribbon();
							}
						}
					}
				}
			});
		}
	},
	give: {
		hide: function () {
			if ($('#giveback').size()) {
				$('#giveback').animate({
					'top': 20,
					'opacity': 0
				}, 350, function () {
					$('#giveback').remove();
				});
			}
		},
		activate: function () {
			if ($('#giveback').size() === 0) return false;
			
			var form = $('#giveback');
			
			//activate segmented control
			var payment_type = 'credit';
			form.find('.segmented .tab').click(function () {
				var value = $(this).text();
				$(this).parent().find('.tab').removeClass('selected');
				$(this).addClass('selected');
				
				switch (value) {
					case 'credit':
						payment_type = 'credit';
						setTimeout(function () {
							$('#giveback_payment #giveback_echeck_payment').hide();
						}, 400);
						if ($.browser.webkit) {
							$('#giveback_payment #giveback_credit_payment').css('left', 9);
							$('#giveback_payment #giveback_echeck_payment').css('left', 328);
						}
						else {
							$('#giveback_payment #giveback_credit_payment').animate({
								'left': 9
							}, 350);
							$('#giveback_payment #giveback_echeck_payment').animate({
								'left': 328
							}, 350);
						}
						break;
					case 'echeck':
						payment_type = 'echeck';
						$('#giveback_payment #giveback_echeck_payment').show();
						if ($.browser.webkit) {
							$('#giveback_payment #giveback_credit_payment').css('left', -328);
							$('#giveback_payment #giveback_echeck_payment').css('left', 9);
						}
						else {
							$('#giveback_payment #giveback_credit_payment').animate({
								'left': -328
							}, 350);
							$('#giveback_payment #giveback_echeck_payment').animate({
								'left': 9
							}, 350);
						}
						break;
				}
				
				return false;
			});
			
			var credit = $('#giveback_credit_payment');
			credit.find('#giveback_cc').keyup(function () {
				//indentify card
				var value = $(this).val();
				value = value.replace(/[^0-9]/gi, '');
				
				//card type
				var card = null;
				if (value.length === 16 && value.indexOf('6011') === 0) {
					card = 'discover';
				}
				else if (value.length === 16 && value.indexOf('5') === 0) {
					card = 'mc';
				}
				else if (value.length === 15 && value[0]+value[1] in {'34':'', '37':''}) {
					card = 'amex';
				}
				else if ((value.length === 13 || value.length === 16) && value.indexOf('4') === 0) {
					card = 'visa';
				}
				
				//if identification made, put icon in field
				$(this).removeClass('visa').removeClass('amex').removeClass('mc').removeClass('discover');
				if (card !== null) {
					$(this).addClass(card);
				}
			});
			
			var is_recurring = false;
			var recurring    = $('#giveback_recurring_field').parent();
			$('.toggler', form).click(function () {
				var newStatus = $(this).hasClass('true') ? false : true;
				
				is_recurring = newStatus;
				
				if ($.browser.webkit) {
					$(this).toggleClass('true');
					$('#' + $(this).attr('rel')).attr('checked', $(this).hasClass('true') ? 'checked' : '');
					setTimeout(function () {
						if (newStatus) {
							recurring.find('.hide_these').stop(true, true).slideDown(150);
						}
						else {
							recurring.find('.hide_these').stop(true, true).slideUp(150);
						}
					}, 150);
				}
				else {
					var that = $(this);
					var checkbox = $('#' + $(this).attr('rel'));
					if ($(this).hasClass('true')) {
						$(this).find('.slide').animate({
							'right': '0'
						}, 150, function () {
							that.toggleClass('true');
							checkbox.attr('checked', '');
							if (newStatus) {
								recurring.find('.hide_these').stop(true, true).slideDown(150);
							}
							else {
								recurring.find('.hide_these').stop(true, true).slideUp(150);
							}
						});
					}
					else {
						$(this).find('.slide').animate({
							'right': '-50'
						}, 150, function () {
							that.toggleClass('true');
							checkbox.attr('checked', 'checked');
							if (newStatus) {
								if ($.browser.webkit) {
									recurring.find('.hide_these').show();
								}
								else {
									recurring.find('.hide_these').stop(true, true).slideDown(150);
								}
							}
							else {
								if ($.browser.webkit) {
									recurring.find('.hide_these').hide();
								}
								else {
									recurring.find('.hide_these').stop(true, true).slideUp(150);
								}
							}
						});
					}
				}
			});
			$('label[rel=recurring_toggler]', form).click(function () {
				var id = $(this).attr('rel');
				$('#' + id).click();
			});
			$('#giveback_recurring_field', form).bind('focus', function () {
				alert($(this).val());
			});
			$('#giveback_recurring_date', form).date_selector();
			var closeOnBodyClick = null;
			closeOnBodyClick = function () {
				$('.jf_date_selector').remove();
				$('body').unbind('click', closeOnBodyClick);
			};
			$('#giveback_recurring_date', form).bind('focus', function () {
				$('body').unbind('click', closeOnBodyClick).bind('click', closeOnBodyClick);
			});
			
			form.find('input').blur(function () {
				$(this).removeClass('error');
			});
			
			form.find('input[type=button]').click(function () {
				form.get(0).reset();
			});
			
			form.submit(function () {
				form.find('input[type=submit], input[type=button]').attr('disable', 'disable');
				
				//get field values
				var firstName	= $('#giveback_firstName').val();
				var lastName	= $('#giveback_lastName').val();
				var email       = $('#giveback_email').size() ? $('#giveback_email').val() : '';
				var campus      = $('#giveback_campus').size() ? $('#giveback_campus').val() : '0';
				var comments    = $('#giveback_comments').size() ? $('#giveback_comments').val() : '';
				
				//get payment
				if (payment_type === 'credit') {
					var cc		= $('#giveback_cc').val();
					var code	= $('#giveback_code').val();
					var exp		= $('#giveback_exp').val();
					var zip		= $('#giveback_zip').val();
				}
				else {
					var routing	= $('#giveback_routing').val();
					var account = $('#giveback_account').val();
					var bank	= $('#giveback_bank').val();
					var type	= $('#giveback_account_type').val();
				}
				
				
				var amount		= $('#giveback_amount').val();
				
				//check amount
				amount = parseFloat(amount.replace(/[^0-9.]/gi, ''));
				if (amount <= 0) {
					amount = null;
				}
				
				//if is recurring
				if (is_recurring) {
					var frequency 	= $('#giveback_recurring_frequency').val();
					var start_date 	= $('#giveback_recurring_date').val();
					var num_gifts 	= $('#giveback_recurring_count').val();
				}
				
				//validate fields
				var invalid = false;
				var custom_fields = [];
				if ($('[name^=custom_field]').size() > 0) {
					var field = null;
					for (var i=0, len=$('[name^=custom_field]').size(); i<len; i++) {
						field = $('[name^=custom_field]').eq(i);
						if (field.get(0).nodeName == 'SELECT') {
							if (field.val() == '') {
								invalid = true;
								form.find('input[type=submit], input[type=button]').attr('disable', '');
							}
							else {
								custom_fields.push(field.val())
							}
						}
						else if (field.get(0).nodeName == 'INPUT' && field.attr('type') == 'radio') {
							if ($('[name='+field.attr('name')+']:checked').size() === 0) {
								invalid = true;
								form.find('input[type=submit], input[type=button]').attr('disable', '');
							}
							if (field.filter(':checked').size() === 1) {
								custom_fields.push(field.val())
							}
						}
					}
				}
				if (($('#giveback_campus').size() && $('#giveback_campus').val() != '' && $('#giveback_campus').val() >= 0) === false) {
					invalid = true;
					form.find('input[type=submit], input[type=button]').attr('disable', '');
					
					alert('Please select a campus.');
				}
				if ((payment_type === 'credit' ? (!firstName || !lastName || !cc || !code || !exp || !zip || !amount || ($('#giveback_email').size() && !email)) : (!firstName || !lastName || !routing || !account || !bank || !type || !amount || ($('#giveback_email').size() && !email)))) {
					invalid = true;
					form.find('input[type=submit], input[type=button]').attr('disable', '');
				}
				if (!firstName) {
					$('#giveback_firstName').addClass('error');
				}
				if (!lastName) {
					$('#giveback_lastName').addClass('error');
				}
				if ($('#giveback_email').size() && !email) {
					$('#giveback_email').addClass('error')
				}
				if (payment_type === 'credit') {
					if (!cc) {
						$('#giveback_cc').addClass('error');
					}
					if (!code) {
						$('#giveback_code').addClass('error');
					}
					if (!exp) {
						$('#giveback_exp').addClass('error');
					}
					if (!zip) {
						$('#giveback_zip').addClass('error');
					}
				}
				else {
					if (!routing) {
						$('#giveback_routing').addClass('error');
					}
					if (!account) {
						$('#giveback_account').addClass('error');
					}
					if (!bank) {
						$('#giveback_bank').addClass('error');
					}
					if (!type) {
						$('#giveback_account_type').addClass('error');
					}
				}
				if (!amount) {
					$('#giveback_amount').addClass('error');
				}
				if (is_recurring && (!start_date || !num_gifts)) {
					invalid = true;
					if (!start_date) {
						$('#giveback_recurring_date').addClass('error');
					}
					if (!num_gifts) {
						$('#giveback_recurring_count').addClass('error');
					}
				}
				
				//validate card
				if (cc) {
					cc = cc.replace(/ /g, '');
					var cardLength = cc.length;
					if (/[^\d ]/.test(cc) === true || (cardLength !== 16 && cardLength !== 13 && cardLength !== 15)) {
						$('#giveback_cc').addClass('error');
					}
				}
				
				if (invalid) {
					form.find('span.error').show();
					return false;
				}
				
				//process card
				var data = {
					'firstName': firstName,
					'lastName': lastName,
					'amount': amount,
					'payment_type': payment_type,
					'comments': comments
				};
				if ($('#giveback_page_id').size()) {
					data.page_id = $('#giveback_page_id').val();
				}
				if (email) {
					data.email			= email;
				}
				if (campus) {
					data.campus			= campus;
				}
				if (payment_type === 'credit') {
					data.cc 			= cc;
					data.code 			= code;
					data.exp			= exp;
					data.zip			= zip;
				}
				else {
					data.routing 		= routing;
					data.account 		= account;
					data.bank			= bank;
					data.type			= type;
					data.holder			= firstName+' '+lastName;
				}
				if (is_recurring) {
					data.frequency		= frequency;
					data.start_date		= start_date;
					data.num_gifts		= num_gifts;
				}
				data.custom_fields = custom_fields;
				$.ajax({
					'url': '/givingback/process',
					'data': data,
					'type': 'POST',
					'dataType': 'html',
					'success': function (data) {
						if (data) {
							if (form.hasClass('on_page') === false) {
								form.html('<span class="message">'+data+'</span> <input type="button" id="giveback_close" value="Close" />');
								form.find('#giveback_close').click(function () {
									$('form input[type=submit]').attr('disable', '');
									ccc.give.hide();
									return false;
								});
							}
							else {
								form.hide();
								var confirm_form = $('<form id="giveback" class="on_page"><span class="message">'+data+'</span> <input type="button" id="giveback_close" value="Close" /></form>');
								confirm_form.find('#giveback_close').click(function () {
									confirm_form.remove();
									form.get(0).reset();
									form.show();
									return false;
								});
								confirm_form.insertAfter(form);
							}
						}
						else {
							form.hide();
							var confirm_form = $('<form id="giveback" class="on_page"><span class="message">It appears that something went wrong, please contact us.</span> <input type="button" id="giveback_close" value="Close" /></form>');
							confirm_form.find('#giveback_close').click(function () {
								confirm_form.remove();
								form.get(0).reset();
								form.show();
								return false;
							});
							confirm_form.insertAfter(form);
						}
					}
				});
				
				return false;
			});
		},
		show: function () {
			if ($('#giveback').size() !== 0) return false;
			
			var form = $('<form />', {
				id: 'giveback'
			});
			
			//add header
			form.append('<h2>Giving Back to God</h2>');
			form.append('<span class="error">all fields are required</span>');
			form.find('span.error').hide();
			
			//user info
			var user_info = $('<fieldset />');
			user_info.append('<div class="label_set firstName"><label for="giveback_firstName">first name</label><input type="text" id="giveback_firstName" /></div>');
			user_info.append('<div class="label_set lastName"><label for="giveback_lastName">last name</label><input type="text" id="giveback_lastName" /></div>');
			user_info.appendTo(form);
			
			//segmented switch
			var segmented = $('<fieldset />');
			segmented.append('<div class="segmented"><a href="#" class="tab selected">credit</a> <a href="#" class="tab">echeck</a></div>');
			segmented.appendTo(form);
			
			//payment
			var payment = $('<div />', { id: 'giveback_payment' });
			payment.appendTo(form);
			
			//credit
			var credit = $('<fieldset />', { id: 'giveback_credit_payment' });
			credit.append('<div class="label_set cc"><label for="giveback_cc">card number</label><input type="text" autocomplete="off" id="giveback_cc" placeholder="1234567890123456" /></div>');
			credit.append('<div class="label_set code"><label for="giveback_code">CVS</label><input type="text" placeholder="123" autocomplete="off" id="giveback_code" /></div>');
			credit.append('<div class="label_set exp"><label for="giveback_exp">exp</label><input type="text" id="giveback_exp" placeholder="00/00" /></div>');
			credit.append('<div class="label_set zip"><label for="giveback_zip">zip code</label><input type="text" id="giveback_zip" placeholder="60540" /></div>');
			credit.appendTo(payment);
			
			//echeck
			var echeck = $('<fieldset />', { id: 'giveback_echeck_payment' });
			echeck.append('<div class="label_set routing"><label for="giveback_routing">routing number</label><input autocomplete="off" type="text" id="giveback_routing" value="" /></div>');
			echeck.append('<div class="label_set account"><label for="giveback_account">account number</label><input autocomplete="off" type="text" id="giveback_account" value="" /></div>');
			echeck.append('<div class="label_set bank"><label for="giveback_bank">bank name</label><input type="text" id="giveback_bank" value="" /></div>');
			echeck.append('<div class="label_set account_type"><label for="giveback_account_type">account type</label><select id="giveback_account_type"><option value="checking">checking</option><option value="savings">savings</option></select></div>');
			echeck.appendTo(payment);
			echeck.hide();
			
			//fill in
			var name = '';
			if (c) {
				if (c.chat && c.chat._user_name) {
					name = c.chat._user_name.split(' ');
					user_info.find('#giveback_firstName').val(name[0]);
					user_info.find('#giveback_lastName').val(name[1]);
				}
			}
			
			//amount
			var amount = $('<fieldset />');
			amount.append('<div class="label_set amount"><label for="giveback_amount">amount</label><input type="text" id="giveback_amount" value="$0.00" /></div>');
			amount.appendTo(form);
			
			//recurring
			var recurring = $('<fieldset />');
			recurring.append('<input type="checkbox" id="giveback_recurring_field" />');
			recurring.find('#giveback_recurring_field').hide();
			recurring.append('<div class="toggleset"><div id="recurring_toggler" rel="giveback_recurring_field" class="toggler' + ($('#giveback_recurring_field').attr('checked') ? ' true' : '') + '"><div class="bounce"><div class="slide"></div></div></div><label rel="recurring_toggler">Recurring?</label></div>');
			var hiddenFields = $('<div class="hide_these"></div>');
			hiddenFields.append('<div class="label_set recurring_frequency"><label for="giveback_recurring_frequency">frequency</label><select id="giveback_recurring_frequency"><option value="weekly">weekly</option><option value="monthly">monthly</option><option value="quarterly">quarterly</option><option value="semi-annually">semi-annually</option><option value="annually">annually</option></select></div>');
			hiddenFields.append('<div class="label_set recurring_date"><label for="giveback_recurring_date">start date</label><input type="text" id="giveback_recurring_date" placeholder="00/00/0000" /></div>');
			hiddenFields.append('<div class="label_set recurring_count"><label for="giveback_recurring_count"># of gifts</label><input type="text" id="giveback_recurring_count" /></div>');
			hiddenFields.append('<br />');
			recurring.append(hiddenFields);
			recurring.appendTo(form);
			form.find('.hide_these').hide();
			
			//process/submit button
			$('<input type="button" value="Cancel" />').click(function () {
				ccc.give.hide();
				return false;
			}).appendTo(form);
			form.append('<input type="submit" value="Process" />');
			
			if ($('#video_box').size()) {
				form.appendTo('#sidebar');
			}
			else {
				form.appendTo('body');
				form.css({
					'top': 20,
					'left': ($('body').width() - form.width()) / 2,
					'opacity': 0
				}).animate({
					'top': '70',
					'opacity': 1
				}, 350)
			}
			
			this.activate();
		}
	}
};

var footer = {
	_notesList: null,
	init: function (resizeContent) {
		if (resizeContent) {
			this.resizeContent();
		}
		
		window.onresize = function () {
			if ($('#footer_button_bible').data('bible_open') === false) {
				footer.resizeContent();
			}
		}
		
		$('#footer_button_bible').data('bible_open', false)
		var bible_button = $('#footer_button_bible a');
		bible_button.click(function () {
			footer.toggle_bible();
			return false;
		});
		
		var notes_button = $('#footer_button_notes a');
		notes_button.click(function () {
			var closeOnBodyClick = null;
			closeOnBodyClick = function () {
				$('#notesListMenu').remove();
				$('body').unbind('click', closeOnBodyClick);
			};
			$('body').bind('click', closeOnBodyClick);
			
			if ($('#notesListMenu').size()) {
				$('#notesListMenu').animate({
					'top': '+=10',
					'opacity': 0
				}, 150, function () {
					$('#notesListMenu').remove();
				});
				return false;
			}
			
			function showMenu() {
				if (footer._notesList.error) {
					location.href = notes_button.attr('href');
				}
				else {
					var list = footer._notesList.results;
					var ul   = $('<ul />', { id: 'notesListMenu' });
					for (var i=0, len=list.length; i<len; i++) {
						ul.append('<li><a href="'+list[i].link+'">'+list[i].title+'</a></li>');
					}
					ul.append('<li><a href="/notes" class="all">view all</a></li>');
					ul.appendTo('#footer');
					
					var center = $('#footer_button_logout').width() + (notes_button.parent().width() / 2);
					center    -= ul.width() / 2;
					var top    = ul.height() + -5;
					
					ul.css({
						'right': center + 80,
						'top': 0 - top,
						'opacity': 0
					}).animate({
						'top': '-=10',
						'opacity': 1
					}, 150);
				}
			}
			if (!footer._notesList) {
				$.ajax({
					'url': '/api/notes',
					'dataType': 'json',
					'success': function (data) {
						footer._notesList = data;
						showMenu();
					}
				});
			}
			else {
				showMenu();
			}
			
			return false;
		});
	},
	resizeContent: function () {
		$('#content').css('height', 'auto');
		//if ($('#content').height() + 58 + 30 + 20 > $(window).height()) {
			var contentHeight = $(window).height() - 58 - 30 - 20;
			$('#content').height(contentHeight);
		//}
	},
	toggle_bible: function () {
		if ($('#footer_button_bible').data('bible_open') === false) {
			this.show_bible();
			$('#footer_button_bible').data('bible_open', true);
			$('#footer').data('open', 'bible');
		}
		else {
			this.hide_bible();
			$('#footer_button_bible').data('bible_open', false);
			$('#footer').data('open', '');
		}
		return false;
	},
	show_bible: function () {
		if ($('#bible_reader').size() === 0) {
			var bible_reader = $('<iframe id="bible_reader" src="http://beta.communitychristian.org/bible/index.html"></iframe>');
			$('body').append(bible_reader);
		}
		else {
			var bible_reader = $('#bible_reader');
		}
		bible_reader.height(0).animate({
			height: 350
		});
		$('#content').animate({
			height: $(window).height() - 350 - 98
		});
		$('#footer').animate({
			bottom: 350
		});
	},
	hide_bible: function () {
		$('#bible_reader').animate({
			height: 0
		});
		$('#footer').animate({
			bottom: 0
		});
		$('#content').animate({
			height: $(window).height() - 90
		});
	}
};

var c = null;

$(function () {
	ccc.init();
	if (is_mobile) {
		$('#footer').hide();
	}
	else {
		if (location.href.indexOf('watch') != -1) {
			footer.init(true);
		}
		else {
			footer.init(false);
		}
	}
	/*$('#nav_give').click(function () {
		ccc.give.show();
		return false;
	});*/
	
	if ($('#giveback').size()) {
		ccc.give.activate();
	}
	
	ccc.notifications.init();
});