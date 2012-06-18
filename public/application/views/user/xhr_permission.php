<h1 class="panelTitle">Update User Permissions</h1>
<form method="post" action="user/permission_submit/<?=$user_id?>" name="user_permission" id="user_permission">
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr>
        <td class="label-cell"><strong>Category</strong></td>
        <th width="50" style="text-align: center;">New</th>
        <th width="50" style="text-align: center;">Change</th>
        <th width="50" style="text-align: center;">Delete</th>
        <th width="50" style="text-align: center;">Special</th>
        <th>Special Explained</th>
        <th>Overview</th>
    </tr>
	<tr>
        <td class="label-cell">Company</td>
        <td align="center">&nbsp;</td>
        <td align="center" class="important_permission" title="Change company name"><?=checkbox('company_update', $permissions->company->update)?></td>
        <td align="center" class="important_permission" title="Delete the company"><?=checkbox('company_delete', $permissions->company->delete)?></td>
        <td align="center" class="important_permission" title="Upgrade or downgrade the NeoInvoice plan"><?=checkbox('company_upgrade', $permissions->company->upgrade)?></td>
        <td>&laquo; Upgrade</td>
        <td class="notes-column">Company-Wide Settings</td>
    </tr>
	<tr>
        <td class="label-cell">Client</td>
        <td align="center" title="Add a new client"><?=checkbox('client_create', $permissions->client->create)?></td>
        <td align="center" title="Make changes to clients"><?=checkbox('client_update', $permissions->client->update)?></td>
        <td align="center" title="Delete a client"><?=checkbox('client_delete', $permissions->client->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Add new clients, edit clients, and delete clients.</td>
    </tr>
	<tr>
        <td class="label-cell">Expense</td>
        <td align="center" title="Record an expense"><?=checkbox('expense_create', $permissions->expense->create)?></td>
        <td align="center" title="Make changes to expenses"><?=checkbox('expense_update', $permissions->expense->update)?></td>
        <td align="center" title="Delete an expense"><?=checkbox('expense_delete', $permissions->expense->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Record expenses, edit existing expenses, and delete expenses.</td>
    </tr>
	<tr>
        <td class="label-cell">Invoice</td>
        <td align="center" title="Make a new invoice"><?=checkbox('invoice_create', $permissions->invoice->create)?></td>
        <td align="center" title="Make changes to an invoice"><?=checkbox('invoice_update', $permissions->invoice->update)?></td>
        <td align="center" title="Delete an invoice"><?=checkbox('invoice_delete', $permissions->invoice->delete)?></td>
        <td align="center" title="E-Mail an invoice to a client"><?=checkbox('invoice_send', $permissions->invoice->send)?></td>
        <td>&laquo; E-Mail to Client</td>
        <td class="notes-column">Make new Invoices, update and delete them, or send an email.</td>
    </tr>
	<tr>
        <td class="label-cell">Payment</td>
        <td align="center" title="Record a payment"><?=checkbox('payment_create', $permissions->payment->create)?></td>
        <td align="center" title="Make changes to a payment"><?=checkbox('payment_update', $permissions->payment->update)?></td>
        <td align="center" title="Delete a payment"><?=checkbox('payment_delete', $permissions->payment->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Record, edit, and delete payments.</td>
    </tr>
	<tr>
        <td class="label-cell">Project</td>
        <td align="center" title="Make a new project"><?=checkbox('project_create', $permissions->project->create)?></td>
        <td align="center" title="Make changes to a project"><?=checkbox('project_update', $permissions->project->update)?></td>
        <td align="center" title="Delete a project"><?=checkbox('project_delete', $permissions->project->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Add new projects, edit them, and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">Reports</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center" title="View the reports screen"><?=checkbox('reports_access', $permissions->reports->access)?></td>
        <td>&laquo; View Reports</td>
        <td class="notes-column">Allows a user to view the overall reports.</td>
    </tr>
	<tr>
        <td class="label-cell">Teammates</td>
        <td align="center" title="Make a new teammate"><?=checkbox('user_create', $permissions->user->create)?></td>
        <td align="center" title="Make changes to a teammate"><?=checkbox('user_update', $permissions->user->update)?></td>
        <td align="center" title="Delete a teammate"><?=checkbox('user_delete', $permissions->user->delete)?></td>
        <td align="center" title="Set Permissions of a teammate (even themself!)" class="important_permission"><?=checkbox('user_setperms', $permissions->user->setperms)?></td>
        <td>&laquo; Change Permissions</td>
        <td class="notes-column">Make new teammates, edit them, and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">Time Segments</td>
        <td align="center" title="Record the time they've worked on a project"><?=checkbox('segment_create', $permissions->segment->create)?></td>
        <td align="center" title="Make changes to a time segment"><?=checkbox('segment_update', $permissions->segment->update)?></td>
        <td align="center" title="Delete a time segment"><?=checkbox('segment_delete', $permissions->segment->delete)?></td>
        <td align="center" title="Make changes to another persons time"><?=checkbox('segment_editother', $permissions->segment->editother)?></td>
        <td>&laquo; Edit Others</td>
        <td class="notes-column">Record time segments, edit and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">Tickets</td>
        <td align="center" title="Make a new issue ticket"><?=checkbox('ticket_create', $permissions->ticket->create)?></td>
        <td align="center" title="Make changes to a ticket"><?=checkbox('ticket_update', $permissions->ticket->update)?></td>
        <td align="center" title="Delete a ticket"><?=checkbox('ticket_delete', $permissions->ticket->delete)?></td>
        <td align="center" title="Edit other peoples tickets"><?=checkbox('ticket_editother', $permissions->ticket->editother)?></td>
        <td>&laquo; Edit Others</td>
        <td class="notes-column">Make new tickets, edit and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">Work Types</td>
        <td align="center" title="Make a new Work Type"><?=checkbox('worktype_create', $permissions->worktype->create)?></td>
        <td align="center" title="Make changes to a Work Type"><?=checkbox('worktype_update', $permissions->worktype->update)?></td>
        <td align="center" title="Delete a Work Type"><?=checkbox('worktype_delete', $permissions->worktype->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Make new work types, edit and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">User Groups</td>
        <td align="center" title="Make a new Work Group"><?=checkbox('usergroup_create', $permissions->usergroup->create)?></td>
        <td align="center" title="Make changes to a Work Group"><?=checkbox('usergroup_update', $permissions->usergroup->update)?></td>
        <td align="center" title="Delete a Work Group"><?=checkbox('usergroup_delete', $permissions->usergroup->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Make new user groups, edit and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">Ticket Types</td>
        <td align="center" title="Make a new Ticket Type"><?=checkbox('tickettype_create', $permissions->tickettype->create)?></td>
        <td align="center" title="Make changes to a Ticket Type"><?=checkbox('tickettype_update', $permissions->tickettype->update)?></td>
        <td align="center" title="Delete a Ticket Type"><?=checkbox('tickettype_delete', $permissions->tickettype->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Make new ticket types, edit and delete them.</td>
    </tr>
	<tr>
        <td class="label-cell">Expense Types</td>
        <td align="center" title="Make a new Expense Type"><?=checkbox('expensetype_create', $permissions->expensetype->create)?></td>
        <td align="center" title="Make changes to an Expense Type"><?=checkbox('expensetype_update', $permissions->expensetype->update)?></td>
        <td align="center" title="Delete an Expense Type"><?=checkbox('expensetype_delete', $permissions->expensetype->delete)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="notes-column">Make new expense types, edit and delete them.</td>
    </tr>
</table>
<div style="margin: 10px;">
	<input type="submit" class="button" value="Save Permissions" /> * Giving permissions in orange grants administrative privileges, so use with discretion!
</div>
</form>
<script type="text/javascript">
	createAjaxForm('user_permission', 'detailPanel');
</script>