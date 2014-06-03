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
// --- Last modification: Date 02 March 2012 1:29:39 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_params.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Modifier un paramètre
//@PARAM@ extensionName

//@TRANSACTION:

//@LOCK:0

function extension_params_APAS_validerModif($Params)
{
if (($ret=checkParams("CORE", "extension_params_APAS_validerModif",$Params ,"extensionName"))!=null)
	return $ret;
$extensionName=getParams($Params,"extensionName",0);
$self=new DBObj_CORE_extension_params();

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","extension_params_APAS_validerModif",$Params);
$xfer_result->Caption="Modifier un paramètre";
//@CODE_ACTION@
foreach($Params as $name=>$value) {
	$DBObjextension_params=new DBObj_CORE_extension_params;
     $DBObjextension_params->extensionId=$extensionName;
     $DBObjextension_params->paramName=$name;
     if ($DBObjextension_params->find()>0) {
		$DBObjextension_params->fetch();
		$DBObjextension_params->value="$value";
		$DBObjextension_params->update();
     }
}
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
