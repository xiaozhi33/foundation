<?php

require_once 'sde/fastsearch/SearchFactory.php';
require_once 'sde/fastsearch/SearchException.php';
require_once 'sde/fastsearch/query/Query.php';
require_once 'sde/fastsearch/result/QueryTransformationName.php';
require_once 'util/MemcacheUtil.php';

//搜索封装
class FastESPWrapper {
	
	private $params;
	private $docCount;
	private $errorMsg;
	private $temp_navs;
	
	public function getParams() {
		return $this->params;
	}

	public function setParams($params) {
		$this->params = $params;
	}
	
	public function getDocCount() {
		return $this->docCount;
	}
	
	public function getErrorMsg() {
		return $this->errorMsg;
	}
	
	private function initView($viewName) {
		$param = array (
			'fastsearch.SearchFactory' => "http.HttpSearchFactory",
			'fastsearch.http.qservers' => FASTConfig::HOST . ':' . FASTConfig::PORT
		);
		$searchFactory = SearchFactory::newInstance($param);
		return $searchFactory->getSearchView($viewName);
	}
	
	public function search() {
		try {
			$view = $this->initView($this->params->getView());
			$query = new Query();
			$hitsPerPage = $this->params->getHitsPerPage();
			if(empty($hitsPerPage)) {
				$query->setParameterByType(BaseParameter::HITS,FASTConfig::HITS);
				$this->params->setHitsPerPage(FASTConfig::HITS);
			}else {
				$query->setParameterByType(BaseParameter::HITS,$hitsPerPage);
			}
			$query->setParameterByName('language',FASTConfig::LANG);
			$query->setParameterByName('spell',FASTConfig::SPELL);
			$query->setParameterByName('rff_ddr:enabled',FASTConfig::DUPLICATIONREMOVAL);
			$slot1 = FASTConfig::SLOT1;
			$slot2 = FASTConfig::SLOT2;
			$slot1 = $this->params->getSlot1()?$this->params->getSlot1():$slot1;
			$slot2 = $this->params->getSlot2()?$this->params->getSlot2():$slot2;
			if(!empty($slot1)) {
				$query->setParameterByName('rff_ddr:slot1',$slot1);
			}
			if(!empty($slot2)) {
				$query->setParameterByName('rff_ddr:slot2',$slot2);
			}
			
			$query->setParameterByName('qtf_lemmatize','true');
			$queryString = $this->params->getQueryString();
			$advancedQueryStr = $this->params->getAdvancedQueryStr();
			if(!empty($advancedQueryStr)) {
				$query->setQueryString($advancedQueryStr);
			}elseif(empty($queryString)) {
				$queryStr = $this->params->getQuery();
				$queryStr = str_replace('：',':',$queryStr);
				//$queryStr = preg_replace('/[\"\'“”‘’]/u',"'",$queryStr);
				//$queryStr = preg_replace('/[\s　]+/u',' ',$queryStr);
				$matches = array();
				preg_match_all('/\'[^\']*\'/u',$queryStr,$matches);
				$matches = $matches[0];
				//if(!empty($matches)) {
				//	$replace_arr = array();
				//	foreach($matches as $v) {
				//		$replace_arr[] = str_replace(' ','{|}',$v);
				//	}
				//	$queryStr = str_replace($matches,$replace_arr,$queryStr);
				//}
				//
				//$queryStrArr = explode(' ',$queryStr);
				
				//$queryStr = 'and(';
				//foreach($queryStrArr as $k=>$v) {
				//	if($k > 0) {
				//		$queryStr .= ',';
				//	}
				//	$field = '';
				//	if(mb_stripos($v,':') !== false) {
				//		$field = mb_substr($v,0,mb_stripos($v,':') + 1);
				//		$v = mb_substr($v,mb_stripos($v,':') + 1);
				//	}
				//	
				//	//$queryStr .= $field . 'string("'.$v.'", mode="phrase", annotation_class="user")';
				//	if(mb_stripos($v,'range') !== false || !in_array($field,array('title:','source:','author:','content:','id:','size:'))) {
				//		$queryStr .= 'string("' . $field . $v.'", mode="phrase", annotation_class="user")';
				//	}else {
				//		if(in_array($field,array('title:','id:'))) {
				//			$v = str_replace('-*',' ',$v);
				//		}
				//		$queryStr .= $field . 'string("'.$v.'", mode="phrase", annotation_class="user")';
				//	}
				//}
				//$queryStr .=')';
				//$queryStr = str_replace('{|}',' ',$queryStr);
				
				$field = '';
				if(mb_stripos($queryStr,':') !== false) {
					$field = mb_substr($queryStr,0,mb_stripos($queryStr,':') + 1);
					$queryStr = mb_substr($queryStr,mb_stripos($queryStr,':') + 1);
				}
				
				if(mb_stripos($queryStr,'range') !== false || !in_array($field,array('title:','source:','author:','content:','id:','size:'))) {
					$queryStr = 'string("' . addslashes($field . $queryStr) . '", mode="simpleall", annotation_class="user")';
				}else {
					if(in_array($field,array('title:','id:'))) {
						$queryStr = str_replace('-*',' ',$queryStr);
					}
					$queryStr = $field . 'string("' . addslashes($queryStr) . '", mode="simpleall", annotation_class="user")';
				}
				
				$additionFQL = $this->params->getAdditionFQL();
				if(empty($additionFQL)) {
					$query->setQueryString($queryStr);
				}else {
					$query->setQueryString('(' . $queryStr . ') and (' . $additionFQL . ')');
				}
			}else {
				$fql = '';
				$terms = explode('],',$queryString);
				foreach($terms as $key=>$term) {
					$inners = explode(':[',$term);
					if(count($inners) == 2) {
						$s = strtolower($inners[1]);
						$s = preg_replace('/(^\(*(?!and|not|or|count|near)|(and|not|or) {0,1}(\(* {0,1}(and|not|or){0,1} {0,1}))((?!and|not|or|count|near).+?)(\)* |\)*$)/u','$1' . $inners[0] . ':string("$5", mode="simpleall", annotation_class="user")$6',$s);
						if($key != 0) {
							$fql .= ' and ';
						}
						$fql .= '(' . $s . ')';
					}elseif (!empty($term)) {
						$fql .= " and (" . $term . ")";
					}
				}
				
				$filterTerms = $this->params->getFilterTerms();
				if (!empty($filterTerms)) {
					$fql .= " and (filter(meta.collection:or(" . $filterTerms . ")))";
				}
				$query->setQueryString($fql);
			}
			$newsFilterId = $this->params->getNewsFilterId();
			if(!empty($newsFilterId)) {
				$temp_query = '';
				if(is_array($newsFilterId)) {
					foreach($newsFilterId as $v) {
						$temp_query .= ',not(id:string("' . $v . '", mode="simpleany", annotation_class="user"))';
					}
				}else {
					$temp_query .= ',not(id:string("' . $newsFilterId . '", mode="simpleany", annotation_class="user"))';
				}
				$query->setQueryString('and (' . $query->getQueryString() . $temp_query . ')');
			}
			$sources = $this->params->getSources();
			if(!empty($sources)) {
				$query->setQueryString('and(' . $query->getQueryString() . ',' . $sources . ')');
			}
			$query->setParameterByType(BaseParameter::OFFSET, $this->params->getOffset());
			$query->setParameterByType(BaseParameter::SORT_BY, $this->params->getSortBy());
			$query->setParameterByType(BaseParameter::NAVIGATION_FILTER, $this->params->getBreadcrumbs());
			$query->setParameterByName('timeout',30000);
			$query->setParameterByType(BaseParameter::NAVIGATION, $this->params->getIsShowNav());
			$result = $view->search($query);
			$this->docCount = $result->getDocCount();
			$this->temp_navs = $result->navigators();
			return $result;
		}catch(Exception $e) {
			throw new Exception('Search news error -> ' . $e->getMessage());
		}
		
	}
	
	public function getNavigation() {
		$navigator = array();
		$filter = array();
		$navigation = $this->params->getBreadcrumbs();
		try {
			if(!empty($this->temp_navs) && $this->temp_navs->valid()) {
				while($this->temp_navs->valid()) {
					$key = strtolower($this->temp_navs->current()->getDisplayName());
					$modIter = $this->temp_navs->current()->modifiers();
					$i = 0;
					while($modIter->valid() && $i < FASTConfig::NAVHITS) {
						$t_nav = $this->temp_navs->current()->getFieldName();
						$t_val = $modIter->current()->getValue();
						if(mb_stripos($navigation,$t_nav . ':' . $t_val,0,'utf-8') === false) {
							$navigator[$key]['mod'][] = array(
								'name'		=> $modIter->current()->getName(),
								'count'		=> $modIter->current()->getCount(),
								'nav'		=> $t_nav,
								'value'		=> $t_val
							);
							++$i;
						}else {
							$filter[$key]['mod'][] = array(
								'name'		=> $modIter->current()->getName(),
								'count'		=> $modIter->current()->getCount(),
								'nav'		=> $t_nav,
								'value'		=> $t_val
							);
						}
						
						$modIter->next();
					}
					$this->temp_navs->next();
				}
			}
		}catch(Exception $e) {
			throw new Exception('Get navigator error -> ' . $e->getMessage());
		}
		
		return array(
			'navigator' => $navigator,
			'filter'	=> $filter
		);
	}
	
	public function hotsSearch($hots,$isRelate = false) {
		try {
			$temp_data = false;
			
			if($isRelate === false) {
				$memcacheObj = MemcacheUtil::getInstance();
				$memcacheObj->pconnect('localhost', 11211);
				$temp_data = $memcacheObj->get('hotSearch');
			}
			
			if($temp_data === false) {
				$topicView = $this->initView('topicsppublished');
				$topicQuery = new Query();
				$topicQuery->setParameterByType(BaseParameter::HITS,FASTConfig::HOTTOPICHITS);
				if($isRelate) {
					$topicQuery->setParameterByType(BaseParameter::HITS,FASTConfig::RELATEHITS);
				}
				$topicQuery->setParameterByName('language',FASTConfig::LANG);
				$topicQuery->setParameterByName('spell',FASTConfig::SPELL);
				$topicQuery->setParameterByType(BaseParameter::OFFSET, 0);
				$topicQuery->setParameterByType(BaseParameter::SORT_BY, '-default');
				$topicQuery->setParameterByType(BaseParameter::NAVIGATION, 'false');
				$topicQuery->setQueryString("string(\"" . addslashes($hots) . "\", mode=\"simpleany\", annotation_class=\"user\")");
				if($isRelate) {
					$filterId = $this->params->getTopicFilterId();
					if(!empty($filterId)) {
						$topicQuery->setQueryString("and(string(\"" . addslashes($hots) . "\", mode=\"simpleany\", annotation_class=\"user\"),not(id:string(\"" . $filterId . "\", mode=\"simpleany\", annotation_class=\"user\")))");
					}
				}
				$topicQuery->setParameterByName('qtf_teaser:view',"nohigh");
				$topicQuery->setParameterByName('qtf_lemmatize','true');
				$temp_data = $topicView->search($topicQuery);
				
				if($isRelate === false) {
					$memcacheObj->set('hotSearch', $temp_data, MEMCACHE_COMPRESSED, 3600);
				}
			}
			
			return $temp_data;
		}catch(Exception $e) {
			if($isRelate) {
				throw new Exception('relatedSearch error -> ' . $e->getMessage());
			}else {
				throw new Exception('hotsSearch error -> ' . $e->getMessage());
			}
		}
		
	}
	
	public function relatedSearch() {
		try {
			$queryStr = $this->params->getQueryString();
			if(empty($queryStr)) {
				$queryStr = $this->params->getQuery();
				$queryStr = str_replace('：',':',$queryStr);
				$queryStr = preg_replace('/[\"\'“”‘’]/u',"\'",$queryStr);
				$queryStr = preg_replace('/[\s　]+/u',' ',$queryStr);
				
				$queryStrArr = explode(' ',$queryStr);
				
				$queryStr = '';
				foreach($queryStrArr as $k=>$v) {
					$field = '';
					if(mb_stripos($v,':') !== false) {
						$v = mb_substr($v,mb_stripos($v,':') + 1);
					}
					
					$queryStr .= ' ' . $v;
				}
			}else {
				$queryStr = preg_replace('/count\((.+?)\)[<>=]{1,2}\d+/u', '$1', $queryStr);
				$queryStr = preg_replace('/near\((.+?),n=\d+\)/u', '$1', $queryStr);
				$queryStr = preg_replace("/\(|\)/u", " ", $queryStr);
				$queryStr = preg_replace("/[^ ][a-zA-Z]+?:/u", "", $queryStr);
				$queryStr = preg_replace("/ not.+? /u", " ", $queryStr);
				$queryStr = preg_replace("/(^| )(and|or) /u", " ", $queryStr);
			}
			$additionRelateKeys = $this->params->getAdditionRelateKeys();
			if(!empty($additionRelateKeys)) {
				$queryStr = $queryStr . ' ' . $additionRelateKeys;
			}
			return $this->hotsSearch($queryStr,true);
		}catch(Exception $e) {
			throw new Exception('relatedSearch error -> ' . $e->getMessage());
		}
		
	}
	
	public function getPager($options = array()) {
		require_once 'Pager/Pager.php';
			
		$pagerOptions = array(
			'perPage'                   => FASTConfig::HITS,
			'delta'                     => 2,
			'mode'                      => 'sliding',
			'httpMethod'                => 'POST',
			'urlVar'                    => 'offset',
			'altPrev'                   => 'prev',
			'altNext'                   => 'next',
			'nextImg'                   => 'NEXT &gt;&gt;',
			'prevImg'                   => '&lt;&lt; PREV',
			'separator'                 => '',
			'spacesBeforeSeparator'     => 0,
			'spacesAfterSeparator'      => 1,
			'totalItems'				=> $this->docCount,
			'path'						=> str_replace('?' . $_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI'])
		);
			
		$pagerOptions = array_merge($pagerOptions,$options);
		
		$pager = Pager::factory($pagerOptions);
		
		$links = $pager->getLinks();
		$links['all'] = str_replace('/index.php','',$links['all']);
		return $links;
	}
	
	public function autoComplete() {
		try {
			$view = $this->initView($this->params->getView());
			$query = new Query();
			$hitsPerPage = $this->params->getHitsPerPage();
			if(empty($hitsPerPage)) {
				$query->setParameterByType(BaseParameter::HITS,FASTConfig::HITS);
				$this->params->setHitsPerPage(FASTConfig::HITS);
			}else {
				$query->setParameterByType(BaseParameter::HITS,$hitsPerPage);
			}
			$queryStr = strtolower($this->params->getQuery());
			$fql = 'query:' . $queryStr . '*';
			
			if(preg_match('/[a-z]+/u',$queryStr)) {
				$fql .= ' or pinyin:' . $queryStr . '*';
				$initials = "(zh|ch|sh|b|p|m|f|d|t|(?<!a|o|e|i|u)n|l|(?<!n)g|k|h|j|q|x|(?<!e)r|z(?!h)|c(?!h)|s(?!h)|y|w)";
				$replaces = array("iang", "iong", "ueng", "uang", "ang", "ian", "iao", "iou", "uai", "uan", "uen", "uei", "an", "ao", "ai", "ong", "ou", "eng", "en", "er", "ei", "ia", "ing", "iu", "ie", "in", "un", "ua", "uo", "ue", "ui", "ve", "vn", "a", "o", "e", "i", "u", "v");
				foreach($replaces as $replace) {
					$fql .= ' or pinyin:' . preg_replace('/' . $initials . $initials . '/u','$1' . $replace . '$2',$queryStr,1) . '*';
				}
			}
			
			$query->setQueryString($fql);
			$query->setParameterByType(BaseParameter::OFFSET, $this->params->getOffset());
			$query->setParameterByType(BaseParameter::SORT_BY, $this->params->getSortBy());
			return $view->search($query);
		}catch(Exception $e) {
			throw new Exception('autoComplete error -> ' . $e->getMessage());
		}
	}
	
	public function getSources() {
		try {
			$view = $this->initView(FASTConfig::SEARCHVIEW);
			$query = new Query();
			$query->setParameterByType(BaseParameter::HITS,FASTConfig::HITS);
			$query->setParameterByName('language',FASTConfig::LANG);
			$query->setParameterByName('spell',FASTConfig::SPELL);
			$query->setParameterByName('rff_ddr:enabled',0);
			$query->setParameterByName('qtf_lemmatize','true');
			$query->setQueryString('');
			$query->setParameterByType(BaseParameter::OFFSET, 0);
			$query->setParameterByType(BaseParameter::SORT_BY, FASTConfig::DEFAULT_SORTBY);
			$query->setParameterByName('timeout',30000);
			$query->setParameterByType(BaseParameter::NAVIGATION, 1);
			$result = $view->search($query);
			$this->temp_navs = $result->navigators();
			
			$temp_source = $this->getNavigation();
			$temp_source = $temp_source['navigator']['source']['mod'];
			$source = array();
			foreach($temp_source as $v) {
				$source[] = $v['name'];
			}
			
			return $source;
		}catch(Exception $e) {
			throw new Exception('Search news error -> ' . $e->getMessage());
		}
	}
}