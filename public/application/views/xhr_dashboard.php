<h1 class="panelTitle">Dashboard</h1>
<div class="icon_group">
	<div class="icon_48"><a onclick ="nw('mainPanel', 'static/quickstart');" title="Quickstart"><div class="icon_inside"><img src="images/icons/48/quickstart.png" alt="Quickstart" /></div><div class="icon_text">Quickstart</div></a></div>
	<?php if ($permissions->segment->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'segment/add');" title="Record Time"><div class="icon_inside"><img src="images/icons/48/add_segment.png" alt="Record Time" /></div><div class="icon_text">Record Time</div></a></div>
	<?php } ?>
	<?php if ($permissions->ticket->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'ticket/add');" title="Add Ticket"><div class="icon_inside"><img src="images/icons/48/add_ticket.png" alt="Add Ticet" /></div><div class="icon_text">Add Ticket</div></a></div>
	<?php } ?>
	<?php if ($permissions->user->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'user/add');" title="Add User"><div class="icon_inside"><img src="images/icons/48/add_user.png" alt="Add User" /></div><div class="icon_text">Add User</div></a></div>
	<?php } ?>
	<?php if ($permissions->project->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'project/add');" title="Add Project"><div class="icon_inside"><img src="images/icons/48/add_project.png" alt="Add Project" /></div><div class="icon_text">Add Project</div></a></div>
	<?php } ?>
	<?php if ($permissions->client->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'client/add');" title="Add Client"><div class="icon_inside"><img src="images/icons/48/add_client.png" alt="Add Client" /></div><div class="icon_text">Add Client</div></a></div>
	<?php } ?>
	<?php if ($permissions->expense->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'expense/add');" title="Add Expense"><div class="icon_inside"><img src="images/icons/48/add_expense.png" alt="Add Expense" /></div><div class="icon_text">Add Expense</div></a></div>
	<?php } ?>
	<?php if ($permissions->reports->access) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'statistics');" title="Statistics"><div class="icon_inside"><img src="images/icons/48/reports.png" alt="Statistics" /></div><div class="icon_text">Statistics</div></a></div>
	<?php } ?>
	<?php if ($permissions->payment->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'invoice/payment');" title="Record Payment"><div class="icon_inside"><img src="images/icons/48/payment.png" alt="Record Payment" /></div><div class="icon_text">Payment</div></a></div>
	<?php } ?>
	<?php if ($permissions->invoice->create) { ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'invoice/add');" title="New Invoice"><div class="icon_inside"><img src="images/icons/48/invoice.png" alt="New Invoice" /></div><div class="icon_text">New Invoice</div></a></div>
	<?php } ?>
	<div class="icon_48"><a onclick ="nw('mainPanel', 'app/preferences');" title="User Preferences"><div class="icon_inside"><img src="images/icons/48/preferences.png" alt="User Preferences" /></div><div class="icon_text">Preferences</div></a></div>
	<div class="icon_48"><a href="docs/section/updates/feed/" target="_blank" title="Subscribe"><div class="icon_inside"><img src="images/icons/48/rss.png" alt="Subscribe" /></div><div class="icon_text">Subscribe</div></a></div>
	<div class="icon_48"><a href="login/logout" title="Logout"><div class="icon_inside"><img src="images/icons/48/logout.png" alt="Logout" /></div><div class="icon_text">Logout</div></a></div>
	<div class="icon_clear"></div>
</div>