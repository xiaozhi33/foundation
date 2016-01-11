<?php
require_once 'Zend/Cache.php';
class cacheFactory{
	private static $cacheList = null;
	public static function factoryCategory(){
		$backendName = 'File';
		$frontendName = 'Output';
		$frontendOptions = array(
			'lifetime'     					=> null, 
/*			'debug_header'	 				=> false,
//			'default_options'				=> array(
//				'cache_with_get_variables'		=> true
//				
//			),
			'memorize_headers'					=> array(
			)*/
		);
		$backendOptions = array(
			'cache_dir'    => __CACHEPATH__.'/all/'
		);
		//print_r($backendOptions);
		$cache = Zend_Cache::factory($frontendName, $backendName, $frontendOptions, $backendOptions);
		return $cache;
	}
}
?>