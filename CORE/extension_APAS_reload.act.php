<?php
// Action file write by SDK tool
// --- Last modification: Date 21 October 2011 5:33:26 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Recharger les configurations
//@PARAM@ 


//@LOCK:0

function extension_APAS_reload($Params)
{
$self=new DBObj_CORE_extension();
try {
$xfer_result=new Xfer_Container_Custom("CORE","extension_APAS_reload",$Params);
$xfer_result->Caption="Recharger les configurations";
//@CODE_ACTION@
global $SECURITY_LOCK;
$SECURITY_LOCK->open(true);

require_once"CORE/extensionManager.inc.php";
$install = "";
$extlist = getExtensions();
$set_of_ext = array();
foreach($extlist as $name => $path) {
	$set_of_ext[] = new Extension($name,$path);
}
$set_of_ext = sortExtension($set_of_ext,"");
$ExtensionDescription = array();
foreach($set_of_ext as $ext) {
	$install .= "{[center]}{[bold]}".$ext->Name."{[/bold]}{[/center]}";
	$ExtensionDescription[$ext->Name] = $ext->getVersions();
	$ext->installComplete();
	$install .= $ext->message;
}
$install .= Extension:: callApplicationPostInstallation($ExtensionDescription);
$lbl = new Xfer_Comp_LabelForm("info");
$lbl->setLocation(1,0);
$lbl->setValue($install);
$xfer_result->addComponent($lbl);
$xfer_result->addAction( new Xfer_Action('_Fermer','close.png','','', FORMTYPE_MODAL, CLOSE_YES));

$xfer_result->signal("extensionNotify",$xfer_result,0);
$SECURITY_LOCK->close();
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
