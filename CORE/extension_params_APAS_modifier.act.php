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
// --- Last modification: Date 19 August 2009 19:42:45 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_params.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Modifier un paramètre
//@PARAM@ 


//@LOCK:0

function extension_params_APAS_modifier($Params)
{
$self=new DBObj_CORE_extension_params();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","extension_params_APAS_modifier",$Params);
$xfer_result->Caption="Modifier un paramètre";
//@CODE_ACTION@
$paramid=0;
foreach($Params as $param_name=>$param_value)
	if (substr($param_name,0,8)=='paramid_')
		$paramid=$param_value;
$self->get($paramid);
$xfer_result->m_context=array('paramid'=>$paramid);
$xfer_result->setDBObject($self,"description",true);
require_once 'CORE/setup_param.inc.php';
$cmp=$self->getParamComponent("value");
if ($cmp!=null)
{
	$lbl=new Xfer_Comp_Label("lblvalue");
	$lbl->setValue("{[bold]}Valeur{[/bold]}");
	$lbl->setLocation(0, 1);
	$xfer_result->addComponent($lbl);
	$cmp->setLocation(1, 1);
	$xfer_result->addComponent($cmp);
}
else
	$xfer_result->setDBObject($self,"value",true,1);
$xfer_result->addAction($self->NewAction("_OK",'ok.png','miseajour',FORMTYPE_MODAL,CLOSE_YES));
$xfer_result->addAction($self->NewAction("_Annuler",'cancel.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
