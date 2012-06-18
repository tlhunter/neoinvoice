<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$autoload['libraries'] =	array('multicache', 'session', 'database', 'rm_user');

if ($_SERVER['SERVER_NAME'] == 'localhost') {
	array_push($autoload['libraries'], 'firephp');
}

$autoload['helper'] =		array('url', 'generic', 'cookie');

$autoload['plugin'] =		array();

$autoload['config'] =		array();

$autoload['language'] =		array('blah');

$autoload['model'] =		array('security_model', 'user_model');
