<?php
// This file is part of Lucterios/Diacamma, a software developped by 'Le Sanglier du Libre' (http://www.sd-libre.fr)
// thanks to have payed a retribution for using this module.
// 
// Lucterios/Diacamma is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios/Diacamma is distributed in the hope that it will be useful,
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
require_once('CORE/sessions.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Déconnexion 
//@PARAM@ 


//@LOCK:0

function exitConnection($Params)
{
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","exitConnection",$Params);
$xfer_result->Caption="Déconnexion ";
//@CODE_ACTION@
logAutre("Exit BEGIN");

global $GLOBAL;
$session=$GLOBAL["ses"];

logAutre("Exit session=$session");

$DBObjsessions=new DBObj_CORE_sessions;
$DBObjsessions->sid=$session;
$DBObjsessions->find();
while ($DBObjsessions->fetch()) {
	logAutre("Exit session N°".$DBObjsessions->id);
	$DBObjsessions->valid='n';
	$DBObjsessions->update();
}

logAutre("Exit END");
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
