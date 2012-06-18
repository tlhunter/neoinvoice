<script type="text/javascript" src="scripts/jquery.cross-slide.min.js"></script>
<script type="text/javascript">
$(function() {
	$('#register-slideshow').crossSlide(
		{
			sleep: 4,
			fade: 1
		},
		[
			{ src: 'images/register-1.png' },
			{ src: 'images/register-2.png' },
			{ src: 'images/register-3.png' },
			{ src: 'images/register-4.png' },
			{ src: 'images/register-5.png' },
			{ src: 'images/register-6.png' },
			{ src: 'images/register-7.png' },
			{ src: 'images/register-8.png' },
			{ src: 'images/register-9.png' }
		]
	);
});
</script>
<div class="grid_6">
	<h1>Sign-Up for NeoInvoice</h1>
	<h4>Use this form to create a new business and user account on NeoInvoice.com.</h4>
	<script type="text/javascript" src="<?=base_url();?>scripts/validation.js"></script>
	<?php if (isset($error)) echo "<div class='error'>$error</div>\n"; ?>
	<form id="register" name="register" method="post" action="login/register" onsubmit="return validateForm(this)">
	  <table border="0" cellpadding="2" id="signup_form">
		<tr>
		  <td class="label_column"><label for="input_company_name">Company Name</label></td>
		  <td><input type="hidden" name="company_name_valid" id="company_name_valid" value="0" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="company_name" onblur="validateEmpty(this)" class="text" type="text" id="input_company_name" maxlength="127" value="<?php echo isset($_POST['company_name']) ? htmlentities($_POST['company_name']) : ""; ?>" /></td>
		</tr>
		<tr>
		  <td class="label_column"><label for="input_your_name">Your Name</label></td>
		  <td><input type="hidden" name="your_name_valid" id="your_name_valid" value="0" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="your_name" onblur="validateEmpty(this)" class="text" type="text" id="input_your_name" maxlength="64" value="<?php echo isset($_POST['your_name']) ? htmlentities($_POST['your_name']) : ""; ?>" /></td>
		</tr>
		<tr>
		  <td class="label_column"><label for="input_username">Your Username</label></td>
		  <td><input type="hidden" name="username_valid" id="username_valid" value="0" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="username" onblur="validateUsername(this); validateUsernameDatabase(this);" class="text" type="text" id="input_username" maxlength="32" value="<?php echo isset($_POST['username']) ? htmlentities($_POST['username']) : ""; ?>" /></td>
		</tr>
		<tr>
		  <td class="label_column"><label for="input_email">Your Email</label></td>
		  <td><input type="hidden" name="email_valid" id="email_valid" value="0" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="email" onblur="validateEmail(this); validateEmailDatabase(this)" class="text" type="text" id="input_email" maxlength="64" value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : ""; ?>" /></td>
		</tr>
		<tr>
		  <td class="label_column"><label for="input_password">Password</label></td>
		  <td><input type="hidden" name="password_valid" id="password_valid" value="0" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="password" onblur="validatePassword(this)" class="text" id="input_password" type="password" /></td>
		</tr>
		<tr>
		  <td class="label_column"><label for="input_password2">Confirm Password</label></td>
		  <td><input type="hidden" name="password2_valid" id="password2_valid" value="0" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="password2" onblur="validateConfirmPassword(this, 'input_password')" class="text" id="input_password2" type="password" /></td>
		</tr>
		<tr>
		  <td class="label_column"><label for="input_coupon">Coupon Code</label></td>
		  <td><input type="hidden" name="coupon_valid" id="coupon_valid" value="1" type="hidden" />&nbsp;</td>
		  <td class="input_column"><input name="coupon" onblur="validateCoupon(this)" class="text" id="input_coupon" value="<?=isset($coupon) ? htmlentities($coupon) : '' ?>" /></td>
		</tr>
		<tr>
		  <td class="label_column">&nbsp;</td>
		  <td>&nbsp;</td>
		  <td class="input_column" align="center"><input type="image" src="images/register.png" name="button" id="button" value="Create your NeoInvoice account!" /></td>
		</tr>
		<tr>
		  <td class="label_column">&nbsp;</td>
		  <td>&nbsp;</td>
		  <td class="input_column">By signing up you agree to our <a href="tos" target="_blank">Terms and<br />Conditions</a>.</td>
		</tr>
	  </table>
		<input type="hidden" name="gotcha" value="99" id="gotcha" />
		<script type="text/javascript">
			$('#gotcha').val('55');
		</script>
	</form>
	<div id="formErrorMessage"></div>
</div>
<div class="grid_6">
	<div id="register-slideshow" style="width: 450px; height: 220px; margin-top: 20px;">Loading</div>
	<div class="register-callout">
		<p>You are one step closer to running a more efficient business! Gone are the days of manually taking notes of what projects you're working on, who worked on what, etc.</p>
		<p><b>Your account is 100% free! </b>During the Public Beta period we are giving away <em>permanently</em> free accounts. Sign up quick; once we leave Beta status new NeoInvoice accounts will cost money!</p>
		<p><em>Note: Do not use this form if your company already has an account, instead wait for an administrator to create you an account.</em></p>
	</div>
</div>
<div class="clear"></div>
