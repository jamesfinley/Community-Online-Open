var bible = {
	_books: [
		{'osis': 'Gen', 'book': 'Genesis', 'chapters': 50},
		{'osis': 'Exod', 'book': 'Exodus', 'chapters': 40},
		{'osis': 'Lev', 'book': 'Leviticus', 'chapters': 27},
		{'osis': 'Num', 'book': 'Numbers', 'chapters': 36},
		{'osis': 'Deut', 'book': 'Deuteronomy', 'chapters': 34},
		{'osis': 'Josh', 'book': 'Joshua', 'chapters': 24},
		{'osis': 'Judg', 'book': 'Judges', 'chapters': 21},
		{'osis': 'Ruth', 'book': 'Ruth', 'chapters': 4},
		{'osis': '1Sam', 'book': '1 Samuel', 'chapters': 31},
		{'osis': '2Sam', 'book': '2 Samuel', 'chapters': 24},
		{'osis': '1Kngs', 'book': '1 Kings', 'chapters': 22},
		{'osis': '2Kngs', 'book': '2 Kings', 'chapters': 25},
		{'osis': '1Chr', 'book': '1 Chronicles', 'chapters': 29},
		{'osis': '2Chr', 'book': '2 Chronicles', 'chapters': 36},
		{'osis': 'Ezra', 'book': 'Ezra', 'chapters': 10},
		{'osis': 'Neh', 'book': 'Nehemiah', 'chapters': 13},
		{'osis': 'Esth', 'book': 'Ester', 'chapters': 10},
		{'osis': 'Job', 'book': 'Job', 'chapters': 42},
		{'osis': 'Ps', 'book': 'Psalm', 'chapters': 150},
		{'osis': 'Prov', 'book': 'Proverbs', 'chapters': 31},
		{'osis': 'Eccl', 'book': 'Ecclesiastes', 'chapters': 12},
		{'osis': 'Song', 'book': 'Song of Solomon', 'chapters': 8},
		{'osis': 'Isa', 'book': 'Isaiah', 'chapters': 66},
		{'osis': 'Jer', 'book': 'Jeremiah', 'chapters': 52},
		{'osis': 'Lam', 'book': 'Lamentations', 'chapters': 5},
		{'osis': 'Ezek', 'book': 'Ezekiel', 'chapters': 48},
		{'osis': 'Dan', 'book': 'Daniel', 'chapters': 12},
		{'osis': 'Hos', 'book': 'Hosea', 'chapters': 14},
		{'osis': 'Joel', 'book': 'Joel', 'chapters': 3},
		{'osis': 'Amos', 'book': 'Amos', 'chapters': 9},
		{'osis': 'Obad', 'book': 'Obadiah', 'chapters': 1},
		{'osis': 'Jonah', 'book': 'Jonah', 'chapters': 4},
		{'osis': 'Mic', 'book': 'Micah', 'chapters': 7},
		{'osis': 'Nah', 'book': 'Nahum', 'chapters': 3},
		{'osis': 'Hab', 'book': 'Habakkuk', 'chapters': 3},
		{'osis': 'Zeph', 'book': 'Zephaniah', 'chapters': 3},
		{'osis': 'Hag', 'book': 'Haggai', 'chapters': 2},
		{'osis': 'Zech', 'book': 'Zechariah', 'chapters': 14},
		{'osis': 'Mal', 'book': 'Malachi', 'chapters': 4},
		{'osis': 'Matt', 'book': 'Matthew', 'chapters': 28},
		{'osis': 'Mark', 'book': 'Mark', 'chapters': 16},
		{'osis': 'Luke', 'book': 'Luke', 'chapters': 24},
		{'osis': 'John', 'book': 'John', 'chapters': 21},
		{'osis': 'Acts', 'book': 'Acts', 'chapters': 28},
		{'osis': 'Rom', 'book': 'Romans', 'chapters': 16},
		{'osis': '1Cor', 'book': '1 Corinthians', 'chapters': 16},
		{'osis': '2Cor', 'book': '2 Corinthians', 'chapters': 13},
		{'osis': 'Gal', 'book': 'Galatians', 'chapters': 6},
		{'osis': 'Eph', 'book': 'Ephesians', 'chapters': 6},
		{'osis': 'Phil', 'book': 'Philippians', 'chapters': 4},
		{'osis': 'Col', 'book': 'Colossians', 'chapters': 4},
		{'osis': '1Thess', 'book': '1 Thessalonians', 'chapters': 5},
		{'osis': '2Thess', 'book': '2 Thessalonians', 'chapters': 3},
		{'osis': '1Tim', 'book': '1 Timothy', 'chapters': 6},
		{'osis': '2Tim', 'book': '2 Timothy', 'chapters': 4},
		{'osis': 'Titus', 'book': 'Titus', 'chapters': 3},
		{'osis': 'Phlm', 'book': 'Philemon', 'chapters': 1},
		{'osis': 'Heb', 'book': 'Hebrews', 'chapters': 13},
		{'osis': 'Jas', 'book': 'James', 'chapters': 5},
		{'osis': '1Pet', 'book': '1 Peter', 'chapters': 5},
		{'osis': '2Pet', 'book': '2 Peter', 'chapters': 3},
		{'osis': '1John', 'book': '1 John', 'chapters': 5},
		{'osis': '2John', 'book': '2 John', 'chapters': 1},
		{'osis': '3John', 'book': '3 John', 'chapters': 1},
		{'osis': 'Jude', 'book': 'Jude', 'chapters': 1},
		{'osis': 'Rev', 'book': 'Revelation', 'chapters': 22}
	],
	book: function (book) {
		for (var i=0, len=this._books.length; i<len; i++) {
			if (this._books[i].book === book) {
				return [
					this._books[i],
					i > 0 ? this._books[i - 1] : null,
					this._books[i + 1] ? this._books[i + 1] : null
				];
			}
		}
		for (var i=0, len=this._books.length; i<len; i++) {
			if (this._books[i].osis === book) {
				return [
					this._books[i],
					i > 0 ? this._books[i - 1] : null,
					this._books[i + 1] ? this._books[i + 1] : null
				];
			}
		}
		return false;
	},
	check_hash: function () {
		//check location hash
		var hash    = location.hash.replace('#', '').replace(/%20/g, ' ').split('/');
		if (hash.length === 2) {
			var book    = hash[0];
			var chapter = parseInt(hash[1]);
			
			if (!(book === this._current_book && chapter === this._current_chapter)) {
				this.load_chapter(this.book(book)[0].osis, chapter);
			}
		}
	},
	init: function () {
		//check location hash
		var hash    = location.hash.replace('#', '').replace(/%20/g, ' ').split('/');
		if (hash.length === 2) {
			var book    = hash[0];
			var chapter = parseInt(hash[1]);
		}
		else {
			var book    = 'Gen';
			var chapter = 1;
		}
		
		var resize = function () {
			$('#bible').css('min-height', $(window).height() - 150);
		};
		resize();
		$(window).bind('resize', resize);
		
		var list = $('<ul id="book_menu"></ul>');
		for (var i=0, len=bible._books.length; i<len; i++) {
			$('<li><a href="#" rel="'+bible._books[i].osis+'">'+bible._books[i].book+'</a></li>').find('a').click(function () {
				var osis = $(this).attr('rel');
				bible.load_chapter(osis, 1);
				return false;
			}).parent().appendTo(list);
			//list.append('<li><a href="javascript:bible.load_chapter(\''+bible._books[i].osis+'\',1)">'+bible._books[i].book+'</a></li>');
		}
		$('#navigation').append(list);
		list.hide();
		$('#navigation .book_name').click(function () {
			list.toggle();
		});
		list.find('a').click(function () {
			list.hide();
		});
		
		this.load_chapter(book, chapter);
		
		setInterval(function () {
			bible.check_hash();
		}, 1000);
	},
	load_chapter: function (book, chapter) {
		this._current_book    = book;
		this._current_chapter = chapter;
		
		location.href='#'+this.book(book)[0].osis+'/'+chapter;
		
		$('#navigation .book_name').html('loading...');
		
		$('title').html('Loading '+this.book(book)[0].book+' '+chapter+' (ESV)');
		
		$.get(/*'http://pixelfaith.com/bible/*/'bible.php?passage='+book+'+'+chapter, function (data) {
			$('#navigation .book_name').html(bible.book(book)[0].book+' '+chapter);
			$('title').html(bible.book(book)[0].book+' '+chapter+' (ESV)');
			
			$('body .footnotes').remove();
			$('#bible').html(data);
			
			/* wrap verses in .verse span */
			var verses = document.querySelector('#bible .esv .esv-text'),
				len    = verses.childNodes.length,
				paragraph,
				newparagraph,
				verse;
			
			while (len--) {
				if (verses.childNodes[len].nodeType == 1 && verses.childNodes[len].nodeName.toLowerCase() == 'p') {
					paragraph = verses.childNodes[len];
					newparagraph = document.createElement('p');
					if (paragraph.className) {
						newparagraph.setAttribute('class', paragraph.className);
					}
					newparagraph.setAttribute('id', paragraph.getAttribute('id'));
					while (node = paragraph.firstChild) {
						if (node.nodeType == 1 && node.nodeName.toLowerCase() == 'span' && (node.getAttribute('class') == 'chapter-num' || node.getAttribute('class') == 'verse-num')) {
							verse = document.createElement('span');
							verse.setAttribute('class', 'verse');
							newparagraph.appendChild(verse);
						}
						if (verse && node) {
							verse.appendChild(node);
						}
					}
					paragraph.parentNode.replaceChild(newparagraph, paragraph);
				}
			}
			/* end wrap */
			
			var footnotes = $('#bible .footnotes');
			footnotes.insertBefore('body .copyright');
			
			/* code created by Weston Ruter */
			/* http://gist.github.com/258017 */
			var footnotes = document.querySelector('.footnotes > p');
			var li,ul = document.createElement('ul');
			
			//Now iterate over the footnotes.childNodes and construct lis out of them
			var node;
			while (node = footnotes.firstChild) {
				if (node.nodeType == 1/*Node.ELEMENT_NODE*/) {
					if (/(^|\s)footnote($|\s)/.test(node.className)) {
						li = document.createElement('li');
						ul.appendChild(li);
			
						//Move the ID from the link to the li
						var link = node.getElementsByTagName('a')[0];
						var id = link.id;
						link.removeAttribute('id');
						li.id = id;
					}
					//Ignore and remove all br elements
					else if (node.localName.toLowerCase() == 'br') {
						footnotes.removeChild(node);
						continue;
					}
				}
				if (li) {
					li.appendChild(node);
				}
			}
			
			footnotes.parentNode.replaceChild(ul, footnotes);
			/* end http://gist.github.com/258017 */
		}, 'html');
		
		var this_book = this.book(this._current_book);
		if (this_book[1] === null) {
			$('#previous_book').css('opacity', .5);
			if (this._current_chapter === 1) {
				$('#previous_chapter').css('opacity', .5);
			}
			else {
				$('#previous_chapter').css('opacity', 1);
			}
		}
		else {
			$('#previous_book').css('opacity', 1);
			$('#previous_chapter').css('opacity', 1);
		}
		if (this_book[2] === null) {
			$('#next_book').css('opacity', .5);
		}
		else {
			$('#next_book').css('opacity', 1);
		}
	},
	next_chapter: function () {
		var book = this.book(this._current_book);
		if (this._current_chapter + 1 > book[0].chapters) {
			if (book[2] !== null) {
				this.load_chapter(book[2].osis, 1);
			}
			else {
				return false;
			}
		}
		else {
			this.load_chapter(this._current_book, this._current_chapter + 1);
		}
	},
	next_book: function () {
		var book = this.book(this._current_book);
		if (book[2] !== null) {
			this.load_chapter(book[2].osis, 1);
		}
		else {
			return false;
		}
	},
	previous_chapter: function () {
		var book = this.book(this._current_book);
		if (this._current_chapter - 1 > 0) {
			this.load_chapter(this._current_book, this._current_chapter - 1);
		}
		else if (book[1] !== null) {
			this.load_chapter(book[1].osis, book[1].chapters);
		}
		else {
			return false;
		}
	},
	previous_book: function () {
		var book = this.book(this._current_book);
		if (book[1] !== null) {
			this.load_chapter(book[1].osis, 1);
		}
		else {
			return false;
		}
	},
	show_search: function () {
		if ($('#search_field').size()) {
			$('#search_field').parent().remove();
			return;
		}
		
		var form = $('<form id="search"></form>');
		form.append('<input type="text" id="search_field" />');
		form.append('<ul id="results"></ul>');
		form.find('#results').hide();
		var input = form.find('input');
		$('#navigation').append(form);
		input.focus();
		
		input.keyup(function () {
			var value = $(this).val().toLowerCase();
			var list  = [];
			if (value != '') {
				for (var i=0, len=bible._books.length; i<len; i++) {
					if (bible._books[i].book.toLowerCase().indexOf(value) === 0) {
						list.push(bible._books[i].book);
					}
				}
			}
			if (list.length) {
				form.find('#results').html('');
				for (var i=0, len=list.length; i<len; i++) {
					$('<li><a href="#">'+list[i]+'</a></li>').find('a').click(function () {
						bible.search_select_book($(this).text());
						return false;
					}).parent().appendTo(form.find('#results'));
					//form.find('#results').append('<li><a href="javascript:bible.search_select_book(\''+list[i]+'\')">'+list[i]+'</a></li>');
				}
				form.find('#results').show();
			}
			else {
				form.find('#results').hide();
			}
		});
		
		form.submit(function () {
			var value   = input.val();
			var matches = value.match(/(([0-9]{1} )?[a-zA-Z ]*) ([0-9]*)/);
			if (matches) {
				var book    = matches[1] ? matches[1] : 'Genesis';
				var chapter = matches[3] ? matches[3] : 1;
				
				if (!book || !chapter) {
					return false;
				}
				if (bible.book(book) === false) {
					return false;
				}
				if (bible.book(book)[0].chapters < parseInt(chapter)) {
					return false;
				}
				
				bible.load_chapter(bible.book(book)[0].osis, parseInt(chapter));
				$('#search_field').parent().remove();
			}
			
			return false;
		});
	},
	search_select_book: function (book) {
		$('#navigation #search_field').val(book+' ').focus();
		$('#navigation #results').remove();
		return false;
	}
};
$(function () {
	bible.init();
});