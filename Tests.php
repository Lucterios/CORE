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
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//
header('Content-Type: text/xml; charset=UTF-8');

function extractData($ext_name){
	require_once("CORE/DBSetup.inc.php");
	require_once("CORE/extensionManager.inc.php");
	$q = "";
	$ext_path=Extension::getFolder($ext_name);
	$ext = new Extension($ext_name,$ext_path);
	$table_list=$ext->getTableList();

 	$table_list=array_reverse($table_list);
	foreach($table_list as $table) {
		$q .= "TRUNCATE TABLE ".$ext_name."_".$table.";\n";
		if ($ext_name!='CORE') {		
			$q .= "TRUNCATE TABLE CORE_extension_params;\n";
		}
	}

 	$table_list=array_reverse($table_list);
	foreach($table_list as $table) {
		$class_name = 'DBObj_'.$ext_name.'_'.$table;
		$tbl = new $class_name;
		$setup = new DBObj_Setup($tbl);
		$line=$setup->extractSQLData();
		if(substr( trim($line),0,2) != '--') {
			if ( trim($line) != '') {
				$q .= $line;
			}
			$q .= "ALTER TABLE ".$ext_name."_".$table." AUTO_INCREMENT=100;\n";
		}
	}
	if ($ext_name!='CORE') {		
		require_once('CORE/extension_params.tbl.php');
		$tbl = new DBObj_CORE_extension_params;
		$setup = new DBObj_Setup($tbl);
		$line=$setup->extractSQLData();
		if(substr( trim($line),0,2) != '--') {
			if ( trim($line) != '') {
				$q .= $line;
			}
			$q .= "ALTER TABLE CORE_extension_params AUTO_INCREMENT=100;\n";
		}
	}
	return split("\n",$q);
}

function importData($queries){
	global $connect;
	$connect->begin();
	try {
		foreach($queries as $query_txt) {
			$query_txt=trim($query_txt);
			if((substr($query_txt,-1) == ';') && ($query_txt != '')) {
				$connect->execute($query_txt,true);
			}
		}
		$connect->commit();
 	} catch(Exception $e) {
		$connect->rollback();
		throw $e;
	}
}

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

include_once('CORE/UnitTest.inc.php');
$GlobalTest=new TestItem($title,"");
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
		$testtag_file='conf/testtag.file';
		$handle = @fopen($testtag_file, "w+");
		@fwrite($handle,"RUNNING");
		@fclose($handle);
		try {
			$create_result=createDataBase(true,false);
			$ext_obj=new Extension($ext_name,Extension::getFolder($ext_name));
			$item=new TestItem($ext_name,"00 Version ".$ext_obj->getPHPVersion());
			if ($connect->connected) {
				$set_of_ext[]=$ext_obj;
				$dep_names=split(" ",$ext_obj->getDepencies());
				foreach($dep_names as $name)
					if ($name!='')
						$set_of_ext[]=new Extension($name,Extension::getFolder($name));
				$set_of_ext = sortExtension($set_of_ext);
				foreach($set_of_ext as $ext)
					$ext->installComplete();
				$queries=extractData($ext_name);

				$item->success();
				$GlobalTest->addTests($item);
				$extDir=Extension::getFolder($ext_name);
				$fileList=array();
				$dh = opendir($extDir);
				$setup_item=null;
				while(($file = readdir($dh)) != false)
					if(substr($file,-9)=='.test.php') {
						$file_name=substr($file,0,-9);
						if ($file_name!='setup')
							$fileList[]=$file_name;
						else
							$setup_item=new TestItem($ext_name,"SETUP");
					}
				sort($fileList);
				if (is_file("$extDir/includes.inc.php"))
					require_once("$extDir/includes.inc.php");
				$inc=1;
				foreach($fileList as $file_name) {
					if (($num_test==-1) || ($num_test==$inc)) {
						$item=new TestItem($ext_name,sprintf('%02d ',$inc).str_replace('_APAS_','::',$file_name));
	
						importData($queries);
						if (!is_null($setup_item)) {						
							$setup_item->runTest($extDir,$ext_name,'setup');
							if (!is_null($setup_item->errorObj))
								$GlobalTest->addTests($setup_item);
							}
	
						$item->runTest($extDir,$ext_name,$file_name);
						$GlobalTest->addTests($item);
					}
					$inc++;
				} 
				closedir($dh);
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
	$item_test->error("Erreur de paramètres");
	$GlobalTest->addTests($item_test);
}
echo $GlobalTest->AllTests();
?> 
