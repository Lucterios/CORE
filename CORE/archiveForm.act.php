<?php
// Action file write by SDK tool
// --- Last modification: Date 16 June 2008 22:39:47 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Sauvegarder les données
//@PARAM@ file_path


//@LOCK:0

function archiveForm($Params)
{
if (($ret=checkParams("CORE", "archiveForm",$Params ,"file_path"))!=null)
	return $ret;
$file_path=getParams($Params,"file_path",0);
try {
$xfer_result=&new Xfer_Container_Custom("CORE","archiveForm",$Params);
$xfer_result->Caption="Sauvegarder les données";
//@CODE_ACTION@
if(isset($xfer_result->m_context['ARCHIVE'])) {
	$img_title = new Xfer_Comp_Image('img_title');
	$img_title->setLocation(0,0,1,2);
	$img_title->setValue('backup_save.png');
	$xfer_result->addComponent($img_title);
	$lbl = new Xfer_Comp_LabelForm("info");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
	$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png'));
	//
	$r = unlink($file_path);
	$ListToArchive = array("CORE/","extensions/","usr/","images/","index.php","coreIndex.php","install.php","Help.php");
	require_once("Archive/Tar.php");
	$tar = new Archive_Tar($file_path,'gz');
	$tar->addModify($ListToArchive);
	//if( is_dir('SDK'))$tar->addModify(array("SDK/"),"PHP");
	$q = '';
	require_once("CORE/DBSetup.inc.php");
	require_once("CORE/extensionManager.inc.php");
	$dir_list = getExtensions();
	foreach($dir_list as $ext_name => $ext_path) {
		$ext = new Extension($ext_name,$ext_path);
		foreach($ext->extend_tables as $table => $desc) {
			require_once($ext_path.$table.'.tbl.php');
			$class_name = 'DBObj_'.$ext_name.'_'.$table;
			$tbl = new $class_name;
			$setup = new DBObj_Setup($tbl);
			$q .= "-- Structure de la classe ".$ext_name."::$table\n";
			$q .= $setup->describeSQLTable( true)."\n";
			$q .= "-- Contenu de la classe ".$ext_name."::$table\n";
			$q .= $setup->extractSQLData()."\n\n";
		}
	}
	//
	$tar->addString("data.sql",$q);
	if( is_file($file_path)) {
		$lbl->setValue("{[center]}{[bold]}Archivage Terminer.{[/bold]}{[/center]}");
	}
	else $lbl->setValue("{[center]}{[bold]}Sauvegarde échouer!!{[/bold]}{[/center]}");
}
else {
	$xfer_result->m_context['ARCHIVE'] = 1;
	$img_title = new Xfer_Comp_Image('img_title');
	$img_title->setLocation(0,0,1,2);
	$img_title->setValue('backup_save.png');
	$xfer_result->addComponent($img_title);
	$lbl = new Xfer_Comp_LabelForm("info");
	$lbl->setLocation(1,0);
	$lbl->setValue("{[center]}{[bold]}Archivage en cours.{[newline]}Merci de patienter.{[/bold]}{[/center]}");
	$xfer_result->addComponent($lbl);
	$btn = new Xfer_Comp_Button("Next");
	$btn->setLocation(1,1);
	$btn->setAction( new Xfer_Action('Archiver','','CORE','archiveForm', FORMTYPE_REFRESH, CLOSE_NO));
	$btn->JavaScript = "
	parent.refresh();
";
	$xfer_result->addComponent($btn);
	$xfer_result->addAction( new Xfer_Action('_Annuler','cancel.png','','', FORMTYPE_MODAL, CLOSE_YES));
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
