<?php
// Action file write by SDK tool
// --- Last modification: Date 18 June 2008 22:25:26 By  ---

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
$lab_bord3 = new Xfer_Comp_LabelForm('bord3');
$lab_bord3->setLocation(1,0);
$lab_bord3->setValue('');
$lab_bord3->setSize(20,20);
//$xfer_result->addComponent($lab_bord3);
$lab = new Xfer_Comp_LabelForm('title');
$lab->setLocation(1,0,3);
$lab->setValue('{[center]}{[bold]}{[underline]}Configuration de votre système{[/underline]}{[/bold]}{[/center]}');
$xfer_result->addComponent($lab);
require_once"CORE/extensionManager.inc.php";
$extlist = getExtensions();
$set_of_ext = array();
foreach($extlist as $name => $path) {
	$set_of_ext[] = new Extension($name,$path);
}
$set_of_ext = sortExtension($set_of_ext,"");
foreach($set_of_ext as $ext) {
	if( is_file($ext->Dir."/config.inc.php")) {
		require_once($ext->Dir."/config.inc.php");
		$function_name = $ext->Name."_config";
		if( function_exists($function_name)) {
			$function_name($xfer_result);
		}
	}
}
//$img_title->setSize(100,100);
$xfer_result->addAction( new Xfer_Action("_Imprimer","print.png","CORE","printConf", FORMTYPE_MODAL, CLOSE_NO));
$xfer_result->addAction( new Xfer_Action("_Fermer","close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
