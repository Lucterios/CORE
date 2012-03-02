<?php
// 	This file is part of Lucterios/Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Lucterios/Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Lucterios/Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY// Action file write by SDK tool
// --- Last modification: Date 02 March 2012 1:32:32 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Sauvegarde d'archive
//@PARAM@ 


//@LOCK:0

function selectNewArchive($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","selectNewArchive",$Params);
$xfer_result->Caption="Sauvegarde d'archive";
//@CODE_ACTION@
$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1,6);
$img_title->setValue('backup_save.png');
$xfer_result->addComponent($img_title);
$title = new Xfer_Comp_LabelForm('title');
$title->setLocation(1,0,4);
$title->setValue("{[bold]}{[underline]}{[center]}Séléctionner un nouveau fichier d'archivage.{[/center]}{[/underline]}{[/bold]}");
$xfer_result->addComponent($title);
//
require_once("CORE/SimpleSelectorFile.mth.php");
SimpleSelectorFile($xfer_result,'backup/','bkf', false);
//
$xfer_result->addAction( new Xfer_Action("_Sauver","ok.png",'CORE','archive',FORMTYPE_MODAL,CLOSE_YES));
$xfer_result->addAction( new Xfer_Action("A_nnuler","cancel.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
