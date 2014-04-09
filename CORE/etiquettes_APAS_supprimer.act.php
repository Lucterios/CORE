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
// --- Last modification: Date 27 August 2007 19:08:42 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/etiquettes.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Supprimer une étiquette
//@PARAM@ 
//@INDEX:etiquette


//@LOCK:2

function etiquettes_APAS_supprimer($Params)
{
$self=new DBObj_CORE_etiquettes();
$etiquette=getParams($Params,"etiquette",-1);
if ($etiquette>=0) $self->get($etiquette);

$self->lockRecord("etiquettes_APAS_supprimer");
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","etiquettes_APAS_supprimer",$Params);
$xfer_result->Caption='Supprimer une étiquette';
$xfer_result->m_context['ORIGINE']="etiquettes_APAS_supprimer";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@

if ($xfer_result->confirme('Etes-vous sure de vouloir supprimer cette étiquette?'))
	$self->delete();
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("etiquettes_APAS_supprimer");
	throw $e;
}
return $xfer_result;
}

?>
