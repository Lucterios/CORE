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
// --- Last modification: Date 03 September 2007 18:56:02 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Déverouillage
//@PARAM@ ORIGINE
//@PARAM@ TABLE_NAME
//@PARAM@ RECORD_ID


//@LOCK:0

function UNLOCK($Params)
{
if (($ret=checkParams("CORE", "UNLOCK",$Params ,"ORIGINE","TABLE_NAME","RECORD_ID"))!=null)
	return $ret;
$ORIGINE=getParams($Params,"ORIGINE",0);
$TABLE_NAME=getParams($Params,"TABLE_NAME",0);
$RECORD_ID=getParams($Params,"RECORD_ID",0);
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","UNLOCK",$Params);
$xfer_result->Caption='Déverouillage';
//@CODE_ACTION@
if (($RECORD_ID>0) && ($TABLE_NAME!='')) {
	require_once "CORE/DBObject.inc.php";
	$table_file_name=DBObj_Basic::getTableName($TABLE_NAME);
	require_once $table_file_name;

	$class="DBObj_$TABLE_NAME";
	$obj=new $class();
	try {
		$obj->get($RECORD_ID);
		$obj->unlockRecord($ORIGINE);
	} catch(Exception $e) {
		// Ne rien faire
	}
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
