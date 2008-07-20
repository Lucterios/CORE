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
// --- Last modification: Date 05 February 2008 23:36:54 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/groups.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Ajouter un groupe
//@PARAM@ 
//@INDEX:group


//@LOCK:0

function groups_APAS_modifier($Params)
{
$self=new DBObj_CORE_groups();
$group=getParams($Params,"group",-1);
if ($group>=0) $self->get($group);
try {
$xfer_result=&new Xfer_Container_Custom("CORE","groups_APAS_modifier",$Params);
$xfer_result->Caption='Ajouter un groupe';
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('group.png');
$img->setLocation(0,0,1,3);
$xfer_result->addComponent($img);

if ($group<=0)
	$xfer_result->Caption="Modifier un groupe";
$xfer_result->setDBObject($self,null,false,0,1);
$xfer_result->addAction($self->NewAction("_OK","ok.png","ajouter"));
$xfer_result->addAction($self->NewAction("_Annuler","cancel.png",""));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
