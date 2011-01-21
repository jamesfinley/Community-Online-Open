var weekDays     = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	months       = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	daysInMonths = [];

(function ($) {
	var daysInAMonth = function (month, year) {
		month = month || (new Date()).getMonth(),
		year  = year || (new Date()).getFullYear();
		
		return 32 - new Date(year, month, 32).getDate();
	};
	
	var build_calendar = function (month, year, callback, date, calendar) {
		month           = month !== undefined ? month : (new Date()).getMonth(),
		year            = year !== undefined ? year : (new Date()).getFullYear();
		date            = date || null,
		firstDayOfMonth = (new Date(year, month, 1)).getDay(),
		daysInMonth     = daysInAMonth(month, year);
		weeksInMonth    = Math.ceil((firstDayOfMonth + daysInMonth) / 7);
		
		var table,
			tr,
			td,
			a,
			week       = 0,
			daysInWeek = 7,
			day        = 0;
		
		if (calendar) {
			table = calendar;
			table.find('tr.week').remove();
			
			table.find('tr.header .current').text(months[month] + ' ' + year);
			
			table.find('.previous').html('').append($('<a />', {
				href: 'javascript:void(0)',
				click: function () {
					build_calendar(month - 1 >= 0 ? month - 1 : 11, month - 1 >= 0 ? year : year - 1, callback, null, table);
				},
				html: '&laquo;'
			}));
			table.find('.next').html('').append($('<a />', {
				href: 'javascript:void(0)',
				click: function () {
					build_calendar(month + 1 < 12 ? month + 1 : 0, month + 1 < 12 ? year : year + 1, callback, null, table);
				},
				html: '&raquo;'
			}));
		}
		else {
			table = $('<table />', {
				className: 'jf_date_selector',
				cellpadding: 0,
				cellspacing: 0
			});
			tr = $('<tr />', {
				className: 'header'
			});
			tr.appendTo(table);
			tr.append($('<td />', {
				className: 'previous'
			}));
			tr.append($('<td />', {
				colspan: 5,
				className: 'current',
				text: months[month] + ' ' + year
			}));
			tr.append($('<td />', {
				className: 'next'
			}));
			tr.find('.previous').append($('<a />', {
				href: 'javascript:void(0)',
				click: function () {
					build_calendar(month - 1 >= 0 ? month - 1 : 11, month - 1 >= 0 ? year : year - 1, callback, null, table);
				},
				html: '&laquo;'
			}));
			tr.find('.next').append($('<a />', {
				href: 'javascript:void(0)',
				click: function () {
					build_calendar(month + 1 < 12 ? month + 1 : 0, month + 1 < 12 ? year : year + 1, callback, null, table);
				},
				html: '&raquo;'
			}));
			tr = null;
		}
		
		//create weeks and days
		while (weeksInMonth--) {
			tr = $('<tr />', {
				className: 'week'
			});
			tr.appendTo(table);
			
			while (daysInWeek--) {
				if (week === 0 && (6 - daysInWeek) < firstDayOfMonth) {
					
				}
				else {
					day++;
				}
				
				if (day && day - 1 < daysInMonth) {
					a = $('<a />', {
						data: {
							'month': month,
							'date': day,
							'year': year,
							'day': 6 - daysInWeek
						},
						href: 'javascript:void(0)',
						click: function () {
							var date = ($(this).data('month') + 1) + '/' + $(this).data('date') + '/' + $(this).data('year');
							
							callback(date, $(this).parent());
						},
						text: day
					});
				}
				
				td = $('<td />', {
					className: date && day === date ? 'today day' : 'day',
					html: a ? a : null
				});
				td.addClass(weekDays[6 - daysInWeek]);
				td.appendTo(tr);
				
				td = a = null;
			}
			
			tr = null;
			daysInWeek = 7;
			week++;
		}
		
		return table;
	};
	$.fn.date_selector = function (o) {
		var s = $.extend({format: 'month-day-year', delimiter: '/', leadingZeros: true, restrictDay: []}, o);
		
		$(this).filter('input').each(function () {
			var input = $(this),
				unique_id = (new Date()).getTime(),
				calendar,
				kill_calendar = function () {
					calendar.remove();
				},
				create_date = function (date) {
					var newDate = date.split('/');
					
					//add leading zeros
					if (s.leadingZeros) {
						newDate[0] = parseInt(newDate[0]) < 10 ? "0"+newDate[0] : newDate[0];
						newDate[1] = parseInt(newDate[1]) < 10 ? "0"+newDate[1] : newDate[1];
						newDate[2] = parseInt(newDate[2]) < 10 ? "0"+newDate[2] : newDate[2];
					}
					
					if (s.format === 'day-month-year') {
						var month      = newDate[0],
							day        = newDate[1];
							newDate[0] = day,
							newDate[1] = month;
					}
					else if (s.format === 'year-month-day') {
						var month      = newDate[0],
							day        = newDate[1],
							year       = newDate[2];
							newDate[0] = year,
							newDate[1] = month,
							newDate[2] = day;
					}
					else if (s.format === 'year-day-month') {
						var month      = newDate[0],
							day        = newDate[1],
							year       = newDate[2];
							newDate[0] = year,
							newDate[1] = day,
							newDate[2] = month;
					}
					
					newDate = newDate.join(s.delimiter);
					
					return newDate;
				};
			
			var show_calendar = function () {
				if ($('table.jf_date_selector[rel='+unique_id+']').size() === 0) {
					var d     = new Date(),
						day   = d.getDay(),
						month = d.getMonth(),
						date  = d.getDate(),
						year  = d.getFullYear();
					
					var val   = $(this).val() ? $(this).val().split('/') : null;
					if (val) {
						switch (s.format) {
							case 'day-month-year':
								month = parseInt(val[1], 10) - 1,
								date  = parseInt(val[0], 10),
								year  = parseInt(val[2], 10);
								break;
							case 'year-month-day':
								month = parseInt(val[1], 10) - 1,
								date  = parseInt(val[2], 10),
								year  = parseInt(val[0], 10);
								break;
							case 'year-day-month':
								month = parseInt(val[2], 10) - 1,
								date  = parseInt(val[1], 10),
								year  = parseInt(val[0], 10);
								break;
							default:
								month = parseInt(val[0], 10) - 1,
								date  = parseInt(val[1], 10),
								year  = parseInt(val[2], 10);
								break;
						}
						d     = new Date(year, month, date),
						day   = d.getDay();
					}
					
					calendar = build_calendar(month, year, function (date, td) {
						var allowAction = true;
						if (s.restrictDay && s.restrictDay.length) {
							allowAction = false;
							for (var i=0, len=s.restrictDay.length; i<len; i++) {
								if (td.hasClass(s.restrictDay[i])) {
									allowAction = true;
								}
							}
						}
						if (allowAction === false) {
							alert('You must select a '+s.restrictDay.join(', ')+'.');
							return false;
						}
						
						input.val(create_date(date));
						kill_calendar();
					}, date);
					calendar.find('td.day').each(function () {
						if (s.restrictDay && s.restrictDay.length) {
							var isDisabled = true;
							for (var i=0, len=s.restrictDay.length; i<len; i++) {
								if ($(this).hasClass(s.restrictDay[i])) {
									isDisabled = false;
								}
							}
							if (isDisabled) {
								$(this).find('a').css({
									opacity: .5
								});
							}
						}
					});
					calendar.attr('rel', unique_id);
					calendar.insertAfter(input).css({
						top: 0 - input.outerHeight() - 2,
						left: input.outerWidth() + 5
					});
				}
			};
			$(this).bind('focus', show_calendar)/*.bind('blur', function () {
				setTimeout(function () {
					kill_calendar();
				}, 500);
			})*/;
		});
	};
})(jQuery);