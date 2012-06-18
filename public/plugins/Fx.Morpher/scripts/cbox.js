/**
 * Fx.Morpher - Extended Fx.Morph plugin
 *
 * Dependencies: MooTools 1.2 [Core]
 *
 * @version			0.5 (20081024)
 *
 * @license			MIT-style license
 * @author			Ivanicus <ivannpaz [at] gmail.com>
 * @copyright		Author???
 * @documentation	{coming soon}
 *
 * Big thanks for your help:
 *
 * > Rajeev J Sebastian
 * > johnwait
 */
 

/*
Hash: Fx.RegPoint
	Common Regpoints 'Constant' used
*/
Fx.RegPoint = {
	'TopLeft':		{x:0, 	y:0},
	'Top': 			{x:0.5, y:0},
	'TopRight':		{x:1, 	y:0},
	'Left':			{x:0, 	y:0.5},
	'Center': 		{x:0.5, y:0.5},
	'Right': 		{x:1, 	y:0.5},
	'BottomLeft': 	{x:0, 	y:1},
	'Bottom': 		{x:0.5, y:1},
	'BottomRight': 	{x:1, 	y:1}
};

/*
Class: Fx.Morpher
	Fx.Morph extended

Arguments:
	Same as Fx.Morph with the addition of extra options:
	- path: pathClass
	- usepath: array of properties that will use the path ['top', 'left']
	- regpoint: A Fx.RegPoint.* value, defines the registration point.
	* more to come
*/
Fx.Morpher = new Class({

	Extends: Fx.CSS,

	options: {
		path: null,
		regpoint: Fx.RegPoint.TopLeft,
		usepath: ['top', 'left']
	},
	
	//directly copied from mootools core (Fx.Morph)
	initialize: function(element, options){
		this.element = this.subject = $(element);
		this.parent(options);
	},

	//directly copied from mootools core (Fx.Morph)
	set: function(now){
		if (typeof now == 'string') now = this.search(now);
		for (var p in now) this.render(this.element, p, now[p], this.options.unit);
		return this;
	},
	
	getRegistration: function(val, axis){
		//var prop = (axis == 'x' ? 'width' : 'height');
		//parseInt(this.element.getStyle('left'))
	},
	
	//translates value using RegPoint
	translate: function(value, axis, from){
		//[0, 0.5, 1]
		//var value 	= ((typeof val) == 'object') ? (val[0]).value : val;
		//var parser 	= ((typeof val) == 'object') ? (val[0]).parser : parser;
		//console.log(value);
		var prop = (axis == 'x' ? 'width' : 'height');
		var result = parseInt((value - (parseInt(this.element.getStyle(prop)) * this.options.regpoint[axis])));

		var computed = [{value: result, parser: from[0].parser}];
		computed.$family = {name: 'fx:css:value'};
		return computed;
	},

	//called at intervals to compute the current css values
	compute: function(from, to, delta){
		var now = {};
		var x, y;
		
		//check if we have a motion path
		var path = (this.options.path ? this.options.path : false);
		
		//if(!DEBUGGED) console.dir(from);
		
		for (var p in from)
		{
			//Only calc coords if there's a path
			//and only do it for the axis that can be morphes [x || y]
			if(path && this.options.usepath.contains(p)) {
				var coords = path.getCoordinates(1 - delta);
				//TODO: make it work with usepath[] and avoid that if/then crap...
				if(p == 'left') x = coords ? coords.x : null;
				if(p == 'top') y = coords ? coords.y : null;
			}
			
			switch(p) {
				case 'left':
						now[p] = (path ?  this.translate(x, 'x', from[p]) : this.parent(from[p], to[p], delta));
						break;
				
				case 'top':
						now[p] = (path ? this.translate(y, 'y', from[p]) : this.parent(from[p], to[p], delta));
						break;
						
				case 'width':
						now[p] = this.parent(from[p], to[p], delta);
						//if we do not calc left motion, then do it here
						/*
						if(!$defined(from['left'])) {
							var regx = this.element.retrieve('regx', parseInt(this.element.getStyle('left'))); 
							now['left'] = this.translate(regx, 'x', from[p]);
						}
						*/
						break;
				
				case 'height':
						now[p] = this.parent(from[p], to[p], delta);
						/*
						if(!$defined(from['top'])) {
							var regy = this.element.retrieve('regy', parseInt(this.element.getStyle('top')));
							now['top'] = this.translate(regy, 'y', from[p]);
						}
						break;
						*/
				default:
					//standard mootools calculation
					now[p] = this.parent(from[p], to[p], delta);
			}
		}
		
		//stop debugging
		//if(!DEBUGGED) DEBUGGED = true;
		
		return now;
	},

	//Modded from mootools core (Fx.Morph)
	//TODO: cleanup, optimization and some rewrite
	start: function(properties, paused){
		//quick check to allow start with no args and motionpath
		if(!properties) properties = {};
		
		if (!this.check(arguments.callee, properties)) return this;
		if (typeof properties == 'string') properties = this.search(properties);
		
		//add a dummy 'top', 'left', 'whatever' in order to trigger compute
		var usepath = this.options.usepath;
		if(this.options.path && usepath.length) {
			//TODO: make it work with usepath[] and avoid that if/then crap...
			//add dummy values to trigger compute
			if(usepath.contains('top')) $extend(properties, {top:[0,1]}); 
			if(usepath.contains('left')) $extend(properties, {left:[0,1]});
		}
		
		var from = {}, to = {};
		for (var p in properties){
			var parsed = this.prepare(this.element, p, properties[p]);
			from[p] = parsed.from;
			to[p] = parsed.to;
		}
		return this.parent(from, to);
	}

});

Element.Properties.morpher = {

	set: function(options){
		var morph = this.retrieve('morpher');
		if (morph) morph.cancel();
		return this.eliminate('morpher').store('morpher:options', $extend({link: 'cancel'}, options));
	},

	get: function(options){
		if (options || !this.retrieve('morpher')){
			if (options || !this.retrieve('morpher:options')) this.set('morpher', options);
			this.store('morpher', new Fx.Morpher(this, this.retrieve('morpher:options')));
		}
		return this.retrieve('morpher');
	}

};

Element.implement({

	morpher: function(props, paused){
		this.get('morpher').start(props, paused);
		return this;
	}

});


/**
 * Fx.Path - Pathing for Mootools
 *
 * Dependencies: MooTools 1.2 [Core]
 *
 * @version			0.5 (20081024)
 *
 * @license			MIT-style license
 * @author			Ivanicus <ivannpaz [at] gmail.com>
 * @copyright		Author???
 * @documentation	{coming soon}
 *
 * Bezier calculation and basic ideas:
 *
 * > Jonas Raoni Soares Silva (http://jsfromhell.com/math/bezier [rev. #1])
 * > Dan Pupius (http://www.pupius.net/)
 * 
 * Big thanks for your help:
 *
 * > Guillermo Rauch: http://devthought.com/
 * > Nathan White: http://www.nwhite.net/
 * > Tom Occhino: http://tomocchino.com/
 */
 

/*
Class: Fx.BasePath
	Blueprint for Pathing Classes

Notes:
	Not meant to be used on its own
	It doesnt do anything right now, may be removed later.
*/
Fx.BasePath = new Class({
				 
		Implements: Options,
		
		options: {},
		
		initialize: function(options){
			this.setOptions(options);
		}

});

/*
Class: Fx.Path
	Describes a Poly-Path (Path with multiple segments)

Arguments:
	segments 	- Array of Segment Classes (see below)
	options 	- Options for the class (see below)

*/
Fx.Path = new Class({
					   
	Implements: Options,
	
	Extends: Fx.BasePath,
	
	options: {
		axis: 'top',  		// [bottom|top]
		origin: {x:0, y:0}	// added to calculations [to use relative values]
	},
	
	initialize: function(segments, options)
	{
		var params = Array.link(arguments, {segments:Array.type, options:Object.type});
		this.parent(params.options || {});
		this._segments = params.segments || [];			
		this._blocksize	= segments ? (1 / this._segments.length) : 0;
		this.setOrigin(this.options.origin);
		
	},
	
	//adds a segment at the end of this collection
	addSegment: function(segment)
	{
		segment.origin = this.options.origin;
		this._segments.push(segment);
		this._blocksize	= (1 / this._segments.length);
	},
	
	//sets the origin of the whole bezier object
	setOrigin: function(origin)
	{
		//store locally
		this.setOptions({origin:origin});
		
		//replicate to children
		this._segments.each(function(el){
			el.offset = origin;
		});
	},

	//p[1 -> 0] ret[x]
	x: function(p)
	{
		//find the segment where this 'time' falls
		var seg = this.getSegment(p);
		
		var mapped = (((p % this._blocksize) * 100) / this._blocksize) / 100;

		return seg.x(mapped);
	},
	
	//p[1 -> 0] ret[y]
	y: function(p)
	{
		//find the segment where this 'time' falls
		var seg = this.getSegment(p);
		
		var mapped = (((p % this._blocksize) * 100) / this._blocksize) / 100;
		
		return seg.y(mapped);
	},

	//p[0 -> 1] ret[{x,y}]
	getCoordinates: function(p)
	{
		//find the segment where this 'time' falls
		var seg = this.getSegment(p);
		
		var mapped = (((p % this._blocksize) * 100) / this._blocksize) / 100;
		
		return seg ? seg.getCoordinates(mapped) : null;
	},
	
	//p[0 -> 1] r[0-#elements]
	getSegment: function(p)
	{
		var n = this._segments.length;

		var index = (n - Math.floor(p * n)) - 1 ;
		
		return this._segments[index];
	},
			
	//plot poly-path
	plot: function(cb)
	{
		this._segments.each(function(el){
			el.plot(cb);
		}, this);
	}
});

/*
Class: CubicBezier
	Describes a Cubic Bezier Curve.

Arguments:
	p0, p1: Start/End Vertex Points
	c0, c1: Start/End Control Points

*/
var CubicBezier = new Class({

	initialize: function(p0, p1, c0, c1)
	{
		this.x0 	= p0[0]; // A x1
		this.y0 	= p0[1];
		this.x1 	= p1[0]; // D x4
		this.y1 	= p1[1];
		this.cx0 	= c0[0]; // B x2
		this.cy0 	= c0[1];
		this.cx1 	= c1[0]; // C x3
		this.cy1 	= c1[1];
		
		this.offset = {x:0, y:0};
		
		this.f1 = function(t) { return (t*t*t); }
		this.f2 = function(t) { return (3*t*t*(1-t)); } 
		this.f3 = function(t) { return (3*t*(1-t)*(1-t)); }
		this.f4 = function(t) { return ((1-t)*(1-t)*(1-t)); }

	},
	
	//p[1 -> 0] ret[x]
	x: function(p)
	{
		return this.x0 * this.f1(p) + this.cx0 * this.f2(p) + this.cx1 * this.f3(p) + this.x1 * this.f4(p);
	},

	//p[1 -> 0] ret[y]
	y: function(p)
	{
		return this.y0 * this.f1(p) + this.cy0 * this.f2(p) + this.cy1 * this.f3(p) + this.y1 * this.f4(p);
	},
	
	//p[0 -> 1] ret[{x,y}]
	getCoordinates: function(p)
	{
		return { x: this.x(p) + this.offset.x, y: this.y(p) + this.offset.y };
	},
	
	//returns the controls points of this curve
	getControls: function()
	{
		return {c0: {x:this.cx0, y:this.cy0}, c1: {x:this.cx1, y:this.cy1}};
	},

	//returns the start/end points of this curve REAL ABSOLUTE VALUES
	getStartEnd: function()
	{
		return {p0: {x:this.x0, y:this.y0}, p1: {x:this.x1, y:this.y1}};
	},
	
	//utility to plot the path for debugging purposes
	plot: function(cb)
	{
		for(var i = 0; i < 1000; i++) cb(this.getCoordinates(i/1000), i);
	}

});

/*
Class: QuadBezier
	Describes a Quadratic Bezier Curve.

Arguments:
	p0, p1: Start/End Vertex Points
	c: 		Control Point

*/
var QuadBezier = new Class({

	Extends: CubicBezier,
	
	initialize: function(p0, p1, c)
	{
		this.x0 	= p0[0]; // A x1
		this.y0 	= p0[1];
		this.x1 	= p1[0]; // D x4
		this.y1 	= p1[1];
		this.cx0 	= c[0]; // B x2
		this.cy0 	= c[1];
		this.cx1 	= c[0]; // C x3
		this.cy1 	= c[1];
		
		this.origin = {x:0, y:0};
		
		this.f1 = function(t) { return (t*t*t); }
		this.f2 = function(t) { return (3*t*t*(1-t)); } 
		this.f3 = function(t) { return (3*t*(1-t)*(1-t)); }
		this.f4 = function(t) { return ((1-t)*(1-t)*(1-t)); }
	}
	
});

