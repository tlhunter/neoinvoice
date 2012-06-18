<h1 class="panelTitle">Teammate: <?=$user['name']?></h1>
<h2 style="float: left; margin: 10px 0 0 10px;"><?=$user['name']?> <span>(<?=$user['username']?>)</span> <a href="mailto:<?=$user['email']?>"><?=$user['email']?></a></h2>
<?php if ($toolbar) { ?>
<div class="mini_toolbar"><a onClick="nw('detailPanel', 'user/edit/<?=$user['id']?>')" title="Edit"><img src="images/icons/user_edit.png" alt="" /></a><a onClick="nw('detailPanel', 'user/permission/<?=$user['id']?>')" title="Permissions"><img src="images/icons/permission.png" alt="" /></a><a onClick="nw('detailPanel', 'user/delete/<?=$user['id']?>')" title="Delete"><img src="images/icons/user_delete.png" alt="" /></a></div>
<?php } ?>
<div style="clear: both;"></div>
<div class="panelContentPad">
	<?php if (!$user['active']) { ?>
	<div class="info"><small>Inactive Teammate</small></div>
	<?php } ?>
	<p>Created on <?=date($this->lang->line('date_format'), strtotime($user['created']))?> (<?=time_ago(strtotime($user['created']))?> ago).</p>
	<p>Last action on <?=date($this->lang->line('date_format'), strtotime($user['modified']))?> (<?=time_ago(strtotime($user['modified']))?> ago).</p>
</div>