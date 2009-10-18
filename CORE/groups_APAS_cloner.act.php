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
// --- Last modification: Date 14 October 2009 22:04:19 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
require_once('CORE/extension_actions.tbl.php');
require_once('CORE/extension_rights.tbl.php');
require_once('CORE/group_rights.tbl.php');
require_once('CORE/groups.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Cloner un groupe
//@PARAM@ 
//@INDEX:group

//@TRANSACTION:

//@LOCK:0

function groups_APAS_cloner($Params)
{
$self=new DBObj_CORE_groups();
$group=getParams($Params,"group",-1);
if ($group>=0) $self->get($group);

global $connect;
$connect->begin();
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","groups_APAS_cloner",$Params);
$xfer_result->Caption="Cloner un groupe";
//@CODE_ACTION@
$new_group=new DBObj_CORE_groups;
$new_group->groupName='Copie de '.$self->groupName;
$new_group->weigth=$self->weigth;
$new_group->insert();

$group_rights=$self->getField('GroupRights');
while($group_rights->fetch()) {
	$new_group_right=new DBObj_CORE_group_rights;
	$new_group_right->groupref=$new_group->id;
	$new_group_right->rightref=$group_rights->rightref;
	$new_group_right->value=$group_rights->value;
	$new_group_right->insert();
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
