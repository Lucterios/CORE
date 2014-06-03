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
// --- Last modification: Date 09 November 2007 11:18:42 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/access.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Ajouter/modifier un acces
//@INDEX:access


//@LOCK:2

function access_APAS_ajouter($Params)
{
$self=new DBObj_CORE_access();
$access=getParams($Params,"access",-1);
if ($access>=0) $self->get($access);

$self->lockRecord("access_APAS_ajouter");
try {
$xfer_result=new Xfer_Container_Custom("CORE","access_APAS_ajouter",$Params);
$xfer_result->Caption='Ajouter/modifier un acces';
$xfer_result->m_context['ORIGINE']="access_APAS_ajouter";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
if ($access>0)
	$xfer_result->Caption='Modifier un acces';
else
	$xfer_result->Caption='Ajouter un acces';

$xfer_result->setDBObject($self);
$xfer_result->addAction($self->NewAction("_OK","ok.png","valider",FORMTYPE_MODAL,CLOSE_YES));
$xfer_result->addAction($self->NewAction("_Annuler","cancel.png"));
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("access_APAS_ajouter");
	throw $e;
}
return $xfer_result;
}

?>
