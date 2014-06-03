<?php
// This file is part of Lucterios/Diacamma, a software developped by 'Le Sanglier du Libre' (http://www.sd-libre.fr)
// thanks to have payed a retribution for using this module.
// 
// Lucterios/Diacamma is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios/Diacamma is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Lucterios; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// Action file write by Lucterios SDK tool

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
$xfer_result=new Xfer_Container_Custom("CORE","archiveForm",$Params);
$xfer_result->Caption="Sauvegarder les données";
//@CODE_ACTION@
global $SECURITY_LOCK;
$SECURITY_LOCK->open(true);
if(isset($xfer_result->m_context['ARCHIVE'])) {
	$img_title = new Xfer_Comp_Image('img_title');
	$img_title->setLocation(0,0,1,3);
	$img_title->setValue('backup_save.png');
	$xfer_result->addComponent($img_title);
	$lbl = new Xfer_Comp_LabelForm("info");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
	$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png'));
	require_once('CORE/ArchiveRestore.inc.php');
	createArchive($file_path);
	//
	if( is_file($file_path)) {
		$lbl->setValue("{[center]}{[bold]}Archivage Terminé.{[/bold]}{[/center]}");
		$btn = new Xfer_Comp_Button("archive");
		$btn->setLocation(1,2);
		$btn->setAction( new Xfer_Action('_Télécharger','up.png','CORE','archiveDownload', FORMTYPE_MODAL, CLOSE_YES));
		$xfer_result->addComponent($btn);
	}
	else
		$lbl->setValue("{[center]}{[bold]}Sauvegarde échouée !!{[/bold]}{[/center]}");
}
else {
	$xfer_result->m_context['ARCHIVE'] = 1;
	$img_title = new Xfer_Comp_Image('img_title');
	$img_title->setLocation(0,0,1,3);
	$img_title->setValue('backup_save.png');
	$xfer_result->addComponent($img_title);
	$lbl = new Xfer_Comp_LabelForm("info");
	$lbl->setLocation(1,0);
	$lbl->setSize(50,200);
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
