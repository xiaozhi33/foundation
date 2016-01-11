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
            return $value;
        }else{
            return '';
        }
    }
    public static function postInsString($field){

        $value = $_POST[$field];

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