<?php
// This file is part of Lucterios, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// Thanks to have payed a donation for using this module.
// 
// Lucterios is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios is distributed in the hope that it will be useful,
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


//@DESC@Restaurer les données
//@PARAM@ 


//@LOCK:0

function selectRestor($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","selectRestor",$Params);
$xfer_result->Caption="Restaurer les données";
//@CODE_ACTION@
$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1,6);
$img_title->setValue('backup_save.png');
$xfer_result->addComponent($img_title);
$title = new Xfer_Comp_LabelForm('title');
$title->setLocation(1,0,4);
$title->setValue("{[bold]}{[underline]}{[center]}Séléctionner le fichier d'archivage à restaurer.{[/center]}{[/underline]}{[/bold]}");
$xfer_result->addComponent($title);
//
global $ARCHIVE_PATH;
if (isset($ARCHIVE_PATH) && ($ARCHIVE_PATH!='') && is_dir($ARCHIVE_PATH))
	$path=$ARCHIVE_PATH;
else
	$path='backup/';
require_once("CORE/SimpleSelectorFile.mth.php");
SimpleSelectorFile($xfer_result,$path,'bkf', true);
//
$xfer_result->addAction( new Xfer_Action("_Restaurer","ok.png",'CORE','restor',0,1));
$xfer_result->addAction( new Xfer_Action("A_nnuler","cancel.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
