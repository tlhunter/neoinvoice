/*

Script: Themes.js
	Allows for switching themes dynamically.

Copyright:
	Copyright (c) 2007-2009 Greg Houston, <http://greghoustondesign.com/>.	

License:
	MIT-style license.

Requires:
	Core.js
	
Notes:
	Themes are new and experimental.	
	
Syntax:
	(start code)
	new MUI.Themes.init(newTheme);
	(end)
	
Example:
	(start code)
	new MUI.Themes.init('charcoal');
	(end)		

Arguments:
	newTheme - (string) The theme name	

*/

MUI.files[MUI.path.source + 'Utilities/Themes.js'] = 1;
	
MUI.Themes = {

	/*
	
	Function: themeInit
		Initialize a theme. This is experimental and not fully implemented yet.
		
	*/	
	init: function(newTheme){
		this.newTheme = newTheme.toLowerCase();
		if (!this.newTheme || this.newTheme == null || this.newTheme == MUI.options.theme.toLowerCase()) return;

		if ($('spinner')) $('spinner').show();
		
		this.oldURIs = [];
		this.oldSheets = [];
		
		$$('link').each( function(link){			
				var href = link.get('href');
				if (href.contains(MUI.path.themes + MUI.options.theme)){
					this.oldURIs.push(href);
					this.oldSheets.push(link);
				}
		}.bind(this));		
		
		/*
		MUI.files.each( function(value, key, hash){			
			if (key.contains(MUI.path.themes + MUI.options.theme)){
				this.oldURIs.push(key);
			}
		}.bind(this));
		*/		
		
		this.newSheetURLs = this.oldURIs.map(function(item, index){
    		return item.replace("/" + MUI.options.theme + "/", "/" + MUI.Themes.newTheme + "/");
		}.bind(this));
			
		this.sheetsToLoad = this.oldURIs.length;
		this.sheetsLoaded = 0;
		
		// Download new stylesheets and add them to an array
		this.newSheets = [];
		this.newSheetURLs.each( function(link){
			var href = link;
								
				//var id = link.id;
				
				var cssRequest = new Request({
					method: 'get',
					url: href,
					onComplete: function(response) { 
						var newSheet = new Element('link', {
							//'id': id,
							'rel': 'stylesheet',
							'media': 'screen',
							'type': 'text/css',
							'href': href
						});
						this.newSheets.push(newSheet);										
					}.bind(this),
					onFailure: function(response){
						this.themeLoadSuccess = false;
						if ($('spinner')) $('spinner').hide();						
						MUI.notification('Stylesheets did not load.');						
					},					
					onSuccess: function(){						
						this.sheetsLoaded++;
						if (this.sheetsLoaded == this.sheetsToLoad) {
							this.updateThemeStylesheets();
							this.themeLoadSuccess = true;
						}  
					}.bind(this)
				});
				cssRequest.send();				

		}.bind(this));
								
	},
	updateThemeStylesheets: function(){

		this.oldSheets.each( function(sheet){
			sheet.destroy();
		});		

		this.newSheets.each( function(sheet){
			MUI.files[sheet.get('href')] = 1;
			sheet.inject(document.head);
		});		

		// Delay gives the stylesheets time to take effect. IE6 needs more delay.	
		if (Browser.Engine.trident){
			this.redraw.delay(1250, this);
		}
		else {
			this.redraw.delay(250, this);
		}	
	
	},	
	redraw: function(){

		$$('.replaced').removeClass('replaced');

		// Redraw open windows		
		$$('.mocha').each( function(element){			
			var instance = element.retrieve('instance');
			
			// Convert CSS colors to Canvas colors.
			instance.setColors();							
			instance.drawWindow();			
		});
		
		if (MUI.Dock){
			if (MUI.Dock.options.useControls){
				MUI.Dock.setDockColors();
				MUI.Dock.renderDockControls();
			}
		}

		// Reformat layout
		if (MUI.Desktop.desktop){
			var checker = (function(){
				// Make sure the style sheets are really ready.				
				if (MUI.Desktop.desktop.getStyle('overflow') != 'hidden'){					
					return;
				}
				$clear(checker);								
				MUI.Desktop.setDesktopSize();				
			}).periodical(50);
		}
		
		if ($('spinner')) $('spinner').hide();		
		MUI.options.theme = this.newTheme;
		
		/*		
		this.cookie = new Hash.Cookie('mochaUIthemeCookie', {duration: 3600});
		this.cookie.empty();
		this.cookie.set('theme', MUI.options.theme);
		this.cookie.save();
		*/			
						
	}
};

window.addEvent('load', function(){
	/*
	// Load theme the user was last using. This needs work.
	var cookie = new Hash.Cookie('mochaUIthemeCookie', {duration: 3600});
	var themeCookie = cookie.load();
	if(cookie.getKeys().length){	
		if (themeCookie.get('theme') != MUI.Themes.options.theme){
			MUI.Themes.init.delay(1000, MUI.Themes, themeCookie.get('theme'));
		}
	}
	*/
		
	if ($('themeControl')){
		$('themeControl').getElements('option').setProperty('selected', 'false');
		if ($('chooseTheme')){
			$('chooseTheme').setProperty('selected', 'true');
		}
	}
});
