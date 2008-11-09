<?
header('Content-Type: text/xml; charset=UTF-8');

$run=false;
if (isset($_GET['extensions']) && isset($_GET['dbuser']) && isset($_GET['dbpass']) && isset($_GET['dbname'])) {
	$run=true;
	$extensions=split(';',$_GET['extensions']);
	$dbuser=$_GET['dbuser'];
	$dbpass=$_GET['dbpass'];
	$dbname=$_GET['dbname'];
}
elseif (count($argv)==5) {
	$run=true;
	$extensions=split(';',$argv[1]);
	$dbuser=$argv[2];
	$dbpass=$argv[3];
	$dbname=$argv[4];
}

include_once('CORE/UnitTest.inc.php');
$GlobalTest=new TestItem("LucteriosTest","");
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
	$login="admin";
	global $dbcnf;
	global $login;
	foreach($extensions as $ext_name) {
		global $connect;
		$create_result=createDataBase(true);
		$ext_obj=new Extension($ext_name,Extension::getFolder($ext_name));
		$item=new TestItem($ext_name,"Version ".$ext_obj->getPHPVersion());
		if ($connect->connected) {
			$set_of_ext[]=$ext_obj;
			$dep_names=split(" ",$ext_obj->getDepencies());
			foreach($dep_names as $name)
				if ($name!='')
					$set_of_ext[]=new Extension($name,Extension::getFolder($name));
			$set_of_ext = sortExtension($set_of_ext);
			foreach($set_of_ext as $ext)
				$ext->installComplete();
			$item->success();
			$GlobalTest->addTests($item);
			$extDir=Extension::getFolder($ext_name);
			$fileList=array();
			$dh = opendir($extDir);
			while(($file = readdir($dh)) != false)
				if(substr($file,-9)=='.test.php')
					$fileList[]=substr($file,0,-9);
			sort($fileList);
			foreach($fileList as $file_name) {
				$item=new TestItem($ext_name,str_replace('_APAS_','::',$file_name));
				$item->runTest($extDir,$ext_name,$file_name);
				$GlobalTest->addTests($item);
			} 
			closedir($dh);
		}
		else {
			$item->error($create_result);
			$GlobalTest->addTests($item);
		}
	}
}
else {
	$item_test=new TestItem("AllTest","echec");
	$item_test->error("Erreur de paramètres");
	$GlobalTest->addTests($item_test);
}
echo $GlobalTest->AllTests();
?> 
