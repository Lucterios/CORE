<?php
// Action file write by SDK tool
// --- Last modification: Date 05 June 2008 21:44:46 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Restaurer les données
//@PARAM@ path
//@PARAM@ filename


//@LOCK:0

function restor($Params)
{
if (($ret=checkParams("CORE", "restor",$Params ,"path","filename"))!=null)
	return $ret;
$path=getParams($Params,"path",0);
$filename=getParams($Params,"filename",0);
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","restor",$Params);
$xfer_result->Caption="Restaurer les données";
//@CODE_ACTION@
if(! is_dir($path))$path = getcwd();
$file_path = $path.$filename;
if(! is_file($file_path))$xfer_result->message('Fichier non trouvé.', XFER_DBOX_WARNING);
else if($xfer_result->confirme("Voulez-vous réaliser une restauration du fichier '$file_path'?{[newline]}{[bold]}Attention:{[/bold]}{[italic]}Toutes les données actuels seront perdu.{[italic]}")) {
	$xfer_result->m_context['file_path'] = $file_path;
	$xfer_result->redirectAction( new Xfer_Action('_Restaurer','','CORE','restorForm', FORMTYPE_MODAL, CLOSE_YES));
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
