<div class="grid_7">
<h1><span>Contact NeoInvoice</span></h1>
<h4>Have a question? Send NeoInvoice administrators an email.</h4>
<?=$message?>
<form method="post" action="contact/send">
<table id="contact_table">
<tr><td align="right"><label for="contact-name">Name</label></td><td>&nbsp;</td><td><input name="name" id="contact-name" value="<?=$name?>" /></td></tr>
<tr><td align="right"<label for="contact-email">Email</label></td><td>&nbsp;</td><td><input name="email" id="contact-email" value="<?=$email?>" /></td></tr>
<tr><td align="right"><label for="contact-phone">Phone</label></td><td>&nbsp;</td><td><input name="phone" id="contact-phone" /></td></tr>
<tr><td align="right"><label for="contact-inquiry">Inquiry</label></td><td>&nbsp;</td><td>
		<select name="inquiry" id="contact-inquiry">
			<option value="unknown">Please select an option...</option>
			<option value="Pre Sales">Pre Sales</option>
			<option value="Bug Report">Bug Report or other issue</option>
			<option value="Testimonial">NeoInvoice Testimonial</option>
			<option value="Feature">Feature Request</option>
			<option value="Affiliate">Affiliation Request</option>
		</select>
	</td></tr>
<tr><td valign="top" align="right"><label for="contact-message">Message</label></td><td>&nbsp;</td><td><textarea name="message" id="contact-message" style="width: 380px; height: 150px; font-family: arial; font-size: 12px;" rows="5" cols="20"></textarea></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td><input type="submit" value="Send Email" /></td></tr>
</table>
<input type="hidden" name="action" value="commit" />
</form>
</div>
<div class="grid_5">
	<div class="contact-callout">
		<p>Are you having an issue with NeoInvoice, would like to ask some pre sales questions, or would like to submit a testimonial? This form is the place to do so.</p>
		<p>When specifying an issue, please be sure to tell us what panel of the application is having the error, and what you were doing to cause the error to occur. If the error has a number or any text, please paste this information.</p>
		<p>Also, use this form if you would like to become an affiliate. We'll give you a call and discuss how the affiliate system works and come up with a plan that makes both of us happy!</p>
	</div>
</div>
<div class="clear"></div>
