<?php
class HttpUtil{
    /**
     * HttpUtil::postString()
     *
     * 2007-12-19
     */
    public static function postString($field,$defaultValue=null){
        $value = $_POST[$field];
        $value = strval($value);
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = trim($value);
        $value = str_replace("\\","\\\\",$value);
        $value = str_replace("'","''",$value);
        $value = str_replace('"','\"',$value);

        $value = trim($value);  //清理空格
        $value = strip_tags($value);   //过滤html标签
        $value = htmlspecialchars($value);   //将字符内容转化为html实体
        $value = addslashes($value);

        if(isset($defaultValue)&&!$value){
        	$value = $defaultValue;
        }
        return $value;
    }
    public static function postInsString($field){

        $value = $_POST[$field];

        if(isset($value)){

            $value = strval($value);
            $value = stripslashes($value);
            $value = trim($value);
            $value = str_replace('"','"',$value);
            $value = str_replace("'","'",$value);
            return $value;
        }else{
            return '';
        }
    }
    /**
     * HttpUtil::filterDouble()
     *
     */
    public static function filterDouble($arrayData){
        foreach ($arrayData as $k=>$val){
            $arrayData[$k] = str_replace('"','&quot;',$val);
        }
        return $arrayData;
    }
    /**
     * HttpUtil::getString()
     *
     * 2007-12-19
     */
    public static function getString($field){

        $value = $_GET[$field];

        if(isset($value)){

            $value = strval($value);
            $value = stripslashes($value);
            $value = trim($value);
            $value = strip_tags($value);
            $value = str_replace("\\","\\\\",$value);
            $value = str_replace("'","''",$value);
            $value = str_replace('"','\"',$value);

            $keyword = 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|script|document|eval|<|>';
            $arr = explode( '|', $keyword );

            // 如果含有非法信息，直接跳转到404页面
            foreach ($arr as $k => $v){
                if(check_str($v,$value)){
                    @header("http/1.1 404 not found");
                    @header("status: 404 not found");
                    include("http://www.phpernote.com/404.html");//跳转到某一个页面，推荐使用这种方法
                    exit();
                }
            }

            $value = str_ireplace( $arr, '', $value );

            if ( !empty( $value ) ) {
                if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
                    $value = addslashes($value); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
                }
//$var = str_replace( "_", "\_", $var ); // 把 '_'过滤掉
                $value = str_replace("%", "\%", $value); // 把 '%'过滤掉
                $value = nl2br($value); // 回车转换
                $value = htmlspecialchars($value); // html标记转换
            }

            return $value;
        }else{
            return '';
        }
    }

    function check_str($str, $substr)
    {
        $nums=substr_count($str,$substr);
        if ($nums>=1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function getSession(){

    }
    /**
     * HttpUtil::filterDouble()
     *
     */
    public static function filterNull($arrayData){
        foreach ($arrayData as $k=>$val){
            if($val == ''){
                $arrayData[$k] = "N/A";
            }
        }
        return $arrayData;
    }
    public static function valueString($value){
        if(isset($value)){
            $value = strval($value);
            $value = stripslashes($value);
            $value = strip_tags($value);
            $value = trim($value);
            $value = str_replace("\\","\\\\",$value);
            $value = str_replace("'","''",$value);
            $value = str_replace('"','\"',$value);
            return $value;
        }else{
            return '';
        }
    }
    public static function safeExplodeString($value){
        if(isset($value)){
            $value = strip_tags($value);
            $value = str_replace(":"," ",$value);
            $value = str_replace("|"," ",$value);
            return $value;
        }else{
            return '';
        }
    }
    public static function safeHTML($str, $allow_font = false, $allow_img = false, $allow_lists = true)
    {
        $str = strval($str);
        $str = stripslashes($str);
        $str = trim($str);
        $str = str_replace("\\","\\\\",$str);
        $str = str_replace("'","''",$str);
        $str = str_replace('"','\"',$str);

        $approvedtags = array(
			'p' => 1,   		// 2 means accept all qualifiers: <foo bar>
			'b' => 1,   		// 1 means accept the tag only: <foo>
			'i' => 1,
			'u' => 1,
			's' => 1,
			'a' => 2,
			'em' => 1,
			'br' => 1,
			'strong' => 1,
			'strike' => 1
        );

        if ($allow_font == true)
        {
            $approvedtags['font'] = 2;
            $approvedtags['big'] = 1;
            $approvedtags['sup'] = 1;
            $approvedtags['sub'] = 1;
            $approvedtags['span'] = 2;
        }

        if ($allow_img == true)
        $approvedtags['img'] = 2;

        if ($allow_lists == true)
        {
            $approvedtags['li'] = 1;
            $approvedtags['ol'] = 1;
            $approvedtags['ul'] = 1;
        }

        $keys = array_keys($approvedtags);

        $str = stripslashes($str);
        $str = eregi_replace("<[[:space:]]*([^>]*)[[:space:]]*>","<\\1>",$str);
        $str = eregi_replace("<a([^>]*)href=\"?([^\"]*)\"?([^>]*)>","<a href=\"\\2\">", $str);

        $tmp = '';
        while (eregi("<([^> ]*)([^>]*)>",$str,$reg))
        {
            $i = strpos($str,$reg[0]);
            $l = strlen($reg[0]);
            if ($reg[1][0] == "/")
            $tag = strtolower(substr($reg[1],1));
            else
            $tag = strtolower($reg[1]);

            if ((in_array($tag, $keys))&&($a = $approvedtags[$tag]))
            {
                if ($reg[1][0] == "/")
                $tag = "</$tag>";
                elseif ($a == 1)
                $tag = "<$tag>";
                else
                $tag = "<$tag " . $reg[2] . ">";
            }
            else
            $tag = '';

            $tmp .= substr($str,0,$i) . $tag;
            $str = substr($str,$i+$l);
        }

        $str = $tmp . $str;

        $str = ereg_replace("<\?","",$str);
        $str = ereg_replace("<!--","",$str);

        return $str;
    }
    public static function postCleanTitle($field){
        if(isset($_POST[$field])){
            $value = $_POST[$field];
            $value = strval($value);
            $value = stripslashes($value);
            $value = strip_tags($value);
            $value = trim($value);
            $value = str_replace("\\","\\\\",$value);
            $value = str_replace("'","''",$value);
            $value = str_replace('"','\"',$value);
            $value = str_replace(';','_',$value);
            $value = str_replace('；','_',$value);
            $value = str_replace(' ','',$value);
            $value = str_replace("\n",'',$value);
            $value = str_replace("\r",'',$value);
            $value = str_replace("\t",'',$value);
            $value = str_replace("\xOB",'',$value);
            return $value;
        }else{
            return '';
        }
    }

    public static function isJsonRequest(){
        return (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest');
    }

}
?>