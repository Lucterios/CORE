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
//  // Method file write by SDK tool
// --- Last modification: Date 08 August 2008 22:53:58 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/groups.tbl.php');
require_once('CORE/group_rights.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ right
//@PARAM@ weigth

function group_rights_APAS_CheckRight(&$self,$right,$weigth)
{
//@CODE_ACTION@
$return="";
$self->rightref=$right;
$self->find();
$groups=array();
while ($self->fetch())
   $groups[]=$self->groupref;

$notgroups=array();
$DBObjgroups=new DBObj_CORE_groups;
$nb=$DBObjgroups->find();
while ($DBObjgroups->fetch())
  if (!in_array($DBObjgroups->id,$groups))
    $notgroups[$DBObjgroups->id]=$DBObjgroups->weigth;
$return.="Nb nouveau Groupe/Rights=".count($notgroups);
foreach($notgroups as $grp=>$wgt)
{
   $gr=new DBObj_CORE_group_rights;
   $gr->rightref=$right;
   $gr->groupref=$grp;
   $gr->value=$wgt>=$weigth?'o':'n';
   $gr->insert();
}
return $return;
//@CODE_ACTION@
}

?>
