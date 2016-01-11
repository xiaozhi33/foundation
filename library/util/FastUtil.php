<?php
	require_once 'sde/fastsearch/SearchFactory.php';
	require_once 'sde/fastsearch/SearchException.php';
	require_once 'sde/fastsearch/query/Query.php';
	require_once 'sde/fastsearch/result/QueryTransformationName.php';
	
	class FastUtil {
		public static function getInstance($viewName) {
			$param = array (
				'fastsearch.SearchFactory' => "http.HttpSearchFactory",
				'fastsearch.http.qservers' => __FASTHOST__ . ':' . __FASTPORT__
			);
			$searchFactory = SearchFactory::newInstance($param);
			return $searchFactory->getSearchView($viewName);
		}
		public static function getPager($options) {
			require_once 'Pager/Pager.php';
			
			$pagerOptions = array(
				'perPage'                   => __FASTHIT__,
				'delta'                     => 4,
				'mode'                      => 'sliding',
				'httpMethod'                => 'POST',
				'urlVar'                    => 'offset',
				'altPrev'                   => '上一页',
				'altNext'                   => '下一页',
				'nextImg'                   => '下一页',
				'prevImg'                   => '上一页',
				'separator'                 => '',
				'spacesBeforeSeparator'     => 0,
				'spacesAfterSeparator'      => 1
			);
			
			$pagerOptions = array_merge($pagerOptions,$options);
			
			$pager = Pager::factory($pagerOptions);
			
			$links = $pager->getLinks();
			$links['all'] = str_replace($links['last'],'',$links['all']);
			$links['all'] = str_replace('index.php','',$links['all']);
			$links['all'] = str_replace(urlencode($_POST['query']),$_POST['query'],$links['all']);
			$links['all'] = str_replace(urlencode($_POST['navigation']),$_POST['navigation'],$links['all']);
			$links['all'] = str_replace(urlencode($_POST['drillDown']),$_POST['drillDown'],$links['all']);
			$links['all'] = str_replace(urlencode($_POST['drillUp']),$_POST['drillUp'],$links['all']);
			
			return $links;
		}
	}
?>