function createCanvas(){

	animationWrapper = $('animation');
	
	this.canvasPath = new Element('canvas', {
		'id': 'canvasPath',
		'width': 300,
		'height': 300,
		'styles': {
			'zIndex': 3
		}		
	}).inject(animationWrapper);
	
	if (Browser.Engine.trident){
		G_vmlCanvasManager.initElement(this.canvasPath);
	}
	
	this.canvasSprite = new Element('canvas', {
		'id': 'sprite',
		'width': 10,
		'height': 10,
		'styles': {
			'zIndex': 100,
			'top': -5,
			'left': 145
		}
	}).inject(animationWrapper);
	
	if (Browser.Engine.trident){
		G_vmlCanvasManager.initElement(this.canvasSprite);
	}
	
	renderCanvasPath();
	renderCanvasSprite()
		
}

function renderCanvasPath(){
	var ctx = this.canvasPath.getContext('2d');	
	ctx.strokeStyle = 'rgb(255, 255, 255)';
	ctx.lineWidth = 4;
	ctx.beginPath();
	ctx.moveTo(101.99, 198); // p0
	ctx.bezierCurveTo(56.95, 238.42, 149.15, 271.17, 150, 300); // c0, c1, p1 
	ctx.bezierCurveTo(150.85, 271.17, 243.03, 238.42,198, 198);
	ctx.bezierCurveTo(238.42, 243.04, 271.17, 150.84, 300, 149.99);
	ctx.bezierCurveTo(271.17, 149.14, 238.42, 56.95, 198, 101.99);
	ctx.bezierCurveTo(243.03, 61.57, 150.84, 28.82, 150, 0);
	ctx.bezierCurveTo(149.15, 28.82, 56.95, 61.57, 101.99, 101.99);
	ctx.bezierCurveTo(61.57, 56.95, 28.82, 149.15, 0, 149.99);
	ctx.bezierCurveTo(28.83, 150.84, 61.57, 243.04, 101.99, 198);
	ctx.stroke();	
}

function renderCanvasSprite(){
	var ctx = this.canvasSprite.getContext('2d');	
	ctx.fillStyle = 'rgb(255, 255, 255)';
	ctx.beginPath();
	ctx.arc(5, 5, 5, 0, Math.PI*2, true);
	ctx.fill();
}
		
function myAnim(){
	var sprite = $('sprite');  
	sprite.set('morpher', {  
		path: new Fx.Path([
			new CubicBezier([150,0], [101.99,101.99], [149.15,28.82], [56.95,61.57]), // p0, p1, c0, c1
			new CubicBezier([101.99,101.99], [0,149.99], [61.57,56.95], [28.82,149.15]),
			new CubicBezier([0,149.99], [101.99,198], [28.83,150.84], [61.57,243.04]),
			new CubicBezier([101.99,198], [150,300], [56.95,238.42], [149.15,271.17]),
			new CubicBezier([150,300], [198,198], [150.85,271.17], [243.03,238.42]),
			new CubicBezier([198,198], [300,149.99], [238.42,243.04], [271.17,150.84]),
			new CubicBezier([300,149.99], [198,101.99], [271.17,149.14], [238.42,56.95]),
			new CubicBezier([198,101.99], [150,0], [243.03,61.57], [150.84,28.82])									
 		]),
		fps: 30,
		regpoint: Fx.RegPoint.Center,  
		duration: 7000,
		onComplete: function(){
			sprite.morpher();
		} 
	});  
	sprite.morpher();
}
	