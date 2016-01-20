<?php

function sys_addslash($data)
{
    if (!get_magic_quotes_gpc())
    {
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                {
                    $data[$key] = sys_addslash($value);
                }
                else
                {
                    $data[$key] = addslashes($value);
                }
            }
        }
        else
        {
            $data = addslashes($data);
        }
    }
    return $data;
}
function go_back(){
	echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
	echo('<script language="JavaScript">');
	echo('history.back();');
	echo('</script>');
	exit;
}
function alert_back($msg){
	/*echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
	echo('<script language="JavaScript">');
	echo("alert('$msg');");
	echo('history.back();');
	echo('</script>');
	exit;*/
    echo json_encode(array('error'=>true, 'msg'=>$msg ));
    exit;
}
function alert_go($msg,$url){
	echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
	echo('<script language="JavaScript">');
	echo("alert('$msg');");
	echo("location.href='$url';");
	echo('</script>');
	exit;
}

function check_admin()
{
	  if(!private_admin())
	  header('location:login.php?act=login');
}
function private_admin()
{
      if(isset($_COOKIE['ckaid'])&&!empty($_COOKIE['ckaid']))
      {
      	return 1;
      }else{
	  	return 0;
	  }
}

function assign_login(){
	global $smarty;
	$smarty->assign("islogin",checklogin());
	if(checklogin())
	$smarty->assign("uname",$_COOKIE['username']);
}


function createFckeditor($fckid,$fckvalue=''){
	$editor = new FCKeditor($fckid) ;
	$editor->BasePath   = "fckeditor/";
	$editor->ToolbarSet = "Basic";
	$editor->Width      = "450";
	$editor->Height     = "400";
	$editor->Value=$fckvalue;
	$fckeditor = $editor->CreateHtml();
	return $fckeditor;
}

function getPageNav($pagerecords,$totalrecords)
{
   $page=isset($_GET["page"])?$_GET["page"]:1;
    
    if(!isset($url))
    	$url=$_SERVER["REQUEST_URI"];
    $parse_url=parse_url($url);
    $url_query=isset($parse_url["query"])?$parse_url["query"]:''; 
	
	if($url_query)
	{
	  $url_query=ereg_replace("(^|&)page=$page","",$url_query);
	  $url=str_replace($parse_url["query"],$url_query,$url);
	  if($url_query) $url.="&page"; 
	  else $url.="page";
	}
	else 
	{
	   $url.="?page";
	 }

   $totalpages=ceil($totalrecords/$pagerecords);
   $lastpg=$totalpages;
   $page=min($totalpages,$page);
   $prepg=$page-1;
   $nextpg=($page==$totalpages?0:$page+1);

   $firstcount=($page-1)*$pagerecords;
   //$showresult="display ".($totalrecords?($firstcount+1):0)." - ".min($firstcount+$pagerecords,$totalrecords)." Records, ";
   $showresult="";
   if($lastpg<=1)
   {
	 return "";
   }
   
  if($prepg){
  $showresult.=" <a href='$url=$prepg'>Back</a>";}
  
  $pagelen=10;
 
  if($page<1) $page = 1;
     if($page>$totalpages) $page = $totalpages;
     //计算查询偏移量
    // $offset = 10*($page-1);
     //页码范围计算
     $init = 1;//起始页码数
     $max = $totalpages;//结束页码数
     $pagelen = ($pagelen%2)?$pagelen:$pagelen+1;//页码个数
     $pageoffset = ($pagelen-1)/2;//页码个数左右偏移量

if($totalpages>$pagelen){
         //如果当前页小于等于左偏移
         if($page<=$pageoffset){
             $init=1;
             $max = $pagelen;
         }else{//如果当前页大于左偏移
             //如果当前页码右偏移超出最大分页数
             if($page+$pageoffset>=$totalpages+1){
                 $init = $totalpages-$pagelen+1;
             }else{
                 //左右偏移都存在时的计算
                 $init = $page-$pageoffset;
                 $max = $page+$pageoffset;
             }
         }
     }
//     echo $init;
//     echo "</br>";
//     echo $max;
//     echo "</br>";
//     echo $pageoffset;
//     echo "</br>";
//     echo $page;
//     echo "</br>";
//     echo $totalpages;
//     echo "</br>";
//     echo $offset;
//     echo "</br>";
//     echo $pagelen;
//      echo "</br>";
//     echo $pageoffset;
//      echo "</br>";
//     exit();
     
     
  for($i=$init;$i<=$max;$i++)
  {
  	//$lastpg;
  	$showresult.="<a href='$url=$i'>";
	if($i==$page) $showresult.="<font color='#993033'>$i </font>";
	else $showresult.="$i ";
	$showresult.="</a>";
	
   }
   if($nextpg){
  $showresult.=" <a href='$url=$nextpg'>Next</a>";}
  //$showresult.=" CountAll $lastpg pages";
  
  	unset($init);
   return $showresult;
}

/////查看访问次数
function fangwenliang(){
	try {
		$db=new DB_MySQL();
		$selectSQL="select count(*) from phpmv_visit";
		return $db->fetch_row("$selectSQL");
	}catch (Exception $e){
		return $e;
	}
}


//记录日志
function addlog($logType=null,$logName=null,$logIp=null,$logTime=null,$logMsg=null){
	try {
		$loginfo = new my_logDAO();
		$db=new DBHelper();
		$db->connect();
		$loginfo ->logIp = $logIp;
		$loginfo ->logType = $logType;
		$loginfo ->logName = $logName;
		$loginfo ->logTime = $logTime;
		$loginfo ->logMsg = $logMsg;
		$loginfo ->save($db);
	}catch (Exception $e){
		throw $e;
	}
}

//查看日志
function selectlog($logName=null,$start=null,$end=null){
	try {
		$loginfo = new my_logDAO();
		$db=new DBHelper();
		$db->connect();
		if($start != "" && $end != ""){
			$loginfo ->selectLimit = " and logTime>'$start' and logTime<'$end' order by logPid DESC";
		}
		$loglistinfo = $loginfo ->get($db);
		return $loglistinfo;
	}catch (Exception $e){
		throw $e;
	}
}
?>