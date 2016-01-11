<?php
class uploadPic{
    var $FILE_PATH;
    var $file_type;
    var $file_type_array;
    var $file_type_real_array;
    var $file_type_string;
    var $file_name;
    var $file_size;
    var $file_tmp_name;
    var $file_error;
    var $handledate;
    var $type;
    var $rename;
    static $totalsize=0;

    function __construct($file_name,$file_error,$file_size,$file_tmp_name,$file_type,$type=0,$rename = null){
        $this->handledate=date('m-d-Y');
        if (!empty($file_name)){
            $this->file_name = $file_name;
            $this->file_error = $file_error;
            $this->file_size = $file_size;
            $this->file_tmp_name = $file_tmp_name;
            $this->file_type = $file_type;
            $this->type = $type;
            $this->rename = $rename;
            if($type == 2){//flash
                $this->file_type_array = array('/','application/x-shockwave-flash' , 'image/gif', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/png','image/pjpeg','application/rar','application/x-rar-compressed','application/msword');
            }else{//image
                $this->file_type_array = array('/', 'image/gif', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/png','image/pjpeg');
            }
            $this->file_type_real_array = '';//array(0.1, 'jpg'=>74707370, 'gif'=>7173, 'bmp'=>6677, 'png'=>807871);
             
            //$this->show_execute_message($this->file_error,$this->file_name,$this->file_type,$this->file_size);

        }
    }
    function uploadPic(){
        $picname = $this->show_execute_message($this->file_error,$this->file_name,$this->file_type,$this->file_size,$this->type);
        return $picname;
    }

    function __destruct(){
        $this->file_name = NULL;
        $this->file_error = NULL;
        $this->file_size = NULL;
        $this->file_tmp_name = NULL;
        $this->file_type = NULL;
        self::$totalsize = 0;
    }

    function show_execute_message($smfileerror,$smfilename,$smfiletype,$smfilesize,$type){
        if($smfileerror>0){
            switch ($smfileerror){
                case 1: $smfilemessage='上传文件超过限制大小';break;
                case 2: $smfilemessage='上传文件超过限制大小';break;
                case 3: $smfilemessage='上传文件未完成';break;
                case 4: echo "$this->file_name ". '没有被上传<br/>';break;
            }

            $result['error'] = $smfileerror;
            $result['msg'] = $smfilemessage;
            $result['picname'] = '';
            $result['picsize'] = self::$totalsize;
            self::__destruct();
            return $result;

        }else{
            $smfiletypeflag = array_search($smfiletype,$this->file_type_array);
            if($smfiletypeflag == false){
                if($type == 2){
                    $smfilemessage='上传不成功,请选择相关文件上传';
                }else{
                    $smfilemessage='上传不成功,请选择图片文件上传';
                }

                $result['error'] = 11;
                $result['msg'] = $smfilemessage;
                $result['picname'] = '';
                $result['picsize'] = self::$totalsize;
                self::__destruct();
                return $result;
            }else{
                $resflag = $this->move_file($this->file_tmp_name,$this->file_name,$this->rename);
                $resflag = explode("-",$resflag);
                if ($resflag[0] == 1){
                    if($type == 2){
                        $smfilemessage = '相关文件上传成功';
                    }else{
                        $smfilemessage = '图片上传成功';
                    }
                    self::$totalsize += intval($smfilesize);
                    $result['error'] = 0;
                    $result['msg'] = $smfilemessage;
                    $result['picname'] = $resflag[1];
                    $result['picsize'] = self::$totalsize;
                    self::__destruct();
                    return $result;
                }else{
                    if($type == 2){
                        $smfilemessage='相关文件上传不成功';
                    }else{
                        $smfilemessage = '图片上传不成功';
                    }

                    $result['error'] = 12;
                    $result['msg'] = $smfilemessage;
                    $result['picname'] = '';
                    $result['picsize'] = self::$totalsize;
                    self::__destruct();
                    return $result;
                }
            }
        }

        /*
         $smfilesizeformat = $this->size_BKM($smfilesize);
         echo '<tr>
         <td align="left" >'.$smfilename.'</td>
         <td align="center" >'.$smfiletype.'</td>
         <td align="center" >'.$smfilesizeformat.'</td>
         <td align="center" >'.$smfilemessage.'</td>
         </tr>';
         */
    }

    function move_file($mvfiletmp,$mvfilename,$rename){ 
        $mvfilenamearr = explode('.',basename($mvfilename));
        if(empty($rename)){
            $mvfilenamearr[0] = time().rand(1000,9999);//$this->rand_string();
        }else{
            $mvfilenamearr[0] = $rename;
        }
        $mvfilename = implode('.',$mvfilenamearr);

        if (is_uploaded_file($mvfiletmp)){
            $uploadfile = $this->FILE_PATH."$mvfilename";
            $result = move_uploaded_file($mvfiletmp,$uploadfile);
            return $result.'-'.$mvfilename;
        }
    }

    function rand_string(){
        $string = md5(uniqid(rand().microtime()));
        return $string;
    }

    function size_BKM($size){ 
        if($size < 1024)
        {
            $size_BKM = (string)$size . " B";
        }
        elseif($size < (1024 * 1024))
        {
            $size_BKM = number_format((double)($size / 1024), 1) . " KB";
        }else
        {
            $size_BKM = number_format((double)($size / (1024*1024)),1)." MB";
        }
        return $size_BKM;
    }
}
?>