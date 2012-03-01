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
// --- Last modification: Date 01 March 2012 8:30:17 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Promouvoir un enregistrement
//@PARAM@ origininal_action
//@PARAM@ current_table
//@PARAM@ current_id


//@LOCK:0

function upRecordClass($Params)
{
if (($ret=checkParams("CORE", "upRecordClass",$Params ,"origininal_action","current_table","current_id"))!=null)
	return $ret;
$origininal_action=getParams($Params,"origininal_action",0);
$current_table=getParams($Params,"current_table",0);
$current_id=getParams($Params,"current_id",0);
try {
$xfer_result=&new Xfer_Container_Custom("CORE","upRecordClass",$Params);
$xfer_result->Caption="Promouvoir un enregistrement";
//@CODE_ACTION@
require_once("CORE/DBAbstract.inc.php");
require_once(DBObj_Abstract::getTableName($current_table));
$class_name="DBObj_".$current_table;

$DBObj=new $class_name;
$DBObj->get($current_id);

$lbl = new Xfer_Comp_LabelForm("nameLabel");
$lbl->setLocation(0,0);
$lbl->setValue("{[bold]}Enregistrement{[/bold]}");
$xfer_result->addComponent($lbl);

$lbl = new Xfer_Comp_LabelForm("name");
$lbl->setLocation(1,0);
$lbl->setValue($DBObj->toText());
$xfer_result->addComponent($lbl);

$lbl = new Xfer_Comp_LabelForm("classLabel");
$lbl->setLocation(0,1);
$lbl->setValue("{[bold]}Type actuel{[/bold]}");
$xfer_result->addComponent($lbl);

$lbl = new Xfer_Comp_LabelForm("class");
$lbl->setLocation(1,1);
$lbl->setValue($DBObj->Title);
$xfer_result->addComponent($lbl);


$lbl = new Xfer_Comp_LabelForm("upclassLabel");
$lbl->setLocation(0,2);
$lbl->setValue("{[bold]}Prémouvoir en{[/bold]}");
$xfer_result->addComponent($lbl);

global $rootPath;
if(!isset($rootPath)) $rootPath = "";
require_once('CORE/extensionManager.inc.php');
$class_list = getDaughterClassesList($DBObj->extname.'/'.$DBObj->tblname,$rootPath);

$lbl = new Xfer_Comp_Select("upclass");
$lbl->setLocation(1,2);
$lbl->setSelect($class_list);
$xfer_result->addComponent($lbl);

$xfer_result->addAction( new Xfer_Action("_Promouvoir","ok.png",'CORE','upRecordClassAct',0,1));
$xfer_result->addAction( new Xfer_Action("A_nnuler","cancel.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
