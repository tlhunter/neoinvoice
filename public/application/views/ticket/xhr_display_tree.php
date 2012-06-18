<h1 class="panelTitle">Tickets</h1>
<?php
$user_id = $this->session->userdata('id');
$usergroup_id = $this->session->userdata('usergroup_id');
?>
<ul id="display_tree_tickets" class="tree">

	<li class="folder f-open first"><span>My Assigned Tickets</span>
		<ul>
<?php
foreach($tickets AS $ticket) {
	if ($ticket['countdown'] > 7) {
		$color = 'ticket-fine';
	} else if ($ticket['countdown'] > 0) {
		$color = 'ticket-week';
	} else {
		$color = 'ticket-overdue';
	}
	if ($ticket['assigned_user_id'] == $user_id) {
		echo "\t\t\t<li class=\"doc $color\" data-tree-icon=\"ticket\"><span><a onclick=\"selectTicket({$ticket['id']});\">{$ticket['id']}: {$ticket['name']}</a></span></li>\n";
	}
}
?>
		</ul>
	</li>
	<li class="folder f-open"><span>My Groups Assigned Tickets</span>
		<ul>
<?php
foreach($tickets AS $ticket) {
	if ($ticket['countdown'] > 7) {
		$color = 'ticket-fine';
	} else if ($ticket['countdown'] > 0) {
		$color = 'ticket-week';
	} else {
		$color = 'ticket-overdue';
	}
	if (($ticket['assigned_user_id'] != $user_id) && ($ticket['assigned_usergroup_id'] == $usergroup_id)) {
		echo "\t\t\t<li class=\"doc $color\" data-tree-icon=\"ticket\"><span><a onclick=\"selectTicket({$ticket['id']});\">{$ticket['id']}: {$ticket['name']}</a></span></li>\n";
	}
}
?>
		</ul>
	</li>
	<li class="folder f-close"><span>Other Tickets</span>
		<ul>
<?php
foreach($tickets AS $ticket) {
	if ($ticket['countdown'] > 7) {
		$color = 'ticket-fine';
	} else if ($ticket['countdown'] > 0) {
		$color = 'ticket-week';
	} else {
		$color = 'ticket-overdue';
	}
	if (($ticket['assigned_user_id'] != $user_id) && ($ticket['assigned_usergroup_id'] != $usergroup_id)) {
		echo "\t\t\t<li class=\"doc $color\" data-tree-icon=\"ticket\"><span><a onclick=\"selectTicket({$ticket['id']});\">{$ticket['id']}: {$ticket['name']}</a></span></li>\n";
	}
}
?>
		</ul>
	</li>
</ul>
<?php if (!isset($no_tree) || !$no_tree) { ?>
<script type="text/javascript">
	if (typeof(buildTree) != "undefined")
		buildTree('display_tree_tickets');
</script>
<?php } ?>
<div class="hover-options"><a onClick="refreshTicketPanel();">Reload Panel</a></div>