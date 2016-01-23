<?php
require_once ('Pager/Pager.php');
require_once ('Pager/Sliding.php');
require_once ('Pager/Jumping.php');
class pageDAO{
	public $recordPerPage;   //显示多少页
	public $pageSize;
    function pageInfo($data,$url,$total=null,$httpMethod = 'GET',$RECORDPERPAGE = __RECORDPERPAGE__,$PAGESIZE = __PAGESIZE__){
        $params = array(
                            'mode'=>'Sliding',
        					'separator'=>'',
        					'spacesBeforeSeparator'=>0,
        					'spacesAfterSeparator'=>1,
                            'perPage'=>$RECORDPERPAGE,
                            'delta'=>floor($PAGESIZE/2),
                            'itemData'=>$data,
                            'httpMethod'=>$httpMethod,
                            'altPrev'=> '上一页',
                    		'altNext'=> '下一页',
                        	'nextImg'=> '下一页',
                        	'prevImg'=> '上一页',
                            'path'=>$url,
        					'curPageSpanPre'	=> '<a class="current">',
	    					'curPageSpanPost'   => '</a>',
    						'excludeVars'=> array('user')
        );
        if(isset($total)){
        	$params['extraVars']=array('total'=>$total);
        }
        if(isset($this->pageSize)){
        	$params['delta'] = $this->pageSize;
        }
    	if(isset($this->recordPerPage)){
        	$params['perPage'] = $this->recordPerPage;
        }
        
        $pager = Pager::factory($params);
        $pageArray['pageData'] = $pager->getPageData();
        $pageArray['pageLink'] = $pager->getLinks();
        $pageArray['pageTotalNum'] = $pager->numPages();
        $pageArray['pageRecordNum'] = $pager->numItems();
        return $pageArray;
    }

    /**
     * @param null $data
     * @param $totalItems
     * @param $url
     * @param null $total
     * @param string $httpMethod
     * @param $RECORDPERPAGE
     * @param $PAGESIZE
     * @return mixed
     */
    public function pageHelper($data=null,$totalItems,$url,$total=null,$httpMethod = 'GET',$RECORDPERPAGE = __RECORDPERPAGE__,$PAGESIZE = __PAGESIZE__){
    	$params = array(
	    	'mode'			=> 'Jumping',
	    	'perPage'		=> $RECORDPERPAGE,
	    	'delta'			=> $PAGESIZE,
	    	'itemData'		=> $data,
	    	'totalItems'	=> $totalItems,
	    	'httpMethod'	=> $httpMethod,
	    	'altPrev'		=> '上一页',
	    	'altNext'		=> '下一页',
	    	'nextImg'		=> '下一页',
	    	'prevImg'		=> '上一页',
	    	'path'			=> $url,
    		'curPageSpanPre'	=> '<span class="current">',
	    	'curPageSpanPost'   => '</span>',
	    	'excludeVars'=> array('user')
    	);
        if(isset($total)){
        	$params['extraVars']=array('total'=>$total);
        }
        if(isset($this->pageSize)){
        	$params['delta'] = $this->pageSize;
        }
    	if(isset($this->recordPerPage)){
        	$params['perPage'] = $this->recordPerPage;
        }
        
        $pager = Pager::factory($params);
        $pageArray['pageData'] = $pager->getPageData();
        $pageArray['pageLink'] = $pager->getLinks();
        $pageArray['pageTotalNum'] = $pager->numPages();
        $pageArray['pageRecordNum'] = $pager->numItems();
        return $pageArray;
    }
}
?>