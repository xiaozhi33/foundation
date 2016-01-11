<?php
// createDst(原图片地址，缩略图最大宽度，缩略图最大高度 ，生成缩略图地址)
function createDst ($img_url,$max_width,$max_height,$dst_url='')
{
    if ( empty( $dst_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $sub_url = substr($img_url,0,strrpos( $img_url,"." ));
        $dst_url = $sub_url . "_mini.jpg";
    }
    if(!file_exists($img_url))
    {
        die("图片不存在");
    }

    $img_src = file_get_contents($img_url);
    $image = ImageCreateFromString( $img_src );//用该方法获得图象，可以避免“图片格式”的问题。
    $width = imageSx( $image );
    $height = imageSy( $image );

    $percent = $max_width / $max_height;
    if($height*$percent < $width){
        $width = $height*$percent;
    }
    if($width/$percent <$height){
        $height = $width/$percent;
    }

    //print_r($new_width.'-'.$new_height); exit;
    /*生成高质量的缩略图方法*/
    //$dst = imagecreatetruecolor($tn_width,$tn_height);//新建一个真彩色图像
    $dst = imagecreatetruecolor($max_width,$max_height);
    //imagecopyresampled($dst, $image, 0, 0, 0, 0,$tn_width,$tn_height,$width,$height);
    imagecopyresampled($dst, $image, 0, 0, 0, 0,$max_width,$max_height,$width,$height);
    //header(''Content-type: image/jpeg'');
    ImageJpeg($dst, $dst_url,90);
    ImageDestroy($image);
    ImageDestroy($dst);
    if(!file_exists($dst_url)){
        return '';
    }else{
        return basename($dst_url);
    }
}
function fullImg($width,$height,$images){
	$newSize = $width>$height?$width:$height;
	$x = $width>=$height?0:($height-$width)/2;
	$y = $height>=$width?0:($width-$height)/2;
	$canvas = imagecreatetruecolor($newSize,$newSize);
	$white = imagecolorallocate($canvas,255,255,255);
	imagefill($canvas,0,0,$white);
	$temp = imagecopy($canvas,$images,$x,$y,0,0,$width,$height);
	return $canvas;
}

/**
 * Enter description here...
 *
 * @param String $img_url
 * @param int $max_width
 * @param int $max_height
 * @param string $dst_url
 * @param boolean $pad 是否自动填充
 * @return array
 */
function createResizeDst ($img_url,$max_width,$max_height,$dst_url='',$pad=false)
{
    if ( empty( $dst_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        //$dst_url = $img_url;
        $filename = basename($img_url);
        $urlname = dirname($img_url);
        $filename = explode(".",$filename);
        $dst_url = $urlname.'/'.$filename[0] . "_mini.jpg";
    }
    if(!file_exists($img_url))
    {
        die("图片不存在");
    }

    $img_src = file_get_contents($img_url);

    $images = ImageCreateFromString( $img_src );//用该方法获得图象，可以避免“图片格式”的问题。
    $width = imageSx( $images );
    $height = imageSy( $images );
	if ($pad){
		$image = fullImg($width,$height,$images);
		$width = imageSx( $image );
	    $height = imageSy( $image );
	}else {
		$image = $images;
	}
    $x_ratio = $max_width / $width;
    $y_ratio = $max_height / $height;

    if ( ($width <= $max_width) && ($height <= $max_height) ){
        $tn_width = $width;
        $tn_height = $height;
    }else if (($x_ratio * $height) < $max_height){
        $tn_height = ceil($x_ratio * $height);
        $tn_width = $max_width;
    }else{
        $tn_width = ceil($y_ratio * $width);
        $tn_height = $max_height;
    }
    //print_r($new_width.'-'.$new_height); exit;
    /*生成高质量的缩略图方法*/
    $dst = imagecreatetruecolor($tn_width,$tn_height);//新建一个真彩色图像
    imagecopyresampled($dst, $image, 0, 0, 0, 0,$tn_width,$tn_height,$width,$height);
    //header(''Content-type: image/jpeg'');
    ImageJpeg($dst, $dst_url,90);
    ImageDestroy($image);
    ImageDestroy($dst);
    if(!file_exists($dst_url)){
        return '';
    }else{
        return basename($dst_url);
    }
}

// createDst(原图片地址，缩略图最大宽度，缩略图最大高度 ，生成缩略图地址)
function createDoubleThumnail ($img_url,$max_small_width,$max_small_height,$max_mid_width=200,$max_mid_height=200,$dst_url='',$mid_url='')
{
	if ( empty( $dst_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $sub_url = substr($img_url,0,strrpos( $img_url,"." ));
        $dst_url = $sub_url . "_mini.jpg";
    }
	if ( empty( $mid_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $mid_url = $sub_url . "_mid.jpg";
    }

    if(!file_exists($img_url))
    {
        die("图片不存在");
    }

    $img_src = file_get_contents($img_url);
    $image = ImageCreateFromString( $img_src );//用该方法获得图象，可以避免“图片格式”的问题。
    $width = imageSx( $image );
    $height = imageSy( $image );

    $percent = $max_small_width / $max_small_height;
    if($height*$percent < $width){
        $width = $height*$percent;
    }
    if($width/$percent <$height){
        $height = $width/$percent;
    }

    /*生成中等缩略图*/
    $dst = imagecreatetruecolor($max_mid_width,$max_mid_height);
    imagecopyresampled($dst, $image, 0, 0, 0, 0,$max_mid_width,$max_mid_height,$width,$height);
    ImageJpeg($dst, $mid_url, 90);
    
     //生成小缩略图
    $small = imagecreatetruecolor($max_small_width,$max_small_height);//新建一个真彩色图像
    imagecopyresampled($small, $dst, 0, 0, 0, 0,$max_small_width,$max_small_height,$max_mid_width,$max_mid_height);
    //header(''Content-type: image/jpeg'');
    ImageJpeg($small, $dst_url, 90);
    
    ImageDestroy($image);
    ImageDestroy($dst);
    ImageDestroy($small);
    
    if(!file_exists($dst_url)){
        return array('','');
    }else{
        return array( basename($dst_url), basename($mid_url));
    }
}

function freeCropImage($img_url,$width,$height,$swidth,$sheight,$w,$h,$x=0,$y=0,$dst_url='',$small_url='',$is_mid_make=true){
	if ( empty( $dst_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $sub_url = substr($img_url,0,strrpos( $img_url,"." ));
        $dst_url = $sub_url . "_mid.jpg";
    }
	if ( empty( $small_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $small_url = substr($img_url,0,strrpos( $img_url,"." ));
        $small_url = $small_url . "_mini.jpg";
    }

    if(!file_exists($img_url))
    {
        die("图片不存在");
    }

    $img_src = file_get_contents($img_url);
    $image = ImageCreateFromString( $img_src );//用该方法获得图象，可以避免“图片格式”的问题。

    
	/*生成中等缩略图*/
    if($is_mid_make){
    $dst = imagecreatetruecolor($width,$height);//新建一个真彩色图像
    imagecopyresampled($dst, $image, 0, 0,$x, $y, $width,$height,$w,$h);
    //header(''Content-type: image/jpeg'');
    ImageJpeg($dst, $dst_url,90);
    $x=0;
    $y=0;
    }else{
    	$dst=&$image;
    	$width=$w;
    	$height=$h;
    }
    
    //生成小缩略图
    $small = imagecreatetruecolor($swidth,$sheight);//新建一个真彩色图像
    imagecopyresampled($small, $dst, 0, 0, $x, $y,$swidth,$sheight,$width,$height);
    //header(''Content-type: image/jpeg'');
    ImageJpeg($small, $small_url,90);
    ImageDestroy($image);
    if($is_mid_make)ImageDestroy($dst);
    ImageDestroy($small);
    
    if(!file_exists($small_url)){
        return '';
    }else{
        return array(basename($dst_url),basename($small_url));
    }
}

//裁切缩略图增强版，支持裁切越界自动填充
function freeCropImage2($img_url,$width,$height,$swidth,$sheight,$cuvas,$cropImg,$localImg,$savePath,$dst_url='',$mid_url=''){
	$filename = time().rand(0,9).rand(0,9).rand(0,9);
	if ( empty( $dst_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $dst_url = $savePath.$filename."_mid.jpg";
    }
	if ( empty( $small_url ) ) //如果 $dst_url 参数未赋值的话，则缩略图生成在原图片所在的文件夹
    {
        $small_url = $savePath.$filename."_small.jpg";
    }
    
/*    if(!file_exists($img_url))
    {
        die("图片不存在");
    }*/
    $img_src = file_get_contents($img_url);
    $image = ImageCreateFromString( $img_src );//用该方法获得图象，可以避免“图片格式”的问题。
    
    //创建白色空白图像
    $dst = imagecreatetruecolor($width,$height);
    $white = imagecolorallocate($dst, 255, 255, 255);
	imagefill($dst, 0, 0, $white);
	
	//计算缩放比例
	$scale = $cuvas['w']/$width;
	
	//生成中等缩略图
	imagecopyresampled($dst, $image, 
		round($localImg['x']/$scale), round($localImg['y']/$scale),
		$cropImg['x'], $cropImg['y'], 
		round($cropImg['w']/$scale),round($cropImg['h']/$scale),
		$cropImg['w'],$cropImg['h']
	);
	
    ImageJpeg($dst, $dst_url,90);
    
    //生成小缩略图
    $small = imagecreatetruecolor($swidth,$sheight);//新建一个真彩色图像
    imagecopyresampled($small, $dst, 0, 0, 0, 0,$swidth,$sheight,$width,$height);
    //header(''Content-type: image/jpeg'');
    ImageJpeg($small, $small_url,90);
    ImageDestroy($image);
    ImageDestroy($dst);
    ImageDestroy($small);
    
    if(!file_exists($small_url)){
        return '';
    }else{
        return array(basename($dst_url),basename($small_url));
    }
}
?>