var errorColor = '#f4b240';
var correctColor = '#fff';

function validateForm(formHandle) {
	$('#input_company_name, #input_your_name, #input_username, #input_email, #input_password, #input_password2, #input_coupon').blur();
	if (formHandle.company_name_valid.value == '0') {
		alert("Fix Company Name to continue");
		return false;
	} else if (formHandle.your_name_valid.value == '0') {
		alert("Fix Your Name to continue");
		return false;
	} else if (formHandle.username_valid.value == '0') {
		alert("Fix User Name to continue");
		return false;
	} else if (formHandle.email_valid.value == '0') {
		alert("Fix Email Address to continue");
		return false;
	} else if (formHandle.password_valid.value == '0') {
		alert("Fix Password to continue");
		return false;
	} else if (formHandle.password2_valid.value == '0') {
		alert("Fix Password to continue");
		return false;
	} else if (formHandle.coupon_valid.value == '0') {
		alert("Fix Coupon to continue");
		return false;
	}
	return true;
}

function validateEmpty(fld) {
	var error = "";
	if (fld.value.length == 0) {
		fld.style.background = errorColor; 
		error = "The required field has not been filled in.\n"
		document.getElementById(fld.name+"_valid").value = 0;
	} else {
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	}
	document.getElementById('formErrorMessage').innerHTML = error;
}

function validateUsername(fld) {
	var error = "";
	var illegalChars = /\W/; // a-z, A-Z, 0-9, _
	if (fld.value == "") {
		fld.style.background = errorColor; 
		error = "Please enter a username.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (fld.value.length < 5) {
		fld.style.background = errorColor; 
		error = "Username should be at least 5 characters.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (fld.value.length > 32) {
		fld.style.background = errorColor;
		error = "Username is too long.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (illegalChars.test(fld.value)) {
		fld.style.background = errorColor; 
		error = "Username contains invalid characters.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else {
		//xhr('signup_check.php?field=username&value=' + fld.value, fld);
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	}
	document.getElementById('formErrorMessage').innerHTML = error;
}

function validateUsernameDatabase(fld) {
	if(document.getElementById(fld.name+"_valid").value == 1) {
		xhr('signup_check.php?field=username&value=' + fld.value, fld);
	}
}

function validatePassword(fld) {
	var error = "";
	var legalChars = /[a-zA-Z0-9_!@#$%\^&\(\)\[\]{};:'",\<\.\>`~\\\/\?\-=\+]/; // a-z, A-Z, 0-9, _
	if (fld.value == "") {
		fld.style.background = errorColor;
		error = "Please enter a password.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (fld.value.length < 7) {
		error = "Password should be at least 7 characters.\n";
		fld.style.background = errorColor;
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (!legalChars.test(fld.value)) {
		error = "Password contains illegal characters.\n";
		fld.style.background = errorColor;
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (!((fld.value.search(/(a-z)+/)) && (fld.value.search(/(0-9)+/)))) {
		error = "Password must contain at least one numeral.\n";
		fld.style.background = errorColor;
		document.getElementById(fld.name+"_valid").value = 0;
	} else {
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	}
   document.getElementById('formErrorMessage').innerHTML = error;
}

function validateConfirmPassword(fld, pwFld) {
	var error = "";
	if (fld.value == "") {
		fld.style.background = errorColor;
		error = "Please enter a password.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (fld.value != document.getElementById(pwFld).value) {
		fld.style.background = errorColor;
		error = "Passwords do not match.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else {
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	}
   document.getElementById('formErrorMessage').innerHTML = error;
}
	
function validateEmail(fld) {
	var error="";
	var tfld = trim(fld.value);
	var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/;
	var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/;
   
	if (fld.value == "") {
		fld.style.background = errorColor;
		error = "You didn't enter an email address.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (!emailFilter.test(tfld)) {
		fld.style.background = errorColor;
		error = "Please enter a valid email address.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (fld.value.match(illegalChars)) {
		fld.style.background = errorColor;
		error = "The email address contains illegal characters.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else {
		//xhr('signup_check.php?field=email&value=' + fld.value, fld);
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	}
	document.getElementById('formErrorMessage').innerHTML = error;
}

function validateEmailDatabase(fld) {
	if(document.getElementById(fld.name+"_valid").value == 1) {
		xhr('signup_check.php?field=email&value=' + fld.value, fld);
	}
}

function validateCoupon(fld) {
	var error = "";
	var illegalChars = /\W/; // a-z, A-Z, 0-9, _
	if (illegalChars.test(fld.value)) {
		fld.style.background = errorColor;
		error = "Supplied coupon code isn't alphanumeric.\n";
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (fld.value == "") {
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	} else {
		xhr('signup_check.php?field=coupon&value=' + fld.value, fld);
		fld.style.background = correctColor;
		document.getElementById(fld.name+"_valid").value = 1;
	}
	document.getElementById('formErrorMessage').innerHTML = error;
}

function trim(s) {
	return s.replace(/^\s+|\s+$/, '');
}

function xhr(strURL, fld) {
    var xmlHttpReq = false;
    var self = this;
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('GET', strURL, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
        if (self.xmlHttpReq.readyState == 4) {
            xhrCallback(self.xmlHttpReq.responseText, fld);
        }
    }
    self.xmlHttpReq.send();
}

function xhrCallback(response, fld) {
	if (response == '0') {
		fld.style.background = errorColor;
		if (fld.name == "coupon") {
			document.getElementById('formErrorMessage').innerHTML = "Invalid Coupon";
		} else {
			document.getElementById('formErrorMessage').innerHTML = "Sorry, but this is taken";
		}
		document.getElementById(fld.name+"_valid").value = 0;
	} else if (response == '1') {
		fld.style.background = correctColor;
		document.getElementById('formErrorMessage').innerHTML = "";
		document.getElementById(fld.name+"_valid").value = 1;
	} else {
		fld.style.background = errorColor;
		document.getElementById('formErrorMessage').innerHTML = response;
		document.getElementById(fld.name+"_valid").value = 0;
	}
}