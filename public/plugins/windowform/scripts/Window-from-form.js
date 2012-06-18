/*

Script: Window-from-form.js
	Create a window from a form.
	
Copyright:
	Copyright (c) 2007-2008 Greg Houston, <http://greghoustondesign.com/>.	

License:
	MIT-style license.
	
Requires:
	Core.js, Window.js
	
See Also:
	<Window>	

*/

MochaUI.WindowForm = new Class({
	options: {
		id: null,
		title: 'New Window',
		loadMethod: 'html', 
		content: '',
		contentURL: 'pages/lipsum.html',
		type: 'window',
		width: 300,
		height: 125,
		scrollbars: true,
		x: null,
		y: null
	},
	initialize: function(options){
		this.setOptions(options);
		this.options.id = 'win' + (++MochaUI.Windows.windowIDCount);
		this.options.title = $('newWindowHeaderTitle').value;
		if ($('htmlLoadMethod').checked){
			this.options.loadMethod = 'html';
		}
		if ($('iframeLoadMethod').checked){
			this.options.loadMethod = 'iframe';
		}
		this.options.content = $('newWindowContent').value;
		
		// Remove eval(), javascript:, and script from User Provided Markup		
		this.options.content = this.options.content.replace(/\<(.*)script(.*)\<\/(.*)script(.*)\>/g, ""); 
    	this.options.content = this.options.content.replace(/[\"\'][\s]*javascript:(.*)[\"\']/g, "\"\"");    
    	this.options.content = this.options.content.replace(/eval\((.*)\)/g, "");		
		
		if ($('newWindowContentURL').value){
			this.options.contentURL = $('newWindowContentURL').value;
		}		
		if ($('newWindowModal').checked) {
			this.options.type = 'modal';
		}
		this.options.width = $('newWindowWidth').value.toInt();
		this.options.height = $('newWindowHeight').value.toInt();	
		this.options.x = $('newWindowX').value.toInt();
		this.options.y = $('newWindowY').value.toInt();
		new MochaUI.Window(this.options);
	}
});
MochaUI.WindowForm.implement(new Options);
