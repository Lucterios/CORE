<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//  // Action file write by SDK tool
// --- Last modification: Date 15 October 2009 21:55:29 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Gestion des sauvegardes
//@PARAM@ 


//@LOCK:0

function toolBackup($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","toolBackup",$Params);
$xfer_result->Caption="Gestion des sauvegardes";
//@CODE_ACTION@
$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1,6);
$img_title->setValue('backup_save.png');
$xfer_result->addComponent($img_title);
$title = new Xfer_Comp_LabelForm('title');
$title->setLocation(1,0,4);
$title->setValue("{[bold]}{[underline]}{[center]}Fichiers d'archivage{[/center]}{[/underline]}{[/bold]}");
$xfer_result->addComponent($title);
//
require_once("CORE/SimpleSelectorFile.mth.php");
SimpleSelectorFile($xfer_result,'backup/','bkf', false);
//
$up=new Xfer_Comp_UpLoad('UpFile');
$up->compress=true;
$up->HttpFile=true;
include_once("CORE/fichierFonctions.inc.php");
$up->maxsize=taille_max_dl_fichier();
$up->setNeeded(false);
$up->addFilter('.bkf');
$up->setValue('Archive à ré-insérer');
$up->setLocation(0,10,5);
$xfer_result->addComponent($up);
//
$xfer_result->addAction( new Xfer_Action("_Insérer","add.png",'CORE','archiveUpload',FORMTYPE_MODAL,CLOSE_NO));
$xfer_result->addAction( new Xfer_Action("_Extraire","edit.png",'CORE','archiveDownload',FORMTYPE_MODAL,CLOSE_NO));
$xfer_result->addAction( new Xfer_Action("A_nnuler","cancel.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
