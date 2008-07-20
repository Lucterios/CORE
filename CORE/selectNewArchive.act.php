<?php
// Action file write by SDK tool
// --- Last modification: Date 05 June 2008 22:04:18 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Sauvegarde des données
//@PARAM@ 


//@LOCK:0

function selectNewArchive($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","selectNewArchive",$Params);
$xfer_result->Caption="Sauvegarde des données";
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
require_once("CORE/selectorFile.mth.php");
$ret = selectorFile($xfer_result,$Params,'bkf', false);
//
if($ret)$xfer_result->addAction( new Xfer_Action("_Archiver","ok.png",'CORE','archive',0,1));
$xfer_result->addAction( new Xfer_Action("A_nnuler","cancel.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
