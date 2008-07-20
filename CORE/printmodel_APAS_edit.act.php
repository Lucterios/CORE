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
// --- Last modification: Date 03 September 2007 18:39:20 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/printmodel.tbl.php');
//@TABLES@
//@XFER:template
require_once('CORE/xfer_printing.inc.php');
//@XFER:template@


//@DESC@Editer un modèle
//@PARAM@ model=false
//@INDEX:print_model


//@LOCK:2

function printmodel_APAS_edit($Params)
{
$model=getParams($Params,"model",false);
$self=new DBObj_CORE_printmodel();
$print_model=getParams($Params,"print_model",-1);
if ($print_model>=0) $self->get($print_model);

$self->lockRecord("printmodel_APAS_edit");
try {
$xfer_result=&new Xfer_Container_Template("CORE","printmodel_APAS_edit",$Params);
$xfer_result->Caption='Editer un modèle';
$xfer_result->m_context['ORIGINE']="printmodel_APAS_edit";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
if (is_string($model))
{
  $self->setFrom($Params);
  $self->modify='o';
  $self->update();
  require_once('CORE/xfer_dialogBox.inc.php');
  $xfer_result=&new Xfer_Container_Acknowledge("CORE","printmodel_APAS_edit",$Params);
}
else
{
  $xfer_result->setModel($self->extensionid,
  $self->identify, $self->id,
  $self->model, $self->titre);
}
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("printmodel_APAS_edit");
	throw $e;
}
return $xfer_result;
}

?>
