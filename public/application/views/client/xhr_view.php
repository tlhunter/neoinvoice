<h1 class="panelTitle">Client: <?=$client['name']?></h1>

<div class="mini_toolbar"><a onClick="nw('detailPanel', 'client/edit/<?=$client['id']?>')" title="Edit"><img src="images/icons/client_edit.png" /></a><a onClick="nw('detailPanel', 'client/delete/<?=$client['id']?>')" title="Delete"><img src="images/icons/client_delete.png" /></a><a href="mailto:<?=$client['email']?>" title="E-Mail"><img src="images/icons/email.png" /></a></div>
<h2 style="float: left; margin: 10px 0 0 10px;"><?=$client['name']?></h2>
<div style="clear: both;"></div>
<div class="panelContentPad">
	<p>Created on <?=date($this->lang->line('date_format'), strtotime($client['created']))?> (<?=time_ago(strtotime($client['created']))?> ago).</p>
	<p>Last modified on <?=date($this->lang->line('date_format'), strtotime($client['modified']))?> (<?=time_ago(strtotime($client['modified']))?> ago).</p>
	<p>Your company has recorded a total of <?=$client['total_time']?> hours of work for this project over <?=$client['segment_count']?> time segments.</p>
</div>