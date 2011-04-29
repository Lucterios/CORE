<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// Action file write by SDK tool
// --- Last modification: Date 28 April 2011 23:10:59 By  ---

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


//@LOCK:2

function extension_APAS_Delete($Params)
{
$self=new DBObj_CORE_extension();
$extension=getParams($Params,"extension",-1);
if ($extension>=0) $self->get($extension);

$self->lockRecord("extension_APAS_Delete");
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
$deps=$ext_obj->getDependants(array(),'',true);
$ext_list=array();
if (count($deps)==0)
	$text="";
else {
	$text="{[newline]}Cette extension dépent d'autres extensions:";
	foreach($deps as $dep)
	{
		$ext_dep=new Extension($dep,Extension::getFolder($dep));
		$ext_dep->throwExcept=true;
		$ext_list[]=$ext_dep;
		$text.="{[newline]} - ".$ext_dep->titre;
	}
}

if ($xfer_result->Confirme("Etes-vous sûre de vouloir supprimer l'extension '".$self->titre."'?$text{[newline]}Cela supprimera toutes les données en base."))
{
	$temp_path = getcwd()."/tmp/delete/";
	if(is_dir($temp_path))
		deleteDir($temp_path);
	global $connect;
	$connect->begin();
	try {
		foreach($ext_list as $ext_dep)
			$ext_dep->delete();
		$ext_obj->delete();
		$connect->commit();
		if(is_dir($temp_path))
			deleteDir($temp_path);
		foreach($ext_list as $ext_dep)
			if(is_dir($ext_dep->Dir))
				deleteDir($ext_dep->Dir);
		if(is_dir($ext_obj->Dir))
			deleteDir($ext_obj->Dir);
	}
	 catch(Exception $e) {
		$connect->rollback();
		$dh = @opendir($temp_path);
		while(($file_dir = @readdir($dh)) != false) {
			@rename($temp_path.$file_dir,$file_dir);
		}
		@closedir($dh);
		throw $e;
	}
	$xfer_result->redirectAction(new Xfer_Action('menu','','CORE','menu'));
}
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("extension_APAS_Delete");
	throw $e;
}
return $xfer_result;
}

?>
