<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// Action file write by SDK tool
// --- Last modification: Date 28 April 2011 20:42:52 By  ---

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
global $SECURITY_LOCK;
$SECURITY_LOCK->open(true);
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
	$temp_path = "./tmp/";
	$r = unlink($file_path);
	$ListToArchive = array("CORE/","extensions/","usr/","images/","index.php","coreIndex.php","install.php","Help.php");
	require_once("CORE/ArchiveTar.inc.php");
	$tar = new ArchiveTar($file_path,true);
	$tar->add($ListToArchive);
	require_once("CORE/DBSetup.inc.php");
	require_once("CORE/extensionManager.inc.php");
	$dir_list = getExtensions();
	foreach($dir_list as $ext_name => $ext_path) {
		$q = '';
		$SQL_file_name=$temp_path."data_".$ext_name.".sql";
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
		$handle = @fopen($SQL_file_name, "w+");
		if ($handle) {
			@fwrite($handle,$q);
			@fclose($handle);
		}
		else
			throw new LucteriosException(IMPORTANT,"Fichier $SQL_file_name non créable!");
		$tar->addModify($SQL_file_name,'',$temp_path);
		@unlink($SQL_file_name);
	}
	//
	if( is_file($file_path)) {
		$lbl->setValue("{[center]}{[bold]}Archivage Terminer.{[/bold]}{[/center]}");
		$lbl = new Xfer_Comp_LinkLabel("archive");
		$lbl->setLocation(0,2,2);
		$lbl->setFileToLoad($file_path);
		$lbl->setValue('Telecharger');
		$xfer_result->addComponent($lbl);
	}
	else
		$lbl->setValue("{[center]}{[bold]}Sauvegarde échouer!!{[/bold]}{[/center]}");
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
	$btn->setAction($xfer_result->getRefreshAction('Archiver'));
	$btn->JavaScript = "
	parent.refresh();
";
	$xfer_result->addComponent($btn);
	$xfer_result->addAction( new Xfer_Action('_Annuler','cancel.png','','', FORMTYPE_MODAL, CLOSE_YES));
}
$SECURITY_LOCK->close();
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
