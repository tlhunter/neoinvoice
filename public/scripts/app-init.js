initializeColumns = function() {
	new MUI.Column({
		id: 'leftColumn',
		placement: 'left',
		width: 220,
		resizeLimit: [210, 400]
	});
	
	new MUI.Column({
		id: 'mainColumn',
		placement: 'main',
		resizeLimit: [200, 300]
	});
	
	new MUI.Column({
		id: 'rightColumn',
		placement: 'right',
		width: 220,
		resizeLimit: [210, 400]
	});
	
	new MUI.Panel({
		id: 'projectPanel',
		title: 'Projects & Clients',
		contentURL: 'client_project/list_tree/init',
		column: 'leftColumn',
		require: {
			css: [MUI.path.plugins + 'tree/css/style.css'],			
			js: [MUI.path.plugins + 'tree/scripts/tree.js'],
			onload: function(){
				if (buildTree) buildTree('display_tree_projects');
			}	
		},
		headerToolbox: true,
		headerToolboxURL: 'app/toolbar_project',
		height: 300
	});

	new MUI.Panel({
		id: 'ticketPanel',
		title: 'Tickets',
		contentURL: 'ticket/list_tree/init',
		column: 'rightColumn',
		require: {
			css: [MUI.path.plugins + 'tree/css/style.css'],
			js: [MUI.path.plugins + 'tree/scripts/tree.js'],
			onload: function(){
				if (buildTree) buildTree('display_tree_tickets');
			}
		},
		headerToolbox: true,
		headerToolboxURL: 'app/toolbar_ticket',
		height: 300
	});
	
	new MUI.Panel({
		id: 'mainPanel',
		title: 'Dashboard',
		contentURL: 'app/dashboard',
		column: 'mainColumn',
		headerToolbox: true,
		headerToolboxURL: 'static/xhr_main_panel_toolbar.htm',
		padding: {top: 0, right: 0, bottom: 0, left: 0}
	});
	
	new MUI.Panel({
		id: 'detailPanel',
		title: 'NeoInvoice Updates',
		contentURL: 'app/motd',
		column: 'mainColumn',
		height: 250,
		headerToolbox: true,
		headerToolboxURL: 'static/xhr_detail_panel_toolbar.htm',
		padding: {top: 0, right: 0, bottom: 0, left: 0}
	});
	
	new MUI.Panel({
		id: 'invoicePanel',
		title: 'Invoices',
		contentURL: 'invoice/list_tree/init',
		column: 'leftColumn',
		require: {
			css: [MUI.path.plugins + 'tree/css/style.css'],
			js: [MUI.path.plugins + 'tree/scripts/tree.js'],
			onload: function(){
				if (buildTree) buildTree('display_tree_invoices');
			}
		},
		headerToolbox: true,
		headerToolboxURL: 'app/toolbar_invoice'
	});
	new MUI.Panel({
		id: 'teammatePanel',
		title: 'Teammates',
		contentURL: 'user/list_tree/init',
		column: 'rightColumn',
		require: {
			css: [MUI.path.plugins + 'tree/css/style.css'],
			js: [MUI.path.plugins + 'tree/scripts/tree.js'],
			onload: function(){
				if (buildTree) buildTree('display_tree_teammates');
			}
		},
		headerToolbox: true,
		headerToolboxURL: 'app/toolbar_user'
	});

	MUI.myChain.callChain();
}

window.addEvent('load', function(){
	MUI.myChain = new Chain();
	MUI.myChain.chain(
		function(){MUI.Desktop.initialize();},
		function(){MUI.Dock.initialize();},
		function(){initializeColumns();}
	).callChain();	
});
