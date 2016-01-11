<?php
class MemcacheUtil {
	public static function getInstance() {
		if(class_exists('Memcache') === false) {
			return new self();
		}else {
			return new Memcache();
		}
	}
	
	private function __construct() {
		if(!defined('MEMCACHE_COMPRESSED')) {
			define('MEMCACHE_COMPRESSED',0);
		}
	}
	
	public function pconnect() {
		return false;
	}
	
	public function get() {
		return false;
	}
	
	public function set() {
		return false;
	}
}
