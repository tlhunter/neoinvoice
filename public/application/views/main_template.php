<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=$title?></title>
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>css/960.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url();?>/scripts/facebox/facebox.css" media="screen" />

	<link rel="alternate" type="application/rss+xml" title="NeoInvoice Documentation RSS Feed" href="<?=base_url();?>docs/feed/" />
	<link rel="alternate" type="application/atom+xml" title="NeoInvoice Documentation Atom Feed" href="<?=base_url();?>docs/feed/atom/" />

	<link href='http://fonts.googleapis.com/css?family=Podkova&v2' rel='stylesheet' type='text/css'>

	<script type="text/javascript" src="<?=base_url();?>scripts/jquery.js"></script>
	<script type="text/javascript" src="<?=base_url();?>scripts/jquery.scrollto.js"></script>
	<script type="text/javascript" src="<?=base_url();?>scripts/slider.js"></script>
	<script type="text/javascript" src="<?=base_url();?>/scripts/facebox/facebox.js"></script>

	<script type="text/javascript">
	$(function() {
		$('a[rel*=facebox]').facebox();
	});
	</script>

	<meta name="description" content="NeoInvoice provides a free and easy to use Web 2.0 interface for keeping track of all your billable hours and invoicing needs." />
	<base href="<?=base_url();?>" />
	<meta name="title" content="<?=addslashes($title)?>" />
	<link rel="image_src" type="image/jpeg" href="http://www.neoinvoice.com/images/site-thumbnail.jpg" />
	<link rel="shortcut icon" type="image/png" href="favicon.ico" />
	<!--[if IE 6]>
	<script type="text/javascript">
		var IE6UPDATE_OPTIONS = {
			icons_path: "ie6update/images/"
		}
	</script>
	<script type="text/javascript" src="<?=base_url();?>ie6update/ie6update.js"></script>
	<![endif]-->
</head>
<body id="<?=$this->uri->segment(1)?>_<?=$this->uri->segment(2)?>" class="<?=$body_class?>">
<div id="header">
	<a href="home" id="logo" title="Home"><span class="inside">NeoInvoice</span></a>
	<div id="navigation">
		<?=$navigation?>
	</div>
</div>
	
<?=$splash?>

<?php if ($display_login_bar && !$loggedin) { ?>
	<div id="login-wrapper">
		<div class="inner">
			<div id="login_form" class="container_12">
				<div class="grid_5 login-left"><a href="login/register">Create Account</a> | <a href="login/forgot">Forgot Password</a></div>
				<div class="grid_7 login-right">
					<form method="post" action="login/auth" id="login_go" name="login_go">
						<label for="login-username">Username:</label> <input name="login" size="10" id="login-username" /> &nbsp;&nbsp;&nbsp;
						<label for="login-password">Password:</label> <input name="password" size="10" id="login-password" type="password" /> &nbsp;&nbsp;&nbsp;
						<input type="submit" value="Login" />
					</form>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php } ?>
	<div id="content-wrapper">
		<div class="container_12" id="content">
<?=$contents?>
		</div>
	</div>
	<div id="footer-wrapper">
		<div class="container_12" id="footer">
<?=$footer?>
		</div>
	</div>
<?=$analytics?>
</body>
</html>
