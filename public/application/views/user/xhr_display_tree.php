<h1 class="panelTitle">Teammates</h1>
<ul id="display_tree_teammates" class="tree">
<?php
$first = true;
foreach($teammate_groups AS $teammate_group_name => $teammate_group) {
?>
	<li class="folder f-open<?=$first ? ' first' : ''?>"><span><?=$teammate_group_name ? : 'No Group'?></span>
		<ul>
<?php
foreach($teammate_group AS $teammate) {
	$class = $teammate['active'] ? 'active-user' : 'inactive-user';
	echo "\t\t\t<li class=\"doc\" data-tree-icon=\"user\"><span><a onclick=\"selectUser({$teammate['id']});\" class=\"$class\">{$teammate['name']}</a></span></li>\n";
}
?>
		</ul>
	</li>
<?php
$first = false;
}
?>
</ul>
<?php if (!isset($no_tree) || !$no_tree) { ?>
<script type="text/javascript">
	if (typeof(buildTree) != "undefined")
		buildTree('display_tree_teammates');
</script>
<?php } ?>
<div class="hover-options"><a onClick="refreshTeammatePanel();">Reload Panel</a></div>