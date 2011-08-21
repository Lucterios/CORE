<?
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//            Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//
header('Content-Type: text/xml; charset=UTF-8');

class TestManager {

	private $extensionObj;
	private $extensionName;
	private $numTest;
	
	private $GlobalTest;
	private $CODE_COVER;

	public function __construct($extensionName,$title,$coveractif) {
		$this->extensionName=$extensionName;
		include_once('CORE/UnitTest.inc.php');
		$this->GlobalTest=new TestItem($title,"");
		include_once('CORE/CodeCover.inc.php');
		$this->CODE_COVER=new CodeCover($coveractif);
	}

	private function dumpMysql(){
		global $dbcnf;
		global $fileDump;
		$ret=array();
		$cmd="mysqldump -u ".$dbcnf['dbuser']." -p".$dbcnf['dbpass']." ".$dbcnf['dbname']." > $this->fileDump";
		exec($cmd,$ret);
		//echo "<!-- Dump $cmd :".print_r($ret,true)." - ".print_r($dbcnf,true)." -->\n";
	}

	private function restorMysql(){
		global $dbcnf;
		global $fileDump;
		$ret=array();
		$cmd="mysql -u ".$dbcnf['dbuser']." -p".$dbcnf['dbpass']." ".$dbcnf['dbname']." < $this->fileDump";
		exec($cmd,$ret);
		//echo "<!-- SQL $cmd :".print_r($ret,true)." - ".print_r($dbcnf,true)." -->\n";
	}

	private function installation(){
		$msg="";
		$set_of_ext[]=$this->extensionObj;
		$dep_names=explode(" ",$this->extensionObj->getDepencies());
		foreach($dep_names as $name)
			if ($name!='') {
				$new_ext=new Extension($name,Extension::getFolder($name));
				$new_ext->ThrowExcept=true;
				$set_of_ext[]=$new_ext;
			}
		$msg='';
		$set_of_ext = sortExtension($set_of_ext);
		foreach($set_of_ext as $ext) {
			$ext->installComplete();
			$msg.=$ext->message;
		}
		foreach($set_of_ext as $ext) {
			$ext->upgradeContraintsTable();
			$msg.=$ext->message;
		}
		//echo "<!-- ".str_replace(array("{[newline]}","--","<",">"),array("\n","","&#139;","&#155;"),$msg)." -->\n";
		
		if (is_file($this->extensionObj->Dir.'/setup.test.php')) {
			$setup_item=new TestItem($this->extensionObj->Name,"SETUP");
			$this->CODE_COVER->startCodeCover();
			$setup_item->runTest($this->extensionObj->Dir,$this->extensionObj->Name,'setup');
			$this->CODE_COVER->stopCodeCover();
			if (!is_null($setup_item->errorObj))
				$this->GlobalTest->addTests($setup_item);
		}
	}

	private function getFileTestList(){
		$fileList=array();
		$dh = @opendir($this->extensionObj->Dir);
		while(($file = @readdir($dh)) != false)
			if(substr($file,-9)=='.test.php') {
				$file_name=substr($file,0,-9);
				if ($file_name!='setup')
					$fileList[]=$file_name;
			}
		@closedir($dh);
		sort($fileList);
		return $fileList;
	}
	
	public function initial($dbuser,$dbpass,$dbname,$numTest,$deleteDump){
		$this->numTest=$numTest;
		include_once("CORE/extensionManager.inc.php");
		require_once("CORE/dbcnx.inc.php");
		require_once("CORE/rights.inc.php");
		require_once("CORE/log.inc.php");
		global $connect;
		global $dbcnf;
		global $login;
		$dbcnf = array(
			"dbtype"=>"mysql",
			"dbhost"=>"localhost",
			"dbuser"=>$dbuser,
			"dbpass"=>$dbpass,
			"dbname"=>$dbname
		);
		$login='admin';
		$connect = new DBCNX();
		$connect->connect($dbcnf);

		$this->fileDump="tmp/$dbname.sql";
		if (is_file($this->fileDump) && $deleteDump)
			unlink($this->fileDump);
		
		$this->testtag_file='conf/testtag.file';
		$handle = @fopen($this->testtag_file, "w+");
		@fwrite($handle,"RUNNING");
		@fclose($handle);
	}
	
	private function createDB(){
		global $connect;
		$create_result=createDataBase(!is_file($this->fileDump),false);
		
		$this->extensionObj=new Extension($this->extensionName,Extension::getFolder($this->extensionName));
		$this->extensionObj->ThrowExcept=true;
		$this->CODE_COVER->load($this->extensionObj->Dir);
		$item=new TestItem($this->extensionName,"00 Version ".$this->extensionObj->getPHPVersion());
		if ($connect->connected) {
			if (!is_file($this->fileDump)) {
				$setup_item=$this->installation();
				$this->dumpMysql();
			}
			else if ($this->numTest==0)
				$this->restorMysql();
			$item->success();
			$this->GlobalTest->addTests($item);
			if (is_file($this->extensionObj->Dir."/includes.inc.php"))
				  require_once($this->extensionObj->Dir."/includes.inc.php");
		}
		else {
			$item->error($create_result);
			$this->GlobalTest->addTests($item);
		}
		return $connect->connected;
	}
	
	private function run(){
		$inc=1;
		$fileList=$this->getFileTestList();
		foreach($fileList as $file_name) {
			if (($this->numTest==-1) || ($this->numTest==$inc)) {
				$item=new TestItem($this->extensionName,sprintf('%02d ',$inc).str_replace('_APAS_','::',$file_name));
				$this->restorMysql();
				$this->CODE_COVER->startCodeCover();
				try {
					$item->runTest($this->extensionObj->Dir,$this->extensionName,$file_name);
					$this->CODE_COVER->stopCodeCover();
				} catch(Exception $e) {
					$this->CODE_COVER->stopCodeCover();
					throw $e;
				}
				$this->GlobalTest->addTests($item);
			}
			$inc++;
		} 
	}
	
	public function execute(){
		if ($this->createDB()) {
			try {
			    $this->run();
			} catch(Exception $e) {
				$item=new TestItem($extensionName,"Echec");
				$item->error($e);
				$this->GlobalTest->addTests($item);
			}
		}
		unlink($this->testtag_file);
	}

	public function checkValid(){
		$erreur="";
		if ($this->extensionName=='') {
			$erreur.="Erreur de paramètres:".print_r($_GET,true);
		}
		$prog_list=array("mysqldump","mysql");
		foreach($prog_list as $prog_item) {
			$ret=array();
			exec("$prog_item --version",$ret);
			$begin_ret=substr($ret[0],0,strlen($prog_item));
			if ($begin_ret!=$prog_item) {
				$erreur.=" - $prog_item inconnu!";
			}
		}
		if ($erreur!='') {
			$item_test=new TestItem("AllTest","echec");
			$item_test->error($erreur);
			$this->GlobalTest->addTests($item_test);
			return false;
		}
		return true;
	}
	
	public function show(){
		echo $this->GlobalTest->AllTests($this->CODE_COVER->AllCover());
	}
}

$testManager=null;
if (isset($_GET['extension']) && isset($_GET['dbuser']) && isset($_GET['dbpass']) && isset($_GET['dbname'])) {
	$testManager=new TestManager($_GET['extension'],isset($_GET['title'])?$_GET['title']:"Lucterios Test",
	      isset($_GET['cover'])?($_GET['cover']=='true'):false);
	$testManager->initial($_GET['dbuser'],$_GET['dbpass'],$_GET['dbname'],isset($_GET['num'])?$_GET['num']:-1,isset($_GET['delete'])?($_GET['delete']!='false'):true);
}
elseif ((count($argv)==5) || (count($argv)==6) || (count($argv)==7)) {
	$testManager=new TestManager($argv[1],(count($argv)==6)?$argv[5]:"LucteriosTest",(count($argv)==7)?$argv[6]:false);
	$testManager->initial($argv[2],$argv[3],$argv[4],-1,true);
}
else {
	$testManager=new TestManager("","",false);
}

if ($testManager->checkValid()) {
	$testManager->execute();
}
$testManager->show();

?> 
