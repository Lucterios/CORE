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

$dbcnf = array();
$fileDump="tmp/dump.sql";

function dumpMysql(){
  global $dbcnf;
  global $fileDump;
  $ret=array();
  $cmd="mysqldump -u ".$dbcnf['dbuser']." -p".$dbcnf['dbpass']." ".$dbcnf['dbname']." > $fileDump";
  exec($cmd,$ret);
  //echo "<!-- Dump $cmd :".print_r($ret,true)." - ".print_r($dbcnf,true)." -->\n";
}

function restorMysql(){
  global $dbcnf;
  global $fileDump;
  $ret=array();
  $cmd="mysql -u ".$dbcnf['dbuser']." -p".$dbcnf['dbpass']." ".$dbcnf['dbname']." < $fileDump";
  exec($cmd,$ret);
  //echo "<!-- SQL $cmd :".print_r($ret,true)." - ".print_r($dbcnf,true)." -->\n";
}

$erreur="";
$run=false;
if (isset($_GET['extensions']) && isset($_GET['dbuser']) && isset($_GET['dbpass']) && isset($_GET['dbname'])) {
	$run=true;
	$extensions=split(';',$_GET['extensions']);
	$dbuser=$_GET['dbuser'];
	$dbpass=$_GET['dbpass'];
	$dbname=$_GET['dbname'];
	if (isset($_GET['title']))
		$title=$_GET['title'];
	else
		$title="LucteriosTest";
	if (isset($_GET['num']))
		$num_test=$_GET['num'];
	else
		$num_test=-1;
}
elseif ((count($argv)==5) || (count($argv)==6)) {
	$run=true;
	$extensions=split(';',$argv[1]);
	$dbuser=$argv[2];
	$dbpass=$argv[3];
	$dbname=$argv[4];
	if (count($argv)==6)
		$title=$argv[5];
	else
		$title="LucteriosTest";
	$num_test=-1;
}

$prog_list=array("mysqldump","mysql");
foreach($prog_list as $prog_item)
{
  $ret=array();
  exec("$prog_item --version",$ret);
  $begin_ret=substr($ret[0],0,strlen($prog_item));
  if ($begin_ret!=$prog_item)
  {
    $run=false;
    $erreur.=" - $prog_item inconnu!";
  }
}

include_once('CORE/UnitTest.inc.php');
$GlobalTest=new TestItem($title,"");
include_once('CORE/CodeCover.inc.php');
$CODE_COVER=new CodeCover();
if ($run) {
	include_once("CORE/extensionManager.inc.php");
	require_once("CORE/dbcnx.inc.php");
	require_once("CORE/rights.inc.php");
	require_once("CORE/log.inc.php");
	$dbcnf = array(
		"dbtype"=>"mysql",
		"dbhost"=>"localhost",
		"dbuser"=>$dbuser,
		"dbpass"=>$dbpass,
		"dbname"=>$dbname
	);
	global $connect;
	global $dbcnf;
	global $login;
	$login='admin';
	$connect = new DBCNX();
	$connect->connect($dbcnf);
	foreach($extensions as $ext_name) {
		$CODE_COVER->load(Extension::getFolder($ext_name));
		$testtag_file='conf/testtag.file';
		$handle = @fopen($testtag_file, "w+");
		@fwrite($handle,"RUNNING");
		@fclose($handle);
		try {
			$create_result=createDataBase(true,false);
			$ext_obj=new Extension($ext_name,Extension::getFolder($ext_name));
			$ext_obj->ThrowExcept=true;
			$item=new TestItem($ext_name,"00 Version ".$ext_obj->getPHPVersion());
			if ($connect->connected) {
				$set_of_ext[]=$ext_obj;
				$dep_names=split(" ",$ext_obj->getDepencies());
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
				dumpMysql();
				//echo "<!-- ".str_replace(array("{[newline]}","--","<",">"),array("\n","","&#139;","&#155;"),$msg)." -->\n";

				$item->success();
				$GlobalTest->addTests($item);
				$extDir=Extension::getFolder($ext_name);
				$fileList=array();
				$setup_item=null;
				$dh = @opendir($extDir);
				while(($file = @readdir($dh)) != false)
					if(substr($file,-9)=='.test.php') {
						$file_name=substr($file,0,-9);
						if ($file_name!='setup')
							$fileList[]=$file_name;
						else
							$setup_item=new TestItem($ext_name,"SETUP");
					}
				@closedir($dh);
				sort($fileList);
				if (is_file("$extDir/includes.inc.php"))
					require_once("$extDir/includes.inc.php");
				$inc=1;
				foreach($fileList as $file_name) {
					if (($num_test==-1) || ($num_test==$inc)) {
						$item=new TestItem($ext_name,sprintf('%02d ',$inc).str_replace('_APAS_','::',$file_name));
	
						restorMysql();
						if (!is_null($setup_item)) {
							if ($inc==1)
								$CODE_COVER->startCodeCover();
							$setup_item->runTest($extDir,$ext_name,'setup');
							if ($inc==1)
								$CODE_COVER->stopCodeCover();
							if (!is_null($setup_item->errorObj))
								$GlobalTest->addTests($setup_item);
						}
						
						$CODE_COVER->startCodeCover();
						try {
						  $item->runTest($extDir,$ext_name,$file_name);
						  $CODE_COVER->stopCodeCover();
						} catch(Exception $e) {
						  $CODE_COVER->stopCodeCover();
						  throw $e;
						}
						$GlobalTest->addTests($item);
					}
					$inc++;
				} 
			}
			else {
				$item->error($create_result);
				$GlobalTest->addTests($item);
			}
		} catch(Exception $e) {
			$item=new TestItem($ext_name,"Echec");
			$item->error($e);
			$GlobalTest->addTests($item);
		}
		unlink($testtag_file);
	}
}
else {
	$item_test=new TestItem("AllTest","echec");
	$item_test->error("Erreur de paramètres".$erreur);
	$GlobalTest->addTests($item_test);
}
echo $GlobalTest->AllTests($CODE_COVER->AllCover());
?> 
