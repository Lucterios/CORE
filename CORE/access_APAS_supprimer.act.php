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
// --- Last modification: Date 09 November 2007 11:19:11 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/access.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Supprimer un acces
//@INDEX:access

//@TRANSACTION:

//@LOCK:2

function access_APAS_supprimer($Params)
{
$self=new DBObj_CORE_access();
$access=getParams($Params,"access",-1);
if ($access>=0) $self->get($access);

$self->lockRecord("access_APAS_supprimer");

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","access_APAS_supprimer",$Params);
$xfer_result->Caption='Supprimer un acces';
$xfer_result->m_context['ORIGINE']="access_APAS_supprimer";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
$accesses=new DBObj_CORE_access;
if ($accesses->find()>1)
{
  if ($access>0)
  {
  if ($xfer_result->confirme("Etes-vous sûre de vouloir supprimer cette acces réseau?"))
     $self->delete();
  }
}
else
{
  require_once "CORE/xfer_dialogBox.inc.php";
  $xfer_result=new Xfer_Container_DialogBox("CORE","access_APAS_supprimer",$Params);
  $xfer_result->setTypeAndText("Suppression impossible.",4);
}
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	$self->unlockRecord("access_APAS_supprimer");
	throw $e;
}
return $xfer_result;
}

?>
