/*

Script: Arrange-cascade.js
	Cascade windows.

Copyright:
	Copyright (c) 2007-2009 Greg Houston, <http://greghoustondesign.com/>.	

License:
	MIT-style license.	

Requires:
	Core.js, Window.js

Syntax:
	(start code)
	MUI.arrangeCascade();
	(end)

*/

MUI.files[MUI.path.source + 'Window/Arrange-cascade.js'] = 'loaded';

MUI.extend({   
	arrangeCascade: function(){

		var	viewportTopOffset = 30;    // Use a negative number if neccessary to place first window where you want it
	var viewportLeftOffset = 20;
	var windowTopOffset = 50;    // Initial vertical spacing of each window
	var windowLeftOffset = 40; 

		// See how much space we have to work with
		var coordinates = document.getCoordinates();
		
		var openWindows = 0;
		MUI.Windows.instances.each(function(instance){
			if (!instance.isMinimized && instance.options.draggable) openWindows ++; 
		});
		
		if ((windowTopOffset * (openWindows + 1)) >= (coordinates.height - viewportTopOffset)) {
			var topOffset = (coordinates.height - viewportTopOffset) / (openWindows + 1);
		}
		else {
			var topOffset = windowTopOffset;
		}
		
		if ((windowLeftOffset * (openWindows + 1)) >= (coordinates.width - viewportLeftOffset - 20)) {
			var leftOffset = (coordinates.width - viewportLeftOffset - 20) / (openWindows + 1);
		}
		else {
			var leftOffset = windowLeftOffset;
		}

		var x = viewportLeftOffset;
		var y = viewportTopOffset;
		$$('.mocha').each(function(windowEl){
			var instance = windowEl.retrieve('instance');
			if (!instance.isMinimized && !instance.isMaximized && instance.options.draggable){
				id = windowEl.id;
				MUI.focusWindow(windowEl);
				x += leftOffset;
				y += topOffset;

				if (MUI.options.advancedEffects == false){
					windowEl.setStyles({
						'top': y,
						'left': x
					});
				}
				else {
					var cascadeMorph = new Fx.Morph(windowEl, {
						'duration': 550
					});
					cascadeMorph.start({
						'top': y,
						'left': x
					});
				}
			}
		}.bind(this));
	}
});
