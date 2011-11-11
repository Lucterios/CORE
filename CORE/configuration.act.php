<?php
// Action file write by SDK tool
// --- Last modification: Date 21 October 2011 4:22:23 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Configuration
//@PARAM@ 


//@LOCK:0

function configuration($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","configuration",$Params);
$xfer_result->Caption="Configuration";
//@CODE_ACTION@
$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1);
$img_title->setValue('config.png');
$xfer_result->addComponent($img_title);
$lab = new Xfer_Comp_LabelForm('title');
$lab->setLocation(1,0,7);
$lab->setValue('{[newline]}{[center]}{[bold]}{[underline]}Configuration de votre système{[/underline]}{[/bold]}{[/center]}');
$xfer_result->addComponent($lab);

$xfer_result->signal("config",$xfer_result);

$xfer_result->addAction( new Xfer_Action("_Imprimer","print.png","CORE","printConf", FORMTYPE_MODAL, CLOSE_NO));
$xfer_result->addAction( new Xfer_Action("_Fermer","close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
