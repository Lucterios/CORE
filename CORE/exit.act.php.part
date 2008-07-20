<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//  // Action file write by SDK tool
// --- Last modification: Date 12 November 2007 19:01:28 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/sessions.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Desconnection
//@PARAM@ 


//@LOCK:0

function exit($Params)
{
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","exit",$Params);
$xfer_result->Caption='Desconnection';
//@CODE_ACTION@
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
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
