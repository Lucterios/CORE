<?php
// Action file write by SDK tool
// --- Last modification: Date 21 October 2011 4:02:03 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Résumé
//@PARAM@ 


//@LOCK:0

function status($Params)
{
try {
$xfer_result=new Xfer_Container_Custom("CORE","status",$Params);
$xfer_result->Caption="Résumé";
//@CODE_ACTION@
global $SECURITY_LOCK;
if ($SECURITY_LOCK->isLock()==0)
	$xfer_result->signal("status",$xfer_result);
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
