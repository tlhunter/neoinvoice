<h1 class="panelTitle">Company Preferences</h1>
<form method="post" action="company/preferences_submit" name="company_preferences_edit" id="company_preferences_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Company Name</td><td><input name="name" value="<?=$company['name']?>" maxlength="120" class="textual" /></td></tr>
		<tr><td class="label-cell">Service Cost</td><td><input value="$<?=$company['service']['price']?> / month" readonly class="readonly" /></td></tr>
		<tr><td class="label-cell">Service Expires</td><td><input value="<?=$company['service_expire']?>" readonly class="readonly" /></td></tr>
		<tr><td class="label-cell">Account Created</td><td><input value="<?=$company['created']?>" readonly class="readonly" /></td></tr>
		<tr><td class="label-cell">Allocated Teammates</td><td><input value="<?=$company['user_count']?> / <?=$company['service']['pref_max_user']?>" readonly class="readonly" /></td></tr>
		<tr><td class="label-cell">New User Language</td><td><select name="language" class="textual"><option value="english">English</option></select></td></tr>
		<tr><td class="label-cell" valign="top">Invoice Contact Info</td><td><textarea name="invoice_address" class="textual" maxlength="500" cols="30" rows="6"><?=$company['invoice_address']?></textarea></td></tr>
		<!--<tr><td class="label-cell">Logo Graphic</td><td>
                <a onclick="owIframe('company/logo', 'logo_select', 'Select Invoice Logo Image', 300, 150, true);">Set Logo</a>
                <?php if ($logo_image) { ?>
                    | <a onclick="owIframe('<?=$logo_image?>', 'logo_view', 'Current Logo Image', 600, 250);">View Logo</a>
                    | <a onclick ="nw('mainPanel', 'company/logo_delete');">Delete Logo</a>
                <?php } ?>
            </td></tr>-->
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="<?=$this->lang->line('prefs_save')?>" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('company_preferences_edit', 'mainPanel', '<?=$this->lang->line('prefs_save')?>');
	document.company_preferences_edit.name.focus();
</script>
