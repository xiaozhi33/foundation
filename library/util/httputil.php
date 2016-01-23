<?php
class HttpUtil{
    /**
     * HttpUtil::postString()
     *
     * 2007-12-19
     */
    public static function postString($field){
        if(isset($_POST[$field])){
            $value = $_POST[$field];
            $value = strval($value);
            $value = stripslashes($value);
            //$value = strip_tags($value);
            $value = trim($value);
            $value = str_replace("\\","\\\\",$value);
            $value = str_replace("'","''",$value);
            $value = str_replace('"','\"',$value);

            $value = trim($value);  //清理空格
            $value = strip_tags($value);   //过滤html标签
            $value = htmlspecialchars($value);   //将字符内容转化为html实体
            $value = addslashes($value);

            return $value;
        }else{
            return '';
        }
    }
    public static function postInsString($field){

        $value = $_POST[$field];

        if( !empty($value)){

            $value = strval($value);
            $value = stripslashes($value);
            $value = trim($value);
            //$value = strip_tags($value);
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
    public static function getString($field,$default = ''){
		if(isset($_GET[$field])) {
			$value = $_GET[$field];
		}

        if(isset($value)){
            $value = strval($value);
            $value = stripslashes($value);
            $value = trim($value);
            $value = strip_tags($value);
            $value = str_replace("\\","\\\\",$value);
            $value = str_replace("'","''",$value);
            $value = str_replace('"','\"',$value);

            $keyword = 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile';
            $arr = explode( '|', $keyword );
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
            return $default;
        }
    }
    public static function clearHtml($value){
        if(isset($value)){
            $value = strip_tags($value);
            $value = str_replace('\\','',$value);
            $value = str_replace('\'','',$value);
            $value = str_replace('"','',$value);
            $value = str_replace(';','',$value);
            return $value;
        }else{
            return '';
        }
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
            $value = trim($value);
            //$value = strip_tags($value);
            $value = str_replace('"','"',$value);
            $value = str_replace("'","'",$value);
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