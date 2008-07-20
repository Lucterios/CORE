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
// --- Last modification: Date 03 March 2008 22:15:01 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Supprimer une extension
//@PARAM@ 
//@INDEX:extension

//@TRANSACTION:

//@LOCK:2

function extension_APAS_Delete($Params)
{
$self=new DBObj_CORE_extension();
$extension=getParams($Params,"extension",-1);
if ($extension>=0) $self->get($extension);

$self->lockRecord("extension_APAS_Delete");

global $connect;
$connect->begin();
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","extension_APAS_Delete",$Params);
$xfer_result->Caption="Supprimer une extension";
$xfer_result->m_context['ORIGINE']="extension_APAS_Delete";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
if (($self->extensionId=='CORE') || ($self->extensionId=='applis'))
{
	require_once "CORE/Lucterios_Error.inc.php";
	throw new LucteriosException(MINOR,"Extension non supprimable");
}
require_once("CORE/extensionManager.inc.php");
$ext_obj=new Extension($self->extensionId,Extension::getFolder($self->extensionId));
$deps=$ext_obj->getDependants();
$ext_list=array();
if (count($deps)==0)
	$text="";
else {
	$text="{[newline]}Cette extension dépent d'autres extensions:";
	foreach($deps as $dep)
	{
		$ext_dep=new Extension($dep,Extension::getFolder($dep));
		$ext_list[]=$ext_dep;
		$text.="{[newline]} - ".$ext_dep->titre;
	}
}

if ($xfer_result->Confirme("Etes-vous sûre de vouloir supprimer l'extension '".$self->titre."'?$text{[newline]}Cela supprimera toutes les données en base."))
{
	foreach($ext_list as $ext_dep)
		$ext_dep->delete();
	$ext_obj->delete();
	$xfer_result->redirectAction(new Xfer_Action('menu','','CORE','menu'));
}
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	$self->unlockRecord("extension_APAS_Delete");
	throw $e;
}
return $xfer_result;
}

?>
