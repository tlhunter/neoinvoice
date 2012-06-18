<?php
class Multicache {
	function __construct() {
		$this->config['memcache_servers'][] = array('host' => '127.0.0.1', 'port' => 11211);
		$this->connected_servers = array();
		$this->connect();
	}

	private function connect() {
		switch (CACHE_SYSTEM) {
			case 'memcache':
				$this->memcache = new Memcache;
				$this->connect_memcache();
			break;

			case 'apc':
				# APC doesn't require a connection
			break;
		}
	}

	private function connect_memcache() {
		if (!empty($this->config['memcache_servers'])) {
			#$error_display = ini_get('display_errors');
			#$error_reporting = ini_get('error_reporting');

			#ini_set('display_errors', "Off");
			#ini_set('error_reporting', 0);

			foreach ($this->config['memcache_servers'] as $server) {
				if ($this->memcache->addServer($server['host'], $server['port'])) {
					$this->connected_servers[] = $server;
				}
			}

			#ini_set('display_errors', $error_display);
			#ini_set('error_reporting', $error_reporting);
		} else {
			die("Empty Server List");
		}
	}

	function get($key) {
		switch (CACHE_SYSTEM) {
			case 'memcache':
				if (empty($this->connected_servers)) {
					return false;
				}
				return $this->memcache->get($key);
			break;

			case 'apc':
				return apc_fetch($key);
			break;
		}
	}

	function set($key, $data, $expire = 0) {
		switch (CACHE_SYSTEM) {
			case 'memcache':
				if (empty($this->connected_servers)) {
					die("Not connected to a server!");
					return false;
				}
				return $this->memcache->set($key, $data, 0, $expire);
			break;

			case 'apc':
				apc_store($key, $data, $expire);
			break;
		}
	}

	function replace($key, $data, $expire = 0) {
		switch (CACHE_SYSTEM) {
			case 'memcache':
				if (empty($this->connected_servers)) {
					return false;
				}
				return $this->memcache->replace($key, $data, 0, $expire);
			break;

			case 'apc':
				apc_delete($key);
				apc_store($key, $data, $expire);
			break;
		}
	}

	function delete($key, $when = 0) {
		switch (CACHE_SYSTEM) {
			case 'memcache':
				if (empty($this->connected_servers)) {
					return false;
				}
				return $this->memcache->delete($key, $when);
			break;

			case 'apc':
				apc_delete($key);
			break;
		}
	}
}