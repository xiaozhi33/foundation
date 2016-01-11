<?php
/**
 *
 * @author lyb
 * @version 1.0
 */

/**
 * SearchHelper 
 * 
 * @uses
 */
require_once 'util/MemcacheUtil.php';

class SearchUtils {
	/**
	 * @var _searchService
	 */
	private static $_searchService = null;
	
	/**
	 * Get View
	 */
	public static function getView($viewName) {
		$config = Zend_Registry::get('config');
		$qrservers = $config->sfe->qrservers;
		try {
			$wrapper = new Java ("no.fast.search.FastESPWrapper");
			$wrapper->setQrservers ($qrservers);
			$view = $wrapper->getView ($viewName);
		} catch (JavaException $e) { 
			print_r($e);
		}
        return $view;
    }
	
	/**
	 * Get searchService
	 */
	public static function getSearchService()
    {
        if (self::$_searchService === null) {
        	self::createSearchService();
        }

        return self::$_searchService;
    }
    
	/**
	 * Create searchService
	 */
    protected static function createSearchService()
    {
    	//require_once 'Zend/Registry.php';
    	$config = Zend_Registry::get('config');
		$qrservers = $config->sfe->qrservers;
    	self::$_searchService = new Java('com.fastsearch.espimpl.sfeapi.searchservice.SearchServiceImpl', $qrservers);
    }
    
    public static function htmlEncode($value)
    {
        $value = str_replace('<', '&lt;', $value);
        $value = str_replace('>', '&gt;', $value);
        $value = str_replace('"', '&quot;', $value);
        $value = str_replace('&', '&amp;', $value);
        return $value;
    }
    
    private static function getHotsFromDB() {
		try {
			$hotsCount = FASTConfig::HOTCOUNT;
			$host = FASTConfig::DBHOST;
			$port = FASTConfig::DBPORT;
			$dbname = FASTConfig::DBNAME;
			$user = FASTConfig::DBUSER;
			$password = FASTConfig::DBPASS;
			$conn_string = "host=$host dbname=$dbname user=$user password=$password port=$port";
			$dbconn = pg_connect($conn_string);
			if($dbconn === false) {
				throw new Exception('pgsql 无法连接！',500);
			}
			//if ($dbconn){echo 'ok';}else{echo 'fales';};print_r(pg_version());
			$periodLength = FASTConfig::PERIOD;
			$query = "select query,sum(numqueries) as count from querystatistics where view='".FASTConfig::SEARCHVIEW."' and periodlength='hour' and category=1 and startdate>=(current_date-interval '$periodLength') group by query order by count desc limit $hotsCount";
			$rs=pg_query($dbconn,$query);
			$hots = array();
			while(($row = pg_fetch_object($rs))) {
				$hots[$row->query] = $row->count;
			}
			pg_close($dbconn);
			return $hots;
		}catch(Exception $e) {
			throw $e;
		}
    }
    
    public static function getHotsArray() {
		try {
			$memcacheObj = MemcacheUtil::getInstance();
			$memcacheObj->pconnect('localhost', 11211);
			$hotsArray = $memcacheObj->get('hots');
			if($hotsArray === false) {
				$hotsCount = FASTConfig::HOTCOUNT;
				
				$temp = explode(",", FASTConfig::BLACKHOTS);
				$blackHots = array();
				foreach($temp as $v) {
					if(!empty($v)) {
						$blackHots[] = $v;
					}
				}
				
				$array = $blackHots;
				
				$temp = require_once __SITEPATH__ . '/application/configs/hotWordsConfig.php';
				$whiteHots = array();
				foreach($temp as $v) {
					if(!empty($v)) {
						$whiteHots[] = $v;
						$array[] = $v;
					}
				}
				
				$hotsArray = $whiteHots;
				$i = count($whiteHots);
				if($i < $hotsCount) {
					$hots = self::getHotsFromDB();
					foreach ($hots as $key => $value) {
						if ($i >= $hotsCount) {
							break;
						}
						$i++;
						if (!in_array($key, $array)) {
							$hotsArray[] = $key;
						}
					}
				}
				
				$memcacheObj->set('hots', $hotsArray, MEMCACHE_COMPRESSED, 3600);
			}
			
			return $hotsArray;
		}catch(Exception $e) {
			throw $e;
		}
    }
    public static function getHotsDocvector() {
    	$config = Zend_Registry::get('config');
    	$hots = self::getHotsFromDB();
		$hotsCount = $config->sfe->hots->count;
		$whiteHots = $config->sfe->white->hots;
		$whiteHots = explode(",", $whiteHots);
		foreach ($whiteHots as $key) {
    		$hotsText .= '['.$key.',1]';
		}
		$blackHots = $config->sfe->black->hots;
		$array = explode(",", $blackHots) + $whiteHots;
		$first = true;
		$i = count($whiteHots);
		foreach ($hots as $key => $value) {
			if ($i >= $hotsCount) {
				break;
			}
			if (!in_array($key, $array)) {
				if ($first) {
					$first = false;
					$max = $value;
				}
    			$hotsText .= '['.$key.','.$value/$max.']';
			}
		}
		return $hotsText;
    }
}
