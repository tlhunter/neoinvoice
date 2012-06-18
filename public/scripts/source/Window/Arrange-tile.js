/*

Script: Arrange-tile.js
	Cascade windows.
	
Copyright:
	Copyright (c) 2007-2009 Greg Houston, <http://greghoustondesign.com/>.	

Authors:
	Harry Roberts and Greg Houston

License:
	MIT-style license.	

Requires:
	Core.js, Window.js

Syntax:
	(start code)
	MUI.arrangeTile();
	(end)

*/

MUI.files[MUI.path.source + 'Window/Arrange-tile.js'] = 'loaded';
 
MUI.extend({
	arrangeTile: function(){

		var	viewportTopOffset = 30;    // Use a negative number if neccessary to place first window where you want it
		var viewportLeftOffset = 20;

		var x = 10;
		var y = 80;
	
		var instances =  MUI.Windows.instances;

		var windowsNum = 0;

		instances.each(function(instance){
			if (!instance.isMinimized && !instance.isMaximized){
				windowsNum++;
			}
		});

		var cols = 3;
		var rows = Math.ceil(windowsNum / cols);
		
		var coordinates = document.getCoordinates();
	
		var col_width = ((coordinates.width - viewportLeftOffset) / cols);
		var col_height = ((coordinates.height - viewportTopOffset) / rows);
		
		var row = 0;
		var col = 0;
		
		instances.each(function(instance){
			if (!instance.isMinimized && !instance.isMaximized && instance.options.draggable ){
				
				var content = instance.contentWrapperEl;
				var content_coords = content.getCoordinates();
				var window_coords = instance.windowEl.getCoordinates();
				
				// Calculate the amount of padding around the content window
				var padding_top = content_coords.top - window_coords.top;
				var padding_bottom = window_coords.height - content_coords.height - padding_top;
				var padding_left = content_coords.left - window_coords.left;
				var padding_right = window_coords.width - content_coords.width - padding_left;

				/*

				// This resizes the windows
				if (instance.options.shape != 'gauge' && instance.options.resizable == true){
					var width = (col_width - 3 - padding_left - padding_right);
					var height = (col_height - 3 - padding_top - padding_bottom);

					if (width > instance.options.resizeLimit.x[0] && width < instance.options.resizeLimit.x[1]){
						content.setStyle('width', width);
					}
					if (height > instance.options.resizeLimit.y[0] && height < instance.options.resizeLimit.y[1]){
						content.setStyle('height', height);
					}

				}*/

				var left = (x + (col * col_width));
				var top = (y + (row * col_height));

				instance.drawWindow();
				
				MUI.focusWindow(instance.windowEl);
				
				if (MUI.options.advancedEffects == false){
					instance.windowEl.setStyles({
						'top': top,
						'left': left
					});
				}
				else {
					var tileMorph = new Fx.Morph(instance.windowEl, {
						'duration': 550
					});
					tileMorph.start({
						'top': top,
						'left': left
					});
				}				

				if (++col === cols) {
					row++;
					col = 0;
				}
			}
		}.bind(this));
	}
});
