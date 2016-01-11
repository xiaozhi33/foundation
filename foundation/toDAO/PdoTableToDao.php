<?php
require_once 'makeFile.class.php';
class PdoTableToDao {
	private $_conn;
	private $_user;
	private $_password;
	private $_db_name;
	private $db;
	private $_path;
	/**
	 构造函数用于初始化 
	 */
	public function __construct($conn,$user,$password,$db_name,$path){
		$this->_conn=$conn;
		$this->_user=$user;
		$this->_password=$password;
		$this->_db_name=$db_name;
		$this->_path=$path;
	}	
	/**
	 * 用pdo建立数据库连接
	 */
	public function connect(){        
        try{
            $this->db = new PDO("{$this->_conn}","{$this->_user}","{$this->_password}");
            $this->db->query("SET NAMES 'utf8'");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (Exception $e){
            throw new Exception("Pdo连接数据库失败!".$e->getMessage());
        }
	}	
	/**
	 * 返回$this->_db_name中所有表的表名数组
	 */
	protected  function getTable(){
		$sql='show tables';
		try{
            $stmt = $this->db->prepare($sql);           
            $stmt->execute();            
            $tableArray = $stmt->fetchAll();            
            return $tableArray;
        }catch (Exception $e){
            echo $sql;
            throw $e;
        }
	}	
	/**
	 * 返回this->_db_name中$tableName表的所有字段数组
	 */
	protected  function getField($tableName){
		$sql="show columns from ".$tableName;
		try{
            $stmt = $this->db->prepare($sql);           
            $stmt->execute();            
            $fieldArray = $stmt->fetchAll();            			
            return $fieldArray;
        }catch (Exception $e){
            echo $sql;
            throw $e;
        }
	}	
	/**
	 * 计算$tableName中字段个数
	 */
	protected  function countField($tableName){
		$arr=count($this->getField($tableName));
		return $arr;
	}
	/**
	 * 构造文件内容，生成文件
	 */	
	public function output(){
		$tableArray=$this->getTable();
		foreach ($tableArray as $table){
			echo '表名：'.$this->_db_name.'.'.$table[0];			
			$fieldArray=array();
			$fileText="\t ";
			$fileText .= "public ";
			$tableName = "\t const tableName = '{$table[0]}';\r\n";
			$tableField = "\t const tableField ='"; 
			$field = '';
			$countField=$this->countField($table[0]);
			for($i=0;$i<$countField;$i++){
				$fieldArray=$this->getField($table[0]);				
				$field .= "{$fieldArray[$i][0]},";				
				$fileText=$fileText."$".$fieldArray[$i][0].",";				
			}
			$fileText = substr($fileText,0,strlen($fileText)-1);
			$fileText .= ";";	
			$fileText .= "\r\n\t public function __construct(){\r\n\t\t";
			$fileText .= '$this->_init(get_defined_vars());';
			$fileText .= "\r\n\t}";
			
			$fileText .= "\r\n\t public function get(";
			$fileText .= '$db';
			$fileText .= "){\r\n\t\t";
			$fileText .= "return ";
			$fileText .= '$this->selectTable($db);';
			$fileText .= "\r\n\t}";
			
			$fileText .= "\r\n\t public function save(";
			$fileText .= '$db';
			$fileText .= "){\r\n\t\t";
			$fileText .= 'return $this->writeTable($db,array());';
			$fileText .= "\r\n\t}";
			
			$fileText .= "\r\n\t public function del(";
			$fileText .= '$db';
			$fileText .= "){\r\n\t\t";
			$fileText .= 'return $this->deleteTable($db);';
			$fileText .= "\r\n\t}";
			
			$field = substr($field,0,strlen($field)-1);
			$field .= "';";
			$fileName=$table[0]."DAO.php";//构建文件名为"表名.php"
			//构建php文件内容并利用makeFile类创建文件
    		$fileText="<?php
require_once 'EasyORM.php';
class ".$table[0]."DAO extends EasyORM{
".
$tableName.
    $tableField.$field
    ."
".$fileText."
}
?>	 
    			";
    		$fileDir=$this->_path;    			   			
    		$file=new mkFile($fileName,$fileText,$fileDir);
    		$a=$file->save();
    		if($a){
    			echo"...      <font color=red>完成！</font><br><br>";
    		}
    		else{
    			echo "<font color=red>失败，请检查输入参数是否正确</font>";
    		}
    		
		}		
	}	
}

?>
