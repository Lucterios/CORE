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
// --- Last modification: Date 12 December 2008 18:19:06 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/group_rights.tbl.php');
require_once('CORE/users.tbl.php');
require_once('CORE/groups.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Supprimer un groupe
//@PARAM@ 
//@INDEX:group

//@TRANSACTION:

//@LOCK:2

function groups_APAS_supprimer($Params)
{
$self=new DBObj_CORE_groups();
$group=getParams($Params,"group",-1);
if ($group>=0) $self->get($group);

$self->lockRecord("groups_APAS_supprimer");

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","groups_APAS_supprimer",$Params);
$xfer_result->Caption="Supprimer un groupe";
$xfer_result->m_context['ORIGINE']="groups_APAS_supprimer";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
if($self->canBeDelete()!=0)
	$xfer_result->message("Suppression impossible: ce groupe est utilisé!");
else if($xfer_result->confirme("Etes vous sûre de vouloir supprimer ce groupe?"))
	$self->deleteCascade();
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	$self->unlockRecord("groups_APAS_supprimer");
	throw $e;
}
return $xfer_result;
}

?>
