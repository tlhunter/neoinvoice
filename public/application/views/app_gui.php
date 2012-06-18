<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=8" />

	<title><?=$title?></title>

	<link rel="stylesheet" type="text/css" href="themes/neoinvoice/css/Content.css" />
	<link rel="stylesheet" type="text/css" href="themes/neoinvoice/css/Core.css" />
	<link rel="stylesheet" type="text/css" href="themes/neoinvoice/css/Layout.css" />
	<link rel="stylesheet" type="text/css" href="themes/neoinvoice/css/Dock.css" />
	<link rel="stylesheet" type="text/css" href="themes/neoinvoice/css/Window.css" />
	<link rel="stylesheet" type="text/css" href="themes/neoinvoice/css/Tabs.css" />

	<link rel="stylesheet" type="text/css" href="css/calendar-eightysix-v1.1-default.css" media="screen" />

	<link rel="alternate" type="application/rss+xml" title="NeoInvoice Documentation RSS Feed" href="<?=base_url();?>docs/feed/" />
	<link rel="alternate" type="application/atom+xml" title="NeoInvoice Documentation Atom Feed" href="<?=base_url();?>docs/feed/atom/" />

	<link rel="stylesheet" type="text/css" href="css/application.css" />

	<!--[if IE]>
		<script type="text/javascript" src="scripts/excanvas_r43.js"></script>
	<![endif]-->

	<script type="text/javascript" src="scripts/mootools-1.2.5-core.js"></script>
	<script type="text/javascript" src="scripts/mootools-1.2.5.1-more.js"></script>

	<script type="text/javascript" src="scripts/source/Core/Core.js"></script>
	<script type="text/javascript" src="scripts/source/Layout/Layout.js"></script>
	<script type="text/javascript" src="scripts/source/Layout/Dock.js"></script>
	<script type="text/javascript" src="scripts/source/Window/Window.js"></script>
	<script type="text/javascript" src="scripts/source/Window/Modal.js"></script>
	<script type="text/javascript" src="scripts/source/Components/Tabs.js"></script>

	<script type="text/javascript" src="scripts/app-init.js"></script>
	<script type="text/javascript" src="scripts/application.js"></script>
	<script type="text/javascript" src="scripts/calendar-eightysix-v1.1.js"></script>

	<script type="text/javascript">
		var session_username = '<?=$session_username?>';
		var session_email = '<?=$session_email?>';
		var session_name = '<?=$session_name?>';
		var session_userid = '<?=$session_userid?>';
		var session_companyid = '<?=$session_companyid?>';
	</script>
</head>
<body>

<div id="desktop">

	<div id="desktopHeader">
		<div id="desktopTitlebarWrapper">
			<div id="desktopTitlebar">
				<?php if (isset($company['delete_date']) && !empty($company['delete_date'])) { ?>
				<h2 id="company_deletion">Company is going to be deleted on <?=local_date($company['delete_date'])?>. <a onClick="nw('mainPanel', 'company/delete_cancel');">Cancel Company Deletion</a></h2>
				<?php } ?>
				<div id="topNav">
					<ul class="menu-right">
						<li><a href="home">Home</a></li>
						<li><a href="app" class="active">Application</a></li>
						<li><a href="features">Features</a></li>
						<li><a href="docs">Documentation</a></li>
						<li><a href="contact">Contact</a></li>
						<li><a href="login/logout">Logout</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div id="desktopNavbar">
			<ul>
				<li><a class="returnFalse" href="javascript:;">Projects &amp; Clients</a>
					<ul>
						<li><a onClick="nw('mainPanel', 'client/list_items');">Browse Clients</a></li>
						<?php if ($permissions->client->create) { ?>
						<li><a onClick="nw('mainPanel', 'client/add');">Add Client</a></li>
						<?php } ?>
						<li><a onClick="nw('mainPanel', 'project/list_items');">Browse Projects</a></li>
						<?php if ($permissions->project->create) { ?>
						<li><a onClick="nw('mainPanel', 'project/add');">Add Project</a></li>
						<?php } ?>
					</ul>
				</li>
				<li><a class="returnFalse" href="javascript:;">Invoices</a>
					<ul>
						<li><a onClick="nw('mainPanel', 'invoice/list_items');">Browse Invoices</a></li>
						<?php if ($permissions->invoice->create) { ?>
						<li><a onClick="nw('mainPanel', 'invoice/add');">Add Invoice</a></li>
						<?php } ?>
						<?php if ($permissions->payment->create) { ?>
						<li><a onClick="nw('mainPanel', 'invoice/payment');">Record Invoice Payment</a></li>
						<?php } ?>
					</ul>
				</li>
				<li><a class="returnFalse" href="javascript:;">Tickets</a>
					<ul>
						<li><a onClick="nw('mainPanel', 'ticket/list_items');">Browse Tickets</a></li>
						<?php if ($permissions->ticket->create) { ?>
						<li><a onClick="nw('mainPanel', 'ticket/add');">Add Ticket</a></li>
						<?php } ?>
					</ul>
				</li>
				<li><a class="returnFalse" href="javascript:;">View</a>
					<ul>
						<li><a onClick="window.location.reload(true);" class="menu-icon icon-reload">Reload Interface</a></li>
						<li><a onClick="refreshProjectPanel();" class="menu-icon icon-reload">Reload Projects &amp; Clients</a></li>
						<li><a onClick="refreshTicketPanel();" class="menu-icon icon-reload">Reload Tickets</a></li>
						<li><a onClick="refreshInvoicePanel();" class="menu-icon icon-reload">Reload Invoices</a></li>
						<li><a onClick="refreshTeammatePanel();" class="menu-icon icon-reload">Reload Teammates</a></li>
						<li class="divider"><a onClick="nw('mainPanel', 'app/dashboard');" class="menu-icon icon-dashboard">Dashboard</a></li>
						<li><a onClick="nw('detailPanel', 'app/motd');" class="menu-icon icon-motd">NeoInvoice Updates</a></li>
						<li class="divider"><a class="returnFalse arrow-right" href="">Windows</a>
							<ul>
								<li><a onclick="MUI.arrangeCascade();">Cascade Windows</a></li>
								<li><a onclick="MUI.arrangeTile();">Tile Windows</a></li>
								<li><a onclick="MUI.minimizeAll();">Minimize All Windows</a></li>
								<li><a onclick="MUI.closeAll();">Close All Windows</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li><a class="returnFalse" href="javascript:;">Administration</a>
					<ul>
						<?php if ($permissions->user->create || $permissions->user->update || $permissions->user->delete) { ?>
						<li><a onClick="nw('mainPanel', 'user/list_items');" class="menu-icon icon-users">User Management</a></li>
						<?php } ?>
						<?php if ($permissions->company->update) { ?>
						<li><a onClick="nw('mainPanel', 'company/preferences');" class="menu-icon icon-preferences">Company Preferences</a></li>
						<?php } ?>
						<?php /* if ($permissions->company->upgrade) { ?>
						<li><a onClick="nw('mainPanel', 'company/upgrade');" class="menu-icon icon-upgrade">Account Upgrade</a></li>
						<?php } */ ?>
						<?php if ($permissions->project->create) { ?>
						<li class="divider"><a onClick="nw('mainPanel', 'project/add');" class="menu-icon icon-project_add">New Project</a></li>
						<?php } ?>
						<?php if ($permissions->client->create) { ?>
						<li><a onClick="nw('mainPanel', 'client/add');" class="menu-icon icon-client_add">New Client</a></li>
						<?php } ?>
						<?php if ($permissions->user->create) { ?>
						<li><a onClick="nw('mainPanel', 'user/add');" class="menu-icon icon-user_add">New Teammate</a></li>
						<?php } ?>
						<?php if ($permissions->worktype->create || $permissions->worktype->update || $permissions->worktype->delete) { ?>
						<li class="divider"><a onClick="nw('mainPanel', 'worktype/list_items'); nw('detailPanel', 'worktype/add');" class="menu-icon icon-worktypes">Edit Work Types</a></li>
						<?php } ?>
						<?php if ($permissions->usergroup->create || $permissions->usergroup->update || $permissions->usergroup->delete) { ?>
						<li><a onClick="nw('mainPanel', 'usergroup/list_items'); nw('detailPanel', 'usergroup/add');" class="menu-icon icon-worktypes">Edit User Groups</a></li>
						<?php } ?>
						<?php if ($permissions->expensetype->create || $permissions->expensetype->update || $permissions->expensetype->delete) { ?>
						<li><a onClick="nw('mainPanel', 'expensetype/list_items'); nw('detailPanel', 'expensetype/add');" class="menu-icon icon-worktypes">Edit Expense Types</a></li>
						<?php } ?>
						<?php if ($permissions->tickettype->create || $permissions->tickettype->update || $permissions->tickettype->delete) { ?>
						<li><a onClick="nw('mainPanel', 'ticketcategory/list_items'); nw('detailPanel', 'ticketcategory/add');" class="menu-icon icon-worktypes">Edit Ticket Categories</a></li>
						<li><a onClick="nw('mainPanel', 'ticketstage/list_items'); nw('detailPanel', 'ticketstage/add');" class="menu-icon icon-worktypes">Edit Ticket Stages</a></li>
						<?php } ?>
						<?php if ($permissions->company->delete) { ?>
						<li class="divider"><a onClick="nw('mainPanel', 'company/delete');" class="menu-icon icon-delete_company">Delete Company Account</a></li>
						<?php } ?>
					</ul>
				</li>
				<li><a class="returnFalse" href="javascript:;"><?=$session_name?></a>
					<ul>
						<li><a onClick="nw('mainPanel', 'app/preferences');">User Preferences</a></li>
						<li><a href="docs/section/updates/feed/" target="_blank" title="Subscribe">NeoInvoice RSS</a></li>
						<li><a href="login/logout" title="Logout">Logout from NeoInvoice</a></li>
					</ul>
				</li>
			</ul>

			<div class="toolbox divider">
				<div id="spinnerWrapper"><div id="spinner"></div></div>
			</div>
		</div><!-- desktopNavbar end -->
	</div><!-- desktopHeader end -->

	<div id="dockWrapper">
		<div id="dock">
			<div id="dockPlacement"></div>
			<div id="dockAutoHide"></div>
			<div id="dockSort"><div id="dockClear" class="clear"></div></div>
		</div>
	</div>

	<div id="pageWrapper"></div>

	<div id="desktopFooterWrapper">
		<div id="desktopFooter">
			NeoInvoice &copy; 2010 <a target="_blank" href="http://www.renownedmedia.com">Renowned Media</a> | <a onclick="ow('static/about.htm', 'about_window');">About</a> | <a onclick="ow('static/privacy.htm', 'privacy_window');">Privacy Policy</a>
		</div>
	</div>

</div>
</body>
</html>