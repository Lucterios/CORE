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
// --- Last modification: Date 08 August 2008 23:27:34 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_rights.tbl.php');
require_once('CORE/group_rights.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ group
//@PARAM@ weigth

function group_rights_APAS_CheckGroup(&$self,$group,$weigth)
{
//@CODE_ACTION@
$return="";
$self->groupref=$group;
$self->find();
$rights=array();
while ($self->fetch())
   $rights[]=$self->rightref;

$notrights=array();
$DBObjextension_rights=new DBObj_CORE_extension_rights;
$nb=$DBObjextension_rights->find();
while ($DBObjextension_rights->fetch())
  if (!in_array($DBObjextension_rights->id,$rights))
    $notrights[$DBObjextension_rights->id]=$DBObjextension_rights->weigth;
$return.="Nb nouveau Groupe/Rights=".count($notrights);
foreach($notrights as $rgh=>$wgt)
{
   $gr=new DBObj_CORE_group_rights;
   $gr->rightref=$rgh;
   $gr->groupref=$group;
   $gr->value=$wgt<=$weigth?'o':'n';
   $gr->insert();
}
return $return;
//@CODE_ACTION@
}

?>
