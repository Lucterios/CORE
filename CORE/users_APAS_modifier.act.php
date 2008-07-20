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
// --- Last modification: Date 03 September 2007 18:46:05 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Modifier un utilisateur
//@PARAM@ 
//@INDEX:user_actif


//@LOCK:2

function users_APAS_modifier($Params)
{
$self=new DBObj_CORE_users();
$user_actif=getParams($Params,"user_actif",-1);
if ($user_actif>=0) $self->get($user_actif);

$self->lockRecord("users_APAS_modifier");
try {
$xfer_result=&new Xfer_Container_Custom("CORE","users_APAS_modifier",$Params);
$xfer_result->Caption='Modifier un utilisateur';
$xfer_result->m_context['ORIGINE']="users_APAS_modifier";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
$xfer_result=$self->call("Formulaire",$xfer_result);

$xfer_result->addAction($self->NewAction("_OK",'ok.png','miseajour'));
$xfer_result->addAction($self->NewAction("_Annuler",'cancel.png'));
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("users_APAS_modifier");
	throw $e;
}
return $xfer_result;
}

?>
