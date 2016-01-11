<?php
class mkFile{
    private $fileDir;
    private $fileName;
    private $fileText;
    
   
    public function __construct($fileName = '', $fileText = '', $fileDir = ''){
        $this->setFileDir($fileDir);
        $this->setFileName($fileName);
        $this->setFileText($fileText);
    }
   
   
    public function save(){
        $ret = false;
        if ($this->getFileName()===''){
            return false;
        }else{          
            	if($this->getFileDir())
            	{
            		if($f = fopen($this->getFileDir().$this->getFileName(),"w+")){            			
                		fwrite($f,$this->getFileText());
             	 	 	fclose($f);
               			$ret = true;
            		}
            		else {$ret=false;}
            	}
                else{$ret=false;}           
        	 }
        return $ret;
    }
   
    public function setFileName($fileName){
        $this->fileName = $fileName;
    }
   
    public function getFileName(){
        return $this->fileName;
    }
   
    public function setFileText($fileText){
        $this->fileText = $fileText;
    }
   
    public function getFileText(){
        return $this->fileText;
    }
   
    public function setFileDir($fileDir){
        $this->fileDir = $fileDir;
    }
   
    public function getFileDir(){
        return $this->fileDir;
    }
}
?>
