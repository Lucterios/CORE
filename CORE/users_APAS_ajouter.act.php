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
// --- Last modification: Date 05 February 2008 23:27:10 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Ajouter un utilisateur
//@PARAM@ 


//@LOCK:0

function users_APAS_ajouter($Params)
{
$self=new DBObj_CORE_users();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","users_APAS_ajouter",$Params);
$xfer_result->Caption='Ajouter un utilisateur';
//@CODE_ACTION@
$xfer_result=$self->Formulaire($xfer_result);

$xfer_result->addAction($self->NewAction("_OK",'ok.png','miseajour'));
$xfer_result->addAction($self->NewAction("_Annuler",'cancel.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
