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
// --- Last modification: Date 02 March 2012 6:08:24 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Promouvoir un enregistrement
//@PARAM@ origininal_action
//@PARAM@ current_id
//@PARAM@ upclass

//@TRANSACTION:

//@LOCK:0

function upRecordClassAct($Params)
{
if (($ret=checkParams("CORE", "upRecordClassAct",$Params ,"origininal_action","current_id","upclass"))!=null)
	return $ret;
$origininal_action=getParams($Params,"origininal_action",0);
$current_id=getParams($Params,"current_id",0);
$upclass=getParams($Params,"upclass",0);

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","upRecordClassAct",$Params);
$xfer_result->Caption="Promouvoir un enregistrement";
//@CODE_ACTION@
$table_name=str_replace('/','_',$upclass);
global $connect;
$connect->execute("INSERT  INTO $table_name (superId) VALUES ($current_id)",true);

require_once("CORE/DBAbstract.inc.php");
require_once("extensions/".$upclass.".tbl.php");
$class_name="DBObj_".str_replace('/','_',$upclass);

$DBObj=new $class_name;
$DBObj->whereAdd("$table_name.superId=$current_id");
$DBObj->find();
if ($DBObj->fetch())
	$DBObj->updateData($Params);

list($ext,$act)=explode('/',$origininal_action);
$xfer_result->redirectAction(new Xfer_Action('','',$ext,$act,FORMTYPE_MODAL,CLOSE_YES));
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
