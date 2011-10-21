<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// Action file write by SDK tool
// --- Last modification: Date 20 October 2011 22:18:27 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/finalreport.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Suppression d'impression
//@PARAM@ 
//@INDEX:print_report

//@TRANSACTION:

//@LOCK:0

function finalreport_APAS_delete($Params)
{
$self=new DBObj_CORE_finalreport();
$print_report=getParams($Params,"print_report",-1);
if ($print_report>=0) $self->get($print_report);

global $connect;
$connect->begin();
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","finalreport_APAS_delete",$Params);
$xfer_result->Caption="Suppression d'impression";
//@CODE_ACTION@
if (($res=$self->canBeDelete())!=0) {
	require_once("CORE/Lucterios_Error.inc.php");
	throw new LucteriosException(IMPORTANT,"Suppression de ".$self->toText()." impossible");
}
if($xfer_result->confirme("Etes vous sûre de vouloir supprimer ce rapport d'impression?{[newline]}Pour le regénérer, vous devez relancer l'impression à l'origine de ce rapport."))
	$self->deleteCascade();
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
