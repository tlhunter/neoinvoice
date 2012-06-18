function nw(targetPanel, pageUrl) {
	MochaUI.updateContent({
		'element':	$(targetPanel),
		'url':		pageUrl,
		'padding': {top: 0, right: 0, bottom: 0, left: 0}
	});
}

function refreshProjectPanel() {
	MochaUI.updateContent({
		'element': $('projectPanel'),
		'url': 'client_project/list_tree'
	});
}

function refreshTicketPanel() {
	MochaUI.updateContent({
		'element': $('ticketPanel'),
		'url': 'ticket/list_tree'
	});
}

function refreshTeammatePanel() {
	MochaUI.updateContent({
		'element': $('teammatePanel'),
		'url': 'user/list_tree'
	});
}

function refreshInvoicePanel() {
	MochaUI.updateContent({
		'element': $('invoicePanel'),
		'url': 'invoice/list_tree'
	});
}

function ow(pageUrl, windowName, width, height) {
	if (typeof windowName == "undefined") {
		windowName = "window" + Math.ceil(Math.random()*99999);
	}
	if (typeof width == "undefined") {
		width = 340;
	}
	if (typeof height == "undefined") {
		height = 200;
	}
	new MUI.Window({
		id: windowName,
		contentURL: pageUrl,
		width: width,
		height: height
	});
}

function owIframe(pageUrl, windowName, title, width, height, noresize) {
	if (typeof windowName == "undefined") {
		windowName = "window" + Math.ceil(Math.random()*99999);
	}
	if (typeof width == "undefined") {
		width = 300;
	}
	if (typeof height == "undefined") {
		height = 200;
	}
    var options = {
		id: windowName,
		contentURL: pageUrl,
        loadMethod: 'iframe',
        title: title,
		width: width,
		height: height
	};
    if (typeof noresize != "undefined" && noresize) {
        options.resizeLimit = {'x': [0, 0], 'y': [0, 0]};
    }
	new MUI.Window(options);
}

function selectClient(clientId) {
	MochaUI.updateContent({
		'element':	$('mainPanel'),
		'url':		'project/list_by_client/' + clientId,
		'padding': {top: 0, right: 0, bottom: 0, left: 0}
	});
	MochaUI.updateContent({
		'element':	$('detailPanel'),
		'url':		'client/view/' + clientId,
		'padding': {top: 0, right: 0, bottom: 0, left: 0}
	});
}

function selectProject(projectId) {
	MochaUI.updateContent({
		'element':	$('mainPanel'),
		'url':		'segment/list_by_project/' + projectId,
		'padding': {top: 0, right: 0, bottom: 0, left: 0}
	});
	MochaUI.updateContent({
		'element':	$('detailPanel'),
		'url':		'segment/add/' + projectId,
		'padding': {top: 0, right: 0, bottom: 0, left: 0}
	});
}

function selectSegment(segmentId) {
	MochaUI.updateContent({
		'element':	$('detailPanel'),
		'url':		'segment/view/' + segmentId
	});
}

function selectTicket(ticketId) {
	MochaUI.updateContent({
		'element':	$('mainPanel'),
		'url':		'ticket/view/' + ticketId,
		'padding': {top: 0, right: 0, bottom: 0, left: 0}
	});
}

function selectInvoice(invoiceId) {
	ow('invoice/view/'+invoiceId, 'invoice_view_'+invoiceId, 600, 250);
}

function selectUser(userId) {
	MochaUI.updateContent({
		'element':	$('detailPanel'),
		'url':		'user/view/' + userId
	});
}

function createAjaxForm(formName, panelName) {
	$(formName).addEvent('submit', function(e){
		e.stop();

		$('spinner').show();
		if ($(panelName) && MUI.options.standardEffects == true) {
			$(panelName).setStyle('opacity', 0);
		} else {
			$(panelName + '_pad').empty();
		}

		this.set('send', {
			onComplete: function(response) {
				MUI.updateContent({
					'element': $(panelName),
					'content': response,
					'padding': {top: 0, right: 0, bottom: 0, left: 0}
				});
			},
			onSuccess: function(){
				if (MUI.options.standardEffects == true) {
					$(panelName).setStyle('opacity', 0).get('morph').start({'opacity': 1});
				}
			}
		});
		this.send();
	});
}

function showDashboard() {
	nw('mainPanel', 'app/dashboard');
}

function showMotd() {
	nw('detailPanel', 'app/motd');
}

function linkByCheckboxes(panel, beginUrl, formName) {
	pids = '';
	for (var i=0; i<document.forms[formName].elements.length; i++) {
		var e = document.forms[formName].elements[i];
		if ((e.type=='checkbox') && (e.checked)) {
			pids += e.name + ':';
		}
	}
	pids = pids.slice(0, -1);
	nw(panel, beginUrl+pids);
}

function deletePayment(payment_id) {
	getUrl = 'invoice/payment_delete/' + payment_id;
	hideElement = 'payment-row-' + payment_id;
	var req = new Request({
		method: 'get',
		url: getUrl,
		onComplete: function(response) {
			if (response == '1') {
				$(hideElement).hide();
			} else {

			}
		}
	}).send();
}

function unassignExpense(expense_id) {
	getUrl = 'invoice/expense_unassign/' + expense_id;
	hideElement = 'expense-row-' + expense_id;
	var req = new Request({
		method: 'get',
		url: getUrl,
		onComplete: function(response) {
			if (response == '1') {
				$(hideElement).hide();
			} else {

			}
		}
	}).send();
}

function unassignSegment(segment_id) {
	getUrl = 'invoice/segment_unassign/' + segment_id;
	hideElement = 'segment-row-' + segment_id;
	var req = new Request({
		method: 'get',
		url: getUrl,
		onComplete: function(response) {
			if (response == '1') {
				$(hideElement).hide();
			} else {

			}
		}
	}).send();
}

function updateTemplate(element, type, duedate, amount, content, pastdue, clientname, totalpaid, remainpay) {
	message = "";
	if (type == 'reminder') {
		message = "Dear " + clientname + ",\n\
\n\
This is a friendly automated reminder that your invoice is due on " + duedate + ", which is in " + (0-pastdue) + " days from now.\n\
\n\
The total amount of your invoice is $" + amount + ". We have you recorded as having paid $" + totalpaid + ", which leaves\n\
your remaining balance as $" + remainpay + ". Here are the notes tied to this invoice:\n\
\n\
" + content;
	} else if (type == 'overdue') {
		message = clientname + ",\n\
\n\
This is an automated reminder that your invoice was due on " + duedate + ", which is " + pastdue + " days late.\n\
\n\
The total amount of your invoice is $" + amount + ". We have you recorded as having paid $" + totalpaid + ", which leaves\n\
your remaining balance as $" + remainpay + ". Here are the notes tied to this invoice:\n\
\n\
" + content;
	}
	element.value = message;
}

function checkboxToggleElementVisibility(checkboxElement, targetElement) {
	if ($(checkboxElement).get('checked')) {
		$(targetElement).fade('in');
	} else {
		$(targetElement).fade('out');
	}
}

function setupCalculateInvoiceCost(checkboxContainer, costElement) {
	$(checkboxContainer).addEvent('click', function() {
		calculateInvoiceCost(checkboxContainer, costElement);
	});
	calculateInvoiceCost(checkboxContainer, costElement);
}

function calculateInvoiceCost(checkboxContainer, costElement) {
	var invoiceTotal = 0.00;
	$$('#' + checkboxContainer + ' input[type=checkbox]:checked').each(function(element) {
		invoiceTotal += parseFloat(element.getAttribute('data-cost'));
	});
	$(costElement).setAttribute('value', invoiceTotal.toFixed(2));
}