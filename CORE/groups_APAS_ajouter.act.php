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
// --- Last modification: Date 10 December 2008 19:56:54 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/group_rights.tbl.php');
require_once('CORE/groups.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Ajouter un groupe
//@PARAM@ 
//@INDEX:group

//@TRANSACTION:

//@LOCK:0

function groups_APAS_ajouter($Params)
{
$self=new DBObj_CORE_groups();
$group=getParams($Params,"group",-1);
if ($group>=0) $self->get($group);

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","groups_APAS_ajouter",$Params);
$xfer_result->Caption="Ajouter un groupe";
//@CODE_ACTION@
$self->setFrom($Params);
if ($group>0)
  $self->update();
else
  $self->insert();
$DBObjgroup_rights=new DBObj_CORE_group_rights;
$DBObjgroup_rights->CheckGroup($self->id, $self->weigth);
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
