<?php
function sort_client_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'client/list_items/$page/$new_column', 'All Clients', 0);\">$display</a>";
	return $label;
}

function sort_client_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'client/list_items/$new_page/$column', 'All Clients', 0);\">$display</a>";
	return $label;
}

function sort_invoice_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'invoice/list_items/$page/$new_column', 'All Invoices', 0);\">$display</a>";
	return $label;
}

function sort_invoice_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'invoice/list_items/$new_page/$column', 'All Invoices', 0);\">$display</a>";
	return $label;
}

function sort_project_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'project/list_items/$page/$new_column', 'All Projects', 0);\">$display</a>";
	return $label;
}

function sort_project_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'project/list_items/$new_page/$column', 'All Projects', 0);\">$display</a>";
	return $label;
}

function sort_segment_page($new_page, $current_page, $project_id, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'segment/list_by_project/$project_id/$new_page', 'Project Segments', 0);\">$display</a>"; #title needs to change
	return $label;
}

function sort_user_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'user/list_items/$page/$new_column', 'All Users', 0);\">$display</a>";
	return $label;
}

function sort_user_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'user/list_items/$new_page/$column', 'All Users', 0);\">$display</a>";
	return $label;
}

function sort_ticket_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'ticket/list_items/$page/$new_column', 'All Tickets', 0);\">$display</a>";
	return $label;
}

function sort_ticket_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'ticket/list_items/$new_page/$column', 'All Tickets', 0);\">$display</a>";
	return $label;
}

function sort_ticketstage_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'ticketstage/list_items/$page/$new_column', 'All Ticket Stages', 0);\">$display</a>";
	return $label;
}

function sort_ticketstage_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'ticketstage/list_items/$new_page/$column', 'All Ticket Stages', 0);\">$display</a>";
	return $label;
}

function sort_ticketcategory_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'ticketcategory/list_items/$page/$new_column', 'All Ticket Categories', 0);\">$display</a>";
	return $label;
}

function sort_ticketcategory_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'ticketcategory/list_items/$new_page/$column', 'All Ticket Categories', 0);\">$display</a>";
	return $label;
}

function sort_worktype_head($page, $current_column, $new_column, $display) {
	$cl = '';
	if ($current_column == $new_column)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'worktype/list_items/$page/$new_column', 'All Work Types', 0);\">$display</a>";
	return $label;
}

function sort_worktype_page($new_page, $current_page, $column, $display) {
	$cl = '';
	if ($current_page == $new_page)
		$cl = "current";
	$label = "<a class='$cl' onClick=\"nw('mainPanel', 'worktype/list_items/$new_page/$column', 'All Work Types', 0);\">$display</a>";
	return $label;
}