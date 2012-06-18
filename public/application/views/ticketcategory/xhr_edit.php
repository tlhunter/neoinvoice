<h1 class="panelTitle">Edit Ticket Category: <?=$ticketcategory['name']?></h1>
<form method="post" action="ticketcategory/edit_submit/<?=$ticketcategory['id']?>" name="ticketcategory_edit" id="ticketcategory_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" value="<?=$ticketcategory['name']?>" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="description" cols="50" rows="4"><?=$ticketcategory['description']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Ticket Category" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('ticketcategory_edit', 'detailPanel');
</script>
