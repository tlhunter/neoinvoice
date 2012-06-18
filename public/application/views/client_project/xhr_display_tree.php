<h1 class="panelTitle">Projects &amp; Clients</h1>
<ul id="display_tree_projects" class="tree">
	<li class="folder f-open first"><span><a onclick="nw('mainPanel', 'project/list_items');"><?=$this->lang->line('projects')?></a></span>
		<ul>
			<li class="folder f-open first"><span><?=$this->lang->line('active')?></span>
				<ul>
<?php
foreach($projects AS $project) {
	if ($project['active'])
		echo "\t\t\t\t\t<li class=\"doc\" data-tree-icon=\"project\"><span><a onclick=\"selectProject({$project['id']});\">{$project['name']}</a></span></li>\n";
}
?>
				</ul>
			</li>
			<li class="folder f-close"><span><?=$this->lang->line('inactive')?></span>
				<ul>
<?php
foreach($projects AS $project) {
	if (!$project['active'])
		echo "\t\t\t\t\t<li class=\"doc\" data-tree-icon=\"project\"><span><a onclick=\"selectProject({$project['id']});\">{$project['name']}</a></span></li>\n";
}
?>
				</ul>
			</li>
		</ul>
	</li>
	<li class="folder f-open"><span><a onclick="nw('mainPanel', 'client/list_items');"><?=$this->lang->line('clients')?></a></span>
		<ul>
			<li class="folder f-open first"><span><?=$this->lang->line('active')?></span>
				<ul>
<?php
foreach($clients AS $client) {
	if ($client['active'])
		echo "\t\t\t\t\t<li class=\"doc\" data-tree-icon=\"client\"><span><a onclick=\"selectClient({$client['id']});\">{$client['name']}</a></span></li>\n";
}
?>
				</ul>
			</li>
			<li class="folder f-close"><span><?=$this->lang->line('inactive')?></span>
				<ul>
<?php
foreach($clients AS $client) {
	if (!$client['active'])
		echo "\t\t\t\t\t<li class=\"doc\" data-tree-icon=\"client\"><span><a onclick=\"selectClient({$client['id']});\">{$client['name']}</a></span></li>\n";
}
?>
				</ul>
			</li>
		</ul>
	</li>
</ul>
<?php if (!isset($no_tree) || !$no_tree) { ?>
<script type="text/javascript">
	if (typeof(buildTree) != "undefined")
		buildTree('display_tree_projects');
</script>
<?php } ?>
<div class="hover-options"><a onClick="refreshProjectPanel();">Reload Panel</a></div>