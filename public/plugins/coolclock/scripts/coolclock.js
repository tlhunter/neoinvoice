/*
CoolClock by Simon Baird (simon dot baird at gmail dot com)
Version 1.0.4 (09-Nov-2006)
See http://simonbaird.com/coolclock/

Copyright (c) Simon Baird 2006

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this
list of conditions and the following disclaimer.

Redistributions in binary form must reproduce the above copyright notice, this
list of conditions and the following disclaimer in the documentation and/or other
materials provided with the distribution.

Neither the name of the Simon Baird nor the names of other contributors may be
used to endorse or promote products derived from this software without specific
prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
DAMAGE.
*/

window.CoolClock = function(canvasId,displayRadius,skinId,showSecondHand,gmtOffset) {
	return this.init(canvasId,displayRadius,skinId,showSecondHand,gmtOffset);
}

CoolClock.config = {
	clockTracker: {},
	tickDelay: 1000,
	longTickDelay: 15000,
	defaultRadius: 85,
	renderRadius: 100,
	defaultSkin: "default",
	skins:	{
		'default': {
			outerBorder: { lineWidth: 0, radius:99, fillColor: "#577a9e", color: "#fff", alpha: 0 },
			smallIndicator: { lineWidth: 6, startAt: 82, endAt: 89, color: "#fff", alpha: .15 },
			largeIndicator: { lineWidth: 6, startAt: 82, endAt: 89, color: "#fff", alpha: .50 },
			hourHand: { lineWidth: 6, startAt: 0, endAt: 53, color: "#fff", alpha: 1 },
			minuteHand: { lineWidth: 6, startAt: 0, endAt: 80, color: "#fff", alpha: 1 },
			secondHand: { lineWidth: 2, startAt: -18, endAt: 88, color: "#fff", alpha: 1 },
			secondDecoration: { lineWidth: 0, startAt: 79, radius: 5, fillColor: "#fff", color: "#fff", alpha: 1 }
			// secondDecoration2: { lineWidth: 2, startAt: 0, radius: 8, fillColor: "#577a9e", color: "#fff", alpha: 1 }
		},
		'defaultOld': {
			outerBorder: { lineWidth: 6, radius:98, color: "#fff", alpha: 0 },
			smallIndicator: { lineWidth: 2, startAt: 86, endAt: 91, color: "#555", alpha: 1 },
			largeIndicator: { lineWidth: 3, startAt: 80, endAt: 91, color: "#555", alpha: 1 },
			hourHand: { lineWidth: 4, startAt: -1, endAt: 56, color: "#141414", alpha: 1 },
			minuteHand: { lineWidth: 4, startAt: -1, endAt: 78, color: "#141414", alpha: 1 },
			secondHand: { lineWidth: 1, startAt: -16, endAt: 80, color: "#ce1717", alpha: 1 },
			secondDecoration: { lineWidth: 2, startAt: 0, radius: 7, fillColor: "#fff", color: "#ce1717", alpha: 0 }
		},		
		'mochaUI1': {
			outerBorder: { lineWidth: 185, radius:1, color: "#000", alpha: 0 },
			smallIndicator: { lineWidth: 3, startAt: 88, endAt: 94, color: "#595959", alpha: 1 },
			largeIndicator: { lineWidth: 3, startAt: 82, endAt: 94, color: "#ddd", alpha: 1 },
			hourHand: { lineWidth: 4, startAt: 0, endAt: 58, color: "#fff", alpha: 1 },
			minuteHand: { lineWidth: 4, startAt: 0, endAt: 78, color: "#fff", alpha: 1 },
			secondHand: { lineWidth: 4, startAt: 82, endAt: 94, color: "#98B8D9", alpha: 1 },
			secondDecoration: { lineWidth: 3, startAt: 0, radius: 6, fillColor: "white", color: "white", alpha: 1 }
		},
		'mochaUI2': {
			outerBorder: { lineWidth: 185, radius:1, color: "#000", alpha: 0 },
			smallIndicator: { lineWidth: 3, startAt: 88, endAt: 94, color: "#2CC2D1", alpha: 1 },
			largeIndicator: { lineWidth: 3, startAt: 82, endAt: 94, color: "#1BFFD9", alpha: 1 },
			hourHand: { lineWidth: 4, startAt: 0, endAt: 58, color: "#fff", alpha: 1 },
			minuteHand: { lineWidth: 4, startAt: 0, endAt: 78, color: "#fff", alpha: 1 },
			secondHand: { lineWidth: 4, startAt: 82, endAt: 94, color: "#EFCD5F", alpha: 1 },
			secondDecoration: { lineWidth: 0, startAt: 0, radius: 6, fillColor: "#fff", color: "#000", alpha: 1 }
		},
		'mochaUI3': {
			outerBorder: { lineWidth: 185, radius:1, color: "#000", alpha: 0 },
			smallIndicator: { lineWidth: 3, startAt: 88, endAt: 94, color: "#C7C3B7", alpha: 1 },
			largeIndicator: { lineWidth: 3, startAt: 82, endAt: 94, color: "#C7C3B7", alpha: 1 },
			hourHand: { lineWidth: 4, startAt: -1, endAt: 58, color: "#C7C3B7", alpha: 1 },
			minuteHand: { lineWidth: 4, startAt: -1, endAt: 78, color: "#C7C3B7", alpha: 1 },
			secondHand: { lineWidth: 3, startAt: 82, endAt: 94, color: "#ce1717", alpha: 1 },
			secondDecoration: { lineWidth: 0, startAt: 0, radius: 6, fillColor: "#999", color: "#000", alpha: 0 }
		}
	}
};

CoolClock.prototype = {
	init: function() {
		var gmtOffset;
		canvasId = 'myClock';
		this.canvasId = canvasId;
		this.displayRadius = 75;
		this.skinId = 'default';
		this.showSecondHand = true;
		this.tickDelay = 1000;
		
		this.canvas = new Element('canvas', {
			'id': this.canvasId,
			'width': this.displayRadius*2,
			'height': this.displayRadius*2,
			'styles': {
				'width': this.displayRadius*2,
				'height': this.displayRadius*2
			}
		}).inject($('clocker'));
		
		if (MochaUI.ieSupport == 'excanvas' && Browser.Engine.trident ) {			
			G_vmlCanvasManager.initElement(this.canvas);			
		}

		//this.canvas.setAttribute("width",this.displayRadius*2);
		//this.canvas.setAttribute("height",this.displayRadius*2);

		//this.canvas.style.width = this.displayRadius*2 + "px";
		//this.canvas.style.height = this.displayRadius*2 + "px";

		this.renderRadius = CoolClock.config.renderRadius; 

		this.scale = this.displayRadius / this.renderRadius;
		this.ctx = this.canvas.getContext("2d");
		this.ctx.scale(this.scale,this.scale);

		this.gmtOffset = gmtOffset != null ? parseFloat(gmtOffset) : gmtOffset;

		CoolClock.config.clockTracker[canvasId] = this;
		this.initializing = true;
		this.tick();
		return this;
	},

	fullCircle: function(skin) {
		this.fullCircleAt(this.renderRadius,this.renderRadius,skin);
	},

	fullCircleAt: function(x,y,skin) {
		this.ctx.save();
		this.ctx.globalAlpha = skin.alpha;
		this.ctx.lineWidth = skin.lineWidth;
		if (document.all) {
			// excanvas doesn't scale line width so we will do it here
			this.ctx.lineWidth = this.ctx.lineWidth * this.scale;
		}	
		this.ctx.beginPath();	
		this.ctx.arc(x, y, skin.radius, 0, 2*Math.PI, false);
		this.ctx.closePath();
			
		if (document.all) {
			// excanvas doesn't close the circle so let's color in the gap
			this.ctx.arc(x, y, skin.radius, -0.1, 0.1, false);
		}	
		if (skin.fillColor) {
			this.ctx.fillStyle = skin.fillColor
			this.ctx.fill();
		}
		if (skin.color) {
			// XXX why not stroke and fill
			this.ctx.strokeStyle = skin.color;
			this.ctx.stroke();
		}
		this.ctx.restore();

	},

	reflection: function(){
   		this.ctx.fillStyle = 'rgba(250, 250, 250, .4)';
  		this.ctx.beginPath(); 
  		this.ctx.arc(100, 100, 98, 0, Math.PI, true);
  		this.ctx.bezierCurveTo(60, 80, 160, 80, 196, 100);    
  		this.ctx.fill();
	},

	bg: function(){
		this.ctx.beginPath();
		this.ctx.fillStyle = "#577a9e";
		this.ctx.arc(100, 100, 99, 0, 2 * Math.PI, false);
		this.ctx.fill();
	},

	center: function(){
		this.ctx.beginPath();
		this.ctx.fillStyle = "#577a9e";
		this.ctx.arc(100, 100, 8, 0, 2 * Math.PI, false);
		this.ctx.fill();
		this.ctx.strokeStyle = "#fff";
		this.ctx.lineWidth = 2;
		this.ctx.arc(100, 100, 8, 0, 2 * Math.PI, false);
		this.ctx.stroke();	
	},

	radialLineAtAngle: function(angleFraction,skin) {
		this.ctx.save();
		this.ctx.translate(this.renderRadius,this.renderRadius);
		this.ctx.rotate(Math.PI * (2 * angleFraction - 0.5));
		this.ctx.globalAlpha = skin.alpha;
		this.ctx.strokeStyle = skin.color;
		this.ctx.lineWidth = skin.lineWidth;
		this.ctx.lineCap = 'round';
		if (document.all){
			// excanvas doesn't scale line width so we will do it here
			this.ctx.lineWidth = this.ctx.lineWidth * this.scale;
		}	
		if (skin.radius) {
			this.fullCircleAt(skin.startAt,0,skin)
		}
		else {
			this.ctx.beginPath();
			this.ctx.moveTo(skin.startAt,0)
			this.ctx.lineTo(skin.endAt,0);
			this.ctx.stroke();
		}
		this.ctx.restore();

	},

	render: function(hour,min,sec){
		var skin = CoolClock.config.skins[this.skinId];
		this.ctx.clearRect(0,0,this.renderRadius*2,this.renderRadius*2);

			
		//this.bgGradient();
		this.bg();
		this.fullCircle(skin.outerBorder);

		for (var i = 0; i < 60; i++){
			this.radialLineAtAngle(i / 60, skin[i % 5 ? "smallIndicator" : "largeIndicator"]);
		}

		this.radialLineAtAngle((hour+min/60)/12,skin.hourHand);
		this.radialLineAtAngle((min+sec/60)/60,skin.minuteHand);
		if (this.showSecondHand){
			this.radialLineAtAngle(sec/60,skin.secondHand);
			if (!Browser.Engine.trident){
				this.radialLineAtAngle(sec/60,skin.secondDecoration);
			}
		}
		this.center();
	},


	nextTick: function(){
		setTimeout("CoolClock.config.clockTracker['"+this.canvasId+"'].tick()",this.tickDelay);
	},

	stillHere: function(){
		return $(this.canvasId) != null;
	},

	refreshDisplay: function(){
		var now = new Date();
		if (this.gmtOffset != null) {
			// use GMT + gmtOffset
			var offsetNow = new Date(now.valueOf() + (this.gmtOffset * 1000 * 60 * 60));
			this.render(offsetNow.getUTCHours(),offsetNow.getUTCMinutes(),offsetNow.getUTCSeconds());
		}
		else {
			// use local time
			var hours = now.getHours();
			var minutes = now.getMinutes();
			var seconds = now.getSeconds();
			var time;
			this.refreshTime(hours, minutes, seconds);
			this.render(hours,minutes,seconds);
		}
	},

	refreshTime: function(hours, minutes, seconds){
		var now = new Date();		
		var time;
		if (hours >= 12) {
			time = " PM";
		}
		else {
			time = " AM";
		}
		if (hours > 12) {
			hours -= 12;
		}
		if (hours == 0) {
			hours = 12;
		}
		if (minutes < 10) {
			minutes = "0" + minutes;
		}
		if (seconds == 0 || this.initializing == true){
			$('clock_title').set('html', hours + ":" + minutes + time);
			if ($('clock_dockTabText')){
				$('clock_dockTabText').set('html', hours + ":" + minutes + time);
			}
			this.initializing = false;
		}
	},

	tick: function() {
		if (this.stillHere()) {
			this.refreshDisplay()
			this.nextTick();
		}
	}
}
