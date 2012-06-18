/**
 * Widget code Copyright 2011 Renowned Media
 * The source code for this application is NOT open source. Please do not re-distribute.
 * http://www.neoinvoice.com http://www.renownedmedia.com
 */
var drawerVisibility = false;
var configVisibility = false;
var drawerTimer;
var $drawer;
var $preTimer;
var $button;
var $postTimer;
var $resolution;
var resolution;
var timerSeconds = 0;
var startRecordTime;
var isWidget = (typeof(widget) != "undefined");

var serviceUrl = {
	getVersion: 	'http://neoinvoice.com/widget/version',
	login: 			'http://neoinvoice.com/widget/get_worktypes_projects',
	listTickets: 	'http://neoinvoice.com/widget/tickets',
	saveData: 		'http://neoinvoice.com/widget/save',
	update: 		'http://neoinvoice.com/dashboardwidget',
	register: 		'http://neoinvoice.com/login/register',
	help: 			'http://neoinvoice.com/docs/widget-help'
}

$(function() {
	$drawer = $('#drawer');
	$preTimer = $('#pre-time');
	$postTimer = $('#post-time');
	$button = $('#start-button');
	$config = $('#config');
	$inputScreen = $('#screen-input');
	$configScreen = $('#screen-config');
	$loginButton = $('#login-button');
	$resolution = $('#resolution');
	
	$('#link-register').click(function() {
		if (isWidget) {
			widget.openURL(serviceUrl.register);
		} else {
			window.open(serviceUrl.register);
		}
	});
	
	$('#link-help').click(function() {
		if (isWidget) {
			widget.openURL(serviceUrl.help);
		} else {
			window.open(serviceUrl.help);
		}
	});
	
	$button.click(function() {
		if (drawerVisibility) { // User is stopping their time recording session
			$button.val('Start Working')
			$drawer.slideUp('slow');
			
			xhrSaveData();
			
			drawerVisibility = false;
			$('body').addClass('show-config');
			
			clearTimeout(drawerTimer);
			enableControls();
			
			timerSeconds = 0;
			$preTimer.text('00:00:00');
			$postTimer.text('00:00:00');
		} else { // User is starting their time recording session
			if (
				$('#username').val() &&
				$('#password').val() &&
				parseInt($('#project').val()) &&
				parseInt($('#worktype').val())
			) {
				$button.val('Done Working');
				$drawer.slideDown('slow');

				drawerVisibility = true;
				$('body').removeClass('show-config');

				resolution = parseInt($resolution.val());
				drawerTimer = setInterval("incrementTimer()", 1000);
				disableControls();
				
				startRecordTime = new Date();
			} else {
				notice("Please Login and select a project and worktype first (Click the gear icon in the lower right).");
			}
		}
	});
	
	$config.click(function() {
		if (drawerVisibility) { // This shouldn't happen anyway since we hide the icon while running
			notice('Cannot Configure Widget while Running!');
			return;
		}
		if (configVisibility) { // Showing Input screen
			configVisibility = false;
			renderInputScreen();
			
			var defaultProject = parseInt($('#default-project').val());
			var defaultWorktype = parseInt($('#default-worktype').val());
			
			if (defaultProject) {
				$('#project option[value="' + defaultProject + '"]').attr('selected', 'selected');
				reloadTickets();
			}
			if (defaultWorktype) $('#worktype option[value="' + defaultWorktype + '"]').attr('selected', 'selected');
			
			if (isWidget) {
				widget.setPreferenceForKey('username', $('#username').val());
				widget.setPreferenceForKey('password', $('#password').val());
				
				if (defaultProject) widget.setPreferenceForKey('defaultProject', defaultProject);
				if (defaultWorktype)  widget.setPreferenceForKey('defaultWorktype', defaultWorktype);
				
				widget.setPreferenceForKey('resolution', parseInt($('#resolution').val()));
			}
		} else { // Showing Config screen
			configVisibility = true;
			renderConfigScreen();
		}
		
	});
	
	$('#project').change(function() {
		reloadTickets();
	});
	
	$loginButton.click(function() {
		xhrLogin();
	});
});

function renderConfigScreen() {
	if (isWidget) widget.prepareForTransition('ToBack');
	$inputScreen.hide();
	$configScreen.show();
	if (isWidget) setTimeout("widget.performTransition()", 10);
}

function renderInputScreen() {
	if (isWidget) widget.prepareForTransition('ToFront');
	$configScreen.hide();
	$inputScreen.show();
	if (isWidget) setTimeout("widget.performTransition()", 10);
}

function incrementTimer() {
	timerSeconds += 1;
	var resolutionInSeconds = resolution * 60;
	$preTimer.text(secondsToTime(timerSeconds));
	var recordableTime = Math.ceil(timerSeconds / resolutionInSeconds) * resolutionInSeconds;
	$postTimer.text(secondsToTime(recordableTime, true));
}

function enableControls() {
	$('#project').attr('disabled', false);
	$('#worktype').attr('disabled', false);
	$('#ticket').attr('disabled', false);
	$('#notes').attr('disabled', false).fadeTo(0, 1);
}

function disableControls() {
	$('#project').attr('disabled', 'disabled');
	$('#worktype').attr('disabled', 'disabled');
	$('#ticket').attr('disabled', 'disabled');
	$('#notes').attr('disabled', 'disabled').fadeTo(0, 0.5);
}

function secondsToTime(secs, hideSeconds) {
	if (typeof(hideSeconds) == "undefined") {
		hideSeconds = false;
	}
    var hours = Math.floor(secs / (60 * 60));

    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);

    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);

	var display = padDigits(hours, 2) + ":" + padDigits(minutes, 2);
	if (!hideSeconds) {
		display += ":" + padDigits(seconds, 2)
	}

    return display;
}

function padDigits(n, totalDigits) {
	n = n.toString();
	var pd = '';
	if (totalDigits > n.length) {
		for (i=0; i < (totalDigits-n.length); i++) {
			pd += '0';
		}
	}
	return pd + n.toString();
}

function notice(message) {
	$("#notice").text(message);
	$("#notice").fadeIn('fast').delay(3000).fadeOut('slow');
}

function xhrLogin() {
	username = $('#username').val();
	password = $('#password').val();
	if (!username || !password) {
		notice('Empty Username or Password');
		return false;
	}
	$.ajax(
		{
			'type': 'POST',
			'url': serviceUrl.login,
			'data': {
				'username': username,
				'password': password,
			},
			'dataType': 'json',
			'success': function(data) {
				if (data.error) {
					notice("There was an error logging in. Please try again.");
				} else {
					notice("You have logged in, Projects and Work Types have been retrieved.");
					populateProjects(data.projects);
					populateWorktypes(data.worktypes);
				}
			},
			'error': function() {
				notice("There was an error trying to login. Please try again.");
			}
		}
	);
}

function xhrCheckVersion() {
	$widgetVersion = $("#widget-version");
	$widgetVersion.html("Version " + widgetVersion);
	$.ajax(
		{
			url: serviceUrl.getVersion,
			dataType: 'json',
			success: function(data) {
				if (!data.error) {
					var version = data.version;
					if (version > widgetVersion) {
						notice("There is an update available for the widget.");
						$widgetVersion.append(" | <a onclick='widget.openURL(\"" + serviceUrl.update + "\")'>Update</a>");
					} else {
						$widgetVersion.append( " | Current");
					}
				}
			},
			error: function() {
				$widgetVersion.append(" | Error...");
			}
		}
	);
}

function xhrSaveData() {
	var username = $('#username').val();
	var password = $('#password').val();
	if (!username || !password) {
		notice('Empty Username or Password');
		return false;
	}
	
	duration = $postTimer.text();
	
	
	var hour = startRecordTime.getHours();
	var minute = startRecordTime.getMinutes();
	var roundedMinute = Math.ceil(minute / resolution) * resolution;
	if (roundedMinute == 60) {
		hour += 1;
		roundedMinute = 0;
	}
	
	var timeBegin = padDigits(hour, 2) + ":" + padDigits(roundedMinute, 2);
	
	project = parseInt($('#project').val());
	worktype = parseInt($('#worktype').val());
	ticket = parseInt($('#ticket').val());
	notes = $('#notes').val();
	data = {
		'username': username,
		'password': password,
		'duration': duration,
		'starttime': timeBegin,
		'project': project,
		'worktype': worktype,
		'notes': notes,
	};
	if (ticket) {
		data.ticket = ticket;
	}
	
	$.ajax(
		{
			'type': 'POST',
			'url': serviceUrl.saveData,
			'data': data,
			'dataType': 'json',
			'success': function(data) {
				if (data.error) {
					notice("There was an error saving your time... Please enter it manually on the website. [server]");
				} else {
					notice("Time has been recorded!");
				}
			},
			'error': function() {
				notice("There was an error saving your time... Please enter it manually on the website. [client]");
			}
		}
	);
}

function populateProjects(projects) {
	$project = $('#project');
	$defaultProject = $('#default-project');
	
	$project.empty();
	$defaultProject.empty();
	
	$project.append("<option>Project</option>");
	$defaultProject.append("<option>Default Project</option>");
	
	$.each(projects, function(key, value) {
		element = "<option value='" + value.id + "'>" + value.name + "</option>";
		$project.append(element);
		$defaultProject.append(element);
	});
}

function populateWorktypes(worktypes) {
	$worktype = $('#worktype');
	$defaultWorktype = $('#default-worktype');
	
	$worktype.empty();
	$defaultWorktype.empty();
	
	$worktype.append("<option>Worktype</option>");
	$defaultWorktype.append("<option>Default Worktype</option>");
	
	$.each(worktypes, function(key, value) {
		element = "<option value='" + value.id + "'>" + value.name + "</option>";
		$worktype.append(element);
		$defaultWorktype.append(element);
	});
}

function populateTickets(tickets) {
	$ticket = $('#ticket');
	$ticket.empty();
	
	if (tickets && tickets.length) {
		$ticket.attr('disabled', false);
		$ticket.append("<option>Ticket (Optional)</option>");
	
		$.each(tickets, function(key, value) {
			element = "<option value='" + value.id + "'>" + value.name + "</option>";
			$ticket.append(element);
		});
	} else {
		$ticket.append("<option>Project has no Tickets</option>");
		$ticket.attr('disabled', 'disabled');
	}
}

function reloadTickets() {
	var selectedProject = parseInt($("#project option:selected").val());
	if (selectedProject) {
		$.ajax(
			{
				'type': 'POST',
				'url': serviceUrl.listTickets,
				'data': {
					'username': username,
					'password': password,
					'project': selectedProject,
				},
				'dataType': 'json',
				'success': function(data) {
					populateTickets(data.tickets);
				},
				'error': function() {
					notice("There was an error trying to get this projects tickets. Please try again.");
				}
			}
		);
	}
}

if (isWidget) {
	widget.onshow = function() {
		username = widget.preferenceForKey('username');
		password = widget.preferenceForKey('password');
		if (username) $('#username').val(username);
		if (password) $('#password').val(password);
		// check time resolution and set the dropdown accordingly
		xhrCheckVersion();
	}
}