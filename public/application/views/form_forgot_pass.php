<div class="grid_12">
	<h2>Forgot Password</h2>
	<p>Fill out this form if you've forgotten your password. A new one will be emailed to you.</p>
	<?php if (isset($error)) echo "<div class='error'>$error</div>\n"; ?>
	<form id="lost-password" name="lost-password" method="post" action="login/forgot/execute">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<td><input name="email" type="text" id="email" maxlength="50" onclick="this.value=''" value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : "Email Address"; ?>" /></td>
				<td>&nbsp;</td>
				<td><input type="submit" name="button" id="button" value="Email Password" /></td>
			</tr>
		</table>
	</form>
</div>
<div class="clear"></div>