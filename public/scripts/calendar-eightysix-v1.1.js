/**
 * Calendar Eightysix - MooTools datepicker and calendar class
 * @version 1.1
 *
 * by dev.base86.com
 *
 * Source and documentation available at:
 * http://dev.base86.com/scripts/mootools_javascript_datepicker_calendar_eightysix.html
 *
 * Calendar Eightysix is an unobtrusive developer friendly javascript calendar and datepicker 
 * offering a better user experience for date related functionalities.
 *
 * --
 *
 * This Calendar Eightysix script is licensed under the Creative Commons Attribution-NonCommercial 3.0 License:
 * http://creativecommons.org/licenses/by-nc/3.0/
 * 
 * Calendar Eightysix requires a purchased commercial license when used commercially:
 * http://dev.base86.com/scripts/mootools_javascript_datepicker_calendar_eightysix.html#license
 */

var CalendarEightysix = new Class({
	Implements: [Events, Options],
	
	options: {
		'slideDuration': 500,
		'fadeDuration': 200,
		'toggleDuration': 200,
		'fadeTransition': Fx.Transitions.linear,
		'slideTransition': Fx.Transitions.Quart.easeOut,
		
		'prefill': true,
		'defaultDate': null,
		'linkWithInput': true,
		'keyNavigation': false,
		
		'theme': 'default',
		'defaultView': 'month',
		'startMonday': false,
		'alwaysShow': false,
		'injectInsideTarget': false,
		'format': '%m/%d/%Y',
		'alignX': 'right',
		'alignY': 'ceiling',
		'offsetX': 0,
		'offsetY': 0,
		
		'draggable': false,
		'pickable': true,
		'toggler': null,
		'disallowUserInput': false,
		
		'minDate': null,
		'maxDate': null,
		'excludedWeekdays': null,
		'excludedDates': null,
		
		'createHiddenInput': false,
		'hiddenInputName': 'date',
		'hiddenInputFormat': '%t'
	},
	
	initialize: function(target, options) {
		this.setOptions(options);
		
		this.target = $(target);
		this.transitioning = false;
		this.today = new Date().clearTime();
		
		//Extend Date with unix timestamp parser
		Date.defineParser({
		    re: /^[0-9]{10}$/,
		    handler: function(bits) { return new Date.parse('Jan 01 1970').set('seconds', bits[0]); }
		});
		//Extend Date with a workaround for the '-' delimiter parse bug
		Date.defineParser({
		    re: /\-/,
		    handler: function(bits) { return new Date.parse(bits.input.replace(/\-/, '.')); }
		});
		
		//Create the currect selected date
		if($defined(this.options.defaultDate)) 
			this.selectedDate = new Date().parse(this.options.defaultDate).clearTime();
		else if(this.options.linkWithInput && $chk(this.target.get('value'))) 
			this.selectedDate = new Date().parse(this.target.get('value')).clearTime();
		if(!$defined(this.selectedDate) || !this.selectedDate.isValid()) 
			this.selectedDate = this.today.clone();
		
		//Create the HTML base of the calender
		var innerHtml = '<div class="c86-wrapper"><div class="c86-header"><div class="c86-arrow-left"></div><div class="c86-arrow-right"></div><div class="c86-label c86-clickable"></div></div>'+
							'<div class="c86-body"><div class="c86-inner"><div class="c86-container a"></div><div class="c86-container b"></div></div></div><div class="c86-footer"></div></div>';
		this.element = new Element('div', { 'class': 'calendar-eightysix', 'html': innerHtml, 'style': 'display: '+ (this.options.alwaysShow ? 'block' : 'none') }).addClass(this.options.theme);
		
		//Add the calender to the document and position it
		if(this.options.injectInsideTarget) this.element.injectBottom(this.target);
		else {
			this.element.injectBottom($(document.body));
			this.position();
			window.addEvent('resize', this.position.bind(this));
		}
		
		//Assign the containers
		this.currentContainer = this.element.getElement('.c86-container.a').setStyle('z-index', 999);
		this.tempContainer = this.element.getElement('.c86-container.b').setStyle('z-index', 998);
		
		//Assign the interface elements and events
		this.header = this.element.getElement('.c86-header');
		this.label = this.header.getElement('.c86-label');
		this.arrowLeft = this.header.getElement('.c86-arrow-left');
		this.arrowRight = this.header.getElement('.c86-arrow-right');
		
		this.label.addEvent('click', this.levelUp.bind(this));
		this.arrowLeft.addEvent('click', this.slideLeft.bind(this));
		this.arrowRight.addEvent('click', this.slideRight.bind(this));
		
		//Create dates ranges
		if($defined(this.options.minDate)) {
			this.options.minDate = Date.parse(this.options.minDate).clearTime();
			if(!this.options.minDate.isValid()) this.options.minDate = null;
		}
		if($defined(this.options.maxDate)) {
			this.options.maxDate = Date.parse(this.options.maxDate).clearTime();
			if(!this.options.maxDate.isValid()) this.options.maxDate = null;
		}
		
		//Parse excluded dates
		if($defined(this.options.excludedDates)) {
			var excludedDates = [];
			this.options.excludedDates.each(function(date) {
				excludedDates.include(this.format(new Date().parse(date).clearTime(), '%t'));
			}.bind(this));
			this.options.excludedDates = excludedDates;
		}
		
		//Make the calendar draggable?
		if(this.options.draggable && !this.options.injectInsideTarget) {
			this.header.addClass('c86-dragger');
			this.label.setStyle('width', 'auto');
			new Drag(this.element, { 'handle': this.header });
		}
		
		//Create hidden input
		if(this.options.createHiddenInput) {
			this.hiddenInput = new Element('input', { 'type': 'hidden', 'name': this.options.hiddenInputName }).injectAfter(this.target);
		}
		
		//Link with the input element
		if(!this.options.disallowUserInput && this.options.linkWithInput && this.target.get('tag') == 'input') {
			this.target.addEvent('keyup', function() {
				this.setDate(this.target.get('value'), false);
			}.bind(this));
		}
		//Or toggler...
		if($defined(this.options.toggler)) this.options.toggler = $(this.options.toggler);
		
		//Add show and hide events
		($defined(this.options.toggler) ? this.options.toggler : this.target).addEvents({
			'focus': this.show.bind(this), 'click': this.show.bind(this)
		});
		
		if(!this.options.alwaysShow) document.addEvent('mousedown', this.outsideClick.bind(this));
		MooTools.lang.addEvent('langChange', function() { this.render(); this.pick(); }.bind(this));
		
		//Other events
		if(this.target.get('tag') == 'input') {
			this.target.addEvent('keydown', this.onKeyDown.bind(this));
			if(this.options.disallowUserInput) this.target.addEvent('contextmenu', ($lambda(false)));
		}
		
		//See if the date is correct
		this.selectedDate = this.correctDate(this.selectedDate);
		
		//Prefill the current date
		if(this.options.prefill) this.pick();
		
		//Render the default view
		this.viewDate = this.selectedDate.clone().set('date', 1).clearTime();
		this.view = this.options.defaultView;
		this.render();
	},
	
	render: function() {
		this.currentContainer.empty();
		
		switch(this.view) {
			case 'decade': this.renderDecade(); break;
			case 'year': this.renderYear(); break;
			default: this.renderMonth();
		}
		
		return this;
	},
	
	/* Rendering */
	
	renderMonth: function() {
		this.view = 'month';
		this.currentContainer.empty().addClass('c86-month');
		if(this.options.pickable) this.currentContainer.addClass('c86-pickable');
		
		var lang = MooTools.lang.get('Date'), weekdaysCount = this.viewDate.format('%w') - (this.options.startMonday ? 1 : 0);
		if(weekdaysCount == -1) weekdaysCount = 6;
		
		//Label
		this.label.set('html', lang.months[this.viewDate.get('month')] +' '+ this.viewDate.format('%Y'));
		
		//Day label row
		var row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
		for(var i = (this.options.startMonday ? 1 : 0); i < (this.options.startMonday ? 8 : 7); i++) {
			var day = new Element('div', { 'html': lang.days[this.options.startMonday && i == 7 ? 0 : i] }).injectBottom(row);
			day.set('html', day.get('html').substr(0, 2));
		}
		
		//Add days for the beginning non-month days
		row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
		y = this.viewDate.clone().decrement('month').getLastDayOfMonth();
		for(var i = 0; i < weekdaysCount; i++) {
			this.injectDay(row, this.viewDate.clone().decrement('month').set('date', y - (weekdaysCount - i) + 1), true);
		}
		
		//Add month days
		for(var i = 1; i <= this.viewDate.getLastDayOfMonth(); i++) {
			this.injectDay(row, this.viewDate.clone().set('date', i));
			if(row.getChildren().length == 7) {
				row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
			}
		}
		
		//Add outside days
		var y = 8 - row.getChildren().length, startDate = this.viewDate.clone().increment('month').set('date', 1);
		for(var i = 1; i < y; i++) {
			this.injectDay(row, startDate.clone().set('date', i), true);
		}
		
		//Always have six rows
		for(var y = this.currentContainer.getElements('.c86-row').length; y < 7; y++) {
			row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
			for(var z = 0; z < 7; z++) {
				this.injectDay(row, startDate.clone().set('date', i), true);
				i++;
			}
		}
		
		this.renderAfter();
	},
	
	//Used by renderMonth
	injectDay: function(row, date, outside) {
		var day = new Element('div', { 'html': date.get('date') }).injectBottom(row);
		day.store('date', date);
		if(outside) day.addClass('c86-outside');
		
		if(($defined(this.options.minDate) && this.format(this.options.minDate, '%t') > this.format(date, '%t')) || 
		   ($defined(this.options.maxDate) && this.format(this.options.maxDate, '%t') < this.format(date, '%t')) ||
		   ($defined(this.options.excludedWeekdays) && this.options.excludedWeekdays.contains(date.format('%w').toInt())) ||
		   ($defined(this.options.excludedDates) && this.options.excludedDates.contains(this.format(date, '%t'))))
			day.addClass('c86-non-selectable');
		else if(this.options.pickable) day.addEvent('click', this.pick.bind(this));
		
		if(date.format('%x') == this.today.format('%x')) day.addClass('c86-today');
		if(date.format('%x') == this.selectedDate.format('%x')) day.addClass('c86-selected');
	},
	
	renderYear: function() {
		this.view = 'year';
		this.currentContainer.addClass('c86-year-decade');
		var lang = MooTools.lang.get('Date').months;
		
		//Label
		this.label.set('html', this.viewDate.format('%Y'));
		
		var row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
		for(var i = 1; i < 13; i++) {
			var month = new Element('div', { 'html': lang[i - 1] }).injectBottom(row);
			month.set('html', month.get('html').substr(0, 3)); //Setting and getting the innerHTML takes care of html entity problems (e.g. [M&a]uml;r => [Mär]z)
			var iMonth = this.viewDate.clone().set('month', i - 1);
			month.store('date', iMonth);
			
			if(($defined(this.options.minDate) && this.format(this.options.minDate.clone().set('date', 1), '%t') > this.format(iMonth, '%t')) ||
			   ($defined(this.options.maxDate) && this.format(this.options.maxDate.clone().set('date', 1), '%t') < this.format(iMonth, '%t')))
				month.addClass('c86-non-selectable');
			else month.addEvent('click', this.levelDown.bind(this));
			
			if(i - 1 == this.today.get('month') && this.viewDate.get('year') == this.today.get('year')) month.addClass('c86-today');
			if(i - 1 == this.selectedDate.get('month') && this.viewDate.get('year') == this.selectedDate.get('year')) month.addClass('c86-selected');
			if(!(i % 4) && i != 12) row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
		}
		
		this.renderAfter();
	},
	
	renderDecade: function() {
		this.label.removeClass('c86-clickable');
		this.view = 'decade';
		this.currentContainer.addClass('c86-year-decade');
		
		var viewYear, startYear;
		viewYear = startYear = this.viewDate.format('%Y').toInt();
		while(startYear % 12) startYear--;
		
		//Label
		this.label.set('html', startYear +' &#150; '+ (startYear + 11));
		
		var row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
		for(var i = startYear; i < startYear + 12; i++) {
			var year = new Element('div', { 'html': i }).injectBottom(row);
			var iYear = this.viewDate.clone().set('year', i);
			year.store('date', iYear);
			
			if(($defined(this.options.minDate) && this.options.minDate.get('year') > i) ||
			   ($defined(this.options.maxDate) && this.options.maxDate.get('year') < i)) year.addClass('c86-non-selectable');
			else year.addEvent('click', this.levelDown.bind(this));
			
			if(i == this.today.get('year')) year.addClass('c86-today');
			if(i == this.selectedDate.get('year')) year.addClass('c86-selected');
			if(!((i + 1) % 4) && i != startYear + 11) row = new Element('div', { 'class': 'c86-row' }).injectBottom(this.currentContainer);
		}
		
		this.renderAfter();
	},
	
	renderAfter: function() {
		//Iterate rows and add classes and remove navigation if nessesary
		var rows = this.currentContainer.getElements('.c86-row');
		
		for(var i = 0; i < rows.length; i++) {
			rows[i].set('class', 'c86-row '+ ['a', 'b', 'c', 'd', 'e', 'f', 'g'][i] +' '+ (i % 2 ? 'c86-even' : 'c86-odd')).getFirst().addClass('c86-first');
			rows[i].getLast().addClass('c86-last');
			
			if( (this.view == 'month' && i == 1 && $defined(this.options.minDate) && this.options.minDate.diff(this.viewDate) <= 0) ||
				(this.view != 'month' && i == 0 && $defined(this.options.minDate) && this.format(this.options.minDate, '%t') >= this.format(rows[i].getFirst().retrieve('date'), '%t')) )
				this.arrowLeft.setStyle('visibility', 'hidden');
			
			if(i == rows.length - 1 && $defined(this.options.maxDate)) {
				if((this.view == 'month' && this.options.maxDate.diff(this.viewDate.clone().increment('month').decrement()) >= 0) ||
				   (this.view == 'year' &&  this.format(this.options.maxDate, '%t') <= this.format(rows[i].getLast().retrieve('date').clone().increment('month'), '%t')) ||
				   (this.view == 'decade' && this.format(this.options.maxDate, '%t') <= this.format(rows[i].getLast().retrieve('date').clone().increment('year'), '%t')))
					this.arrowRight.setStyle('visibility', 'hidden');
			}
		};
		
		if($defined(this.$events['render'+ this.view])) {
			var elements = this.currentContainer.getElements('div[class~=c86-row] > div');
			if(this.view == 'month') {
				for(var i = 0; i < 7; i++) elements[i] = null;
				elements = elements.clean();
			}
			this.fireEvent('render'+ this.view, { 'view': this.view, 'elements': elements });
		}
	},
	
	/* Animations */
	
	slideLeft: function() {
		this.switchContainers();
		
		//Render new view
		switch(this.view) {
			case 'month':  this.viewDate.decrement('month'); break;
			case 'year':   this.viewDate.decrement('year'); break;
			case 'decade': this.viewDate.set('year', this.viewDate.get('year') - 12); break;
		}
		this.render();
		
		//Tween the new view in and old view out
		this.currentContainer.set('tween', { 'duration': this.options.slideDuration, 'transition': this.options.slideTransition }).tween('left', [-this.currentContainer.getWidth(), 0]);
		this.tempContainer.set('tween', { 'duration': this.options.slideDuration, 'transition': this.options.slideTransition }).tween('left', [0, this.tempContainer.getWidth()]);
	},
	
	slideRight: function() {
		this.switchContainers();
		
		//Render new view
		switch(this.view) {
			case 'month': this.viewDate.increment('month'); break;
			case 'year': this.viewDate.increment('year'); break;
			case 'decade': this.viewDate.set('year', this.viewDate.get('year') + 12); break;
		}
		this.render();
		
		//Tween the new view in and old view out
		this.currentContainer.set('tween', { 'duration': this.options.slideDuration, 'transition': this.options.slideTransition }).tween('left', [this.currentContainer.getWidth(), 0]);
		this.tempContainer.set('tween', { 'duration': this.options.slideDuration, 'transition': this.options.slideTransition }).tween('left', [0, -this.currentContainer.getWidth()]);
	},
	
	levelDown: function(e) {
		if(this.transitioning) return;
		this.switchContainers();
		this.viewDate = $(e.target).retrieve('date');
		
		//Render new view
		switch(this.view) {
			case 'year': this.renderMonth(); break;
			case 'decade': this.renderYear(); break;
		}
		
		//Tween the new view in and old view out
		this.transitioning = true;
		this.currentContainer.set('tween', { 'duration': this.options.fadeDuration, 'transition': this.options.fadeTransition, 
											 'onComplete': function() { this.transitioning = false }.bind(this) }).setStyles({'opacity': 0, 'left': 0}).fade('in');
		this.tempContainer.set('tween', { 'duration': this.options.fadeDuration, 'transition': this.options.fadeTransition }).fade('out');
	},
	
	levelUp: function() {
		if(this.view == 'decade' || this.transitioning) return;
		this.switchContainers();
		
		//Set viewdates and render
		switch(this.view) {
			case 'month': this.renderYear(); break;
			case 'year':  this.renderDecade(); break;
		}
		
		//Tween the new view in and old view out
		this.transitioning = true;
		this.currentContainer.set('tween', { 'duration': this.options.fadeDuration, 'transition': this.options.fadeTransition, 
											 'onComplete': function() { this.transitioning = false }.bind(this) }).setStyles({'opacity': 0, 'left': 0}).fade('in');
		this.tempContainer.set('tween', { 'duration': this.options.fadeDuration, 'transition': this.options.fadeTransition }).fade('out');
	},
	
	switchContainers: function() {
		this.currentContainer = this.currentContainer.hasClass('a') ? this.element.getElement('.c86-container.b') : this.element.getElement('.c86-container.a');
		this.tempContainer = this.tempContainer.hasClass('a') ? this.element.getElement('.c86-container.b') : this.element.getElement('.c86-container.a');
		this.currentContainer.empty().removeClass('c86-month').removeClass('c86-year-decade').setStyles({ 'opacity': 1, 'display': 'block', 'z-index': 999 });
		this.tempContainer.setStyle('z-index', 998);
		
		this.label.addClass('c86-clickable');
		this.arrowLeft.setStyle('visibility', 'visible');
		this.arrowRight.setStyle('visibility', 'visible');
	},
	
	/* Positioning and visibility */
	
	position: function() {
		var top, left;
		var coordinates = this.target.getCoordinates();
		
		switch(this.options.alignX) {
			case 'left':
				left = coordinates.left;
				break;
			case 'middle':
				left = coordinates.left + (coordinates.width / 2) - (this.element.getWidth() / 2);
				break;
			case 'right': default:
				left = coordinates.left + coordinates.width;
		}
		
		switch(this.options.alignY) {
			case 'bottom':
				top = coordinates.top + coordinates.height;
				break;
			case 'top':
				top = coordinates.top - this.element.getHeight();
				break;
			case 'ceiling': default:
				top = coordinates.top;
		}
		
		left += this.options.offsetX.toInt();
		top += this.options.offsetY.toInt();
		
		this.element.setStyles({ 'top': top, 'left': left });
	},
	
	show: function() {
		if(!this.visible & !this.options.alwaysShow) {
			this.visible = true;
			if(!Browser.Engine.trident) {
				this.element.setStyles({ 'opacity': 0, 'display': 'block' });
				if(!this.options.injectInsideTarget) this.position();
				this.element.set('tween', { 'duration': this.options.toggleDuration, 'transition': this.options.fadeTransition }).fade('in');
			}  else {
				this.element.setStyles({ 'opacity': 1, 'display': 'block' });
				if(!this.options.injectInsideTarget) this.position();
			}
		}
	},
	
	hide: function() {
		if(this.visible & !this.options.alwaysShow) {
			this.visible = false;
			if(!Browser.Engine.trident) {
				this.element.set('tween', { 'duration': this.options.toggleDuration, 'transition': this.options.fadeTransition,
											'onComplete': function() { this.element.setStyle('display', 'none') }.bind(this) }).fade('out');
			} else this.element.setStyle('display', 'none');
		}
	},
	
	toggle: function() {
		if(this.visible) this.hide();
		else this.show();
	},
	
	outsideClick: function(e) {
		if(this.visible) {
			var elementCoords = this.element.getCoordinates();
			var targetCoords  = this.target.getCoordinates();
			if(((e.page.x < elementCoords.left || e.page.x > (elementCoords.left + elementCoords.width)) ||
			    (e.page.y < elementCoords.top || e.page.y > (elementCoords.top + elementCoords.height))) &&
			   ((e.page.x < targetCoords.left || e.page.x > (targetCoords.left + targetCoords.width)) ||
			    (e.page.y < targetCoords.top || e.page.y > (targetCoords.top + targetCoords.height))) ) this.hide();
		}
	},
	
	/* Formating and picking */
	
	pick: function(e) {
		if($defined(e)) {
			this.selectedDate = $(e.target).retrieve('date');
			this.element.getElements('.c86-selected').removeClass('c86-selected');
			$(e.target).addClass('c86-selected');
			
			(this.hide.bind(this)).delay(150);
			
			//If outside day, set viewdate and render
			if($(e.target).hasClass('c86-outside')) {
				if(this.options.alwaysShow) {
					if(this.selectedDate.diff(this.viewDate) > 0) this.slideLeft();
					else this.slideRight();
				} else {
					this.viewDate = this.selectedDate.clone().set('date', 1);
					(this.render.bind(this)).delay(300);
				}
			}
		}
		
		var value = this.format(this.selectedDate);
		
		if(!this.options.injectInsideTarget) {
			switch(this.target.get('tag')) {
				case 'input': this.target.set('value', value); break;
				default: this.target.set('html', value);
			}
		}
		
		if($defined(this.hiddenInput)) this.hiddenInput.set('value', this.format(this.selectedDate, this.options.hiddenInputFormat));
		this.fireEvent('change', this.selectedDate);
		
		return this;
	},
	
	//Extended format parser
	format: function(date, format) {
		if(!$defined(format)) format = this.options.format;
		if(!$defined(date)) return;
		format = format.replace(/%([a-z%])/gi,
			function($1, $2) {
				switch($2) {
					case 'D': return date.get('date');
					case 'n': return date.get('mo') + 1;
					case 't': return (date.getTime() / 1000).toInt();
				}
				return '%'+ $2;
			}
		);
		return date.format(format);
	},
	
	onKeyDown: function(e) {
		if(this.options.keyNavigation) {
			var newDate = this.selectedDate.clone();
			var right = true;
			var pressed = false;
			
			switch(e.code) {
				//Left
				case 37: newDate.decrement(); pressed = true; right = false; break;
				//Right
				case 39: newDate.increment(); pressed = true; break;
				//Up
				case 38: newDate.decrement('month'); pressed = true; right = false; break;
				//Down
				case 40: newDate.increment('month'); pressed = true; break;
			}
			
			if(pressed) {
				//Correct it
				newDate = this.correctDate(newDate, right);
				
				//If different, set it
				if(this.selectedDate.diff(newDate) != 0) this.setDate(newDate);
			}
		}
		if(this.options.disallowUserInput) return false;
	},
	
	//Checks if the date is an excluded date, excluded weekday or isn't within the range
	//If so it returns a correct pickable date
	correctDate: function(date, right) {
		if(!$defined(right)) right = true;
		
		//Check if the date is lower than the minimal date
		if($defined(this.options.minDate) && date.diff(this.options.minDate) > 0) date = this.options.minDate.clone();
		//Check if the date is higher than the maximum date
		else if($defined(this.options.maxDate) && date.diff(this.options.maxDate) < 0) date = this.options.maxDate.clone();
		
		//Check if the currect picked weekday is allowed
		var i = 0;
		while( ($defined(this.options.excludedWeekdays) && this.options.excludedWeekdays.contains(date.format('%w').toInt())) ||
			   ( ($defined(this.options.minDate) && date.diff(this.options.minDate) > 0) ||
				 ($defined(this.options.maxDate) && date.diff(this.options.maxDate) < 0) ) ||
			   ($defined(this.options.excludedDates) && this.options.excludedDates.contains(this.format(date, '%t'))) ) {
			if(i == 31) right = !right; //Reverse
			else if(i == 62) {
				date = this.options.minDate.clone();
				break; //Stop a possible infinitive loop
			}
			
			if(right) date.increment();
			else date.decrement();
			i++;
		}
		
		return date;
	},
	
	setDate: function(value, pick) {
		if(!$defined(pick)) pick = true;
		
		if($type(value) == 'date') {
			var date = value.clearTime();
		} else {
			var date = $chk(value) ? new Date().parse(this.target.get('value')).clearTime() : new Date().clearTime();
		}
		
		if(date.isValid()) {
			this.selectedDate = this.correctDate(date);
			this.viewDate = this.selectedDate.clone().set('date', 1);
			this.render();
			if(pick) this.pick();
		}
		
		return this;
	},
	
	getDate: function() {
		return this.selectedDate;
	}
});