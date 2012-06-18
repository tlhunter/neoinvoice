<h1 class="panelTitle">Maximum Teammates</h1>
<div class="error">
	Sorry, but your current plan <strong><?=$company['service']['name']?></strong>, only allows for a maximum of <?=$company['service']['pref_max_user']?>, and you already have <?=$company['user_count']?> registered users.<br />
	<br />
	If you would like to add more users to your account, please visit the <a href="nw('mainPanel', 'company/upgrade');">Upgrade Company Account</a> screen.
</div>