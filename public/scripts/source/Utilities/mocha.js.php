<?php

/*

Script: mocha.js.php
	Dynamically concatenate source javascript files during development.
	
Copyright:
	Copyright (c) 2007-2009 Greg Houston, <http://greghoustondesign.com/>.		
	
Note:
	This is not recommended for live sites since it does not cache, compress or gzip the outputted file. 	 

Use:
	In the source code, edit the files you wish to merge. By default all the javascript files in the source directory are merged. When your site is ready to go live, it is recommended that you take the output of this file, compress it and rename it "mocha.js".

Example:
	(start code)
	<script type="text/javascript" src="scripts/source/Utilities/mocha.js.php"></script>
	(end)

*/

// Array of files to merge
$files = array(
	'Core/Core.js',
	'Utilities/Themes.js',
	'Window/Window.js',
	'Window/Modal.js',
	'Window/Windows-from-html.js',
	'Window/Windows-from-json.js',
	'Window/Arrange-cascade.js',
	'Window/Arrange-tile.js',
	'Components/Tabs.js',
	'Layout/Layout.js',
	'Layout/Dock.js',
	'Layout/Workspaces.js'
);

// Get the path to your web directory
$docRoot = dirname(__FILE__);
while (preg_match('/\\\\/',$docRoot)) {$docRoot = preg_replace('/\\\\/','/',$docRoot);}
while (preg_match('/\/\//',$docRoot)) {$docRoot = preg_replace('/\/\//','/',$docRoot);}
$docRoot = preg_replace('/\/$/','',$docRoot);
$docRoot = preg_replace('/\/Utilities$/','',$docRoot);

// Merge code
$code = '';
foreach ($files as $file) {
	$code .= file_get_contents("$docRoot/$file");
}

$filename = "mocha.js";	 

// Send HTTP headers
header("Cache-Control: must-revalidate");
header("Content-Type: text/javascript");
header('Content-Length: '.strlen($code));
header("Content-Disposition: inline; filename=$filename");

// Output merged code
echo $code;

?>