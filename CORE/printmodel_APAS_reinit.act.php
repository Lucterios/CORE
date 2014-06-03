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
// --- Last modification: Date 03 September 2007 18:39:33 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/printmodel.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Réinitialiser un modèle
//@PARAM@ 
//@INDEX:print_model


//@LOCK:2

function printmodel_APAS_reinit($Params)
{
$self=new DBObj_CORE_printmodel();
$print_model=getParams($Params,"print_model",-1);
if ($print_model>=0) $self->get($print_model);

$self->lockRecord("printmodel_APAS_reinit");
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","printmodel_APAS_reinit",$Params);
$xfer_result->Caption='Réinitialiser un modèle';
$xfer_result->m_context['ORIGINE']="printmodel_APAS_reinit";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
if ($xfer_result->confirme("Etes-vous sûre de réinitialiser ce modèle?"))
{
  $printfile="extensions/".$self->extensionid."/".$self->identify.".prt.php";
  if (is_file($printfile))
  {
    require_once $printfile;
    $self->model= str_replace(array('#&39;'),array("'"),$MODEL_DEFAULT);
    $self->modify='n';
    $self->update();
  }
}
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("printmodel_APAS_reinit");
	throw $e;
}
return $xfer_result;
}

?>
