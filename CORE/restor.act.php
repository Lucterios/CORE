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
// --- Last modification: Date 15 October 2009 21:54:46 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Restaurer les données
//@PARAM@ path
//@PARAM@ filename


//@LOCK:0

function restor($Params)
{
if (($ret=checkParams("CORE", "restor",$Params ,"path","filename"))!=null)
	return $ret;
$path=getParams($Params,"path",0);
$filename=getParams($Params,"filename",0);
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","restor",$Params);
$xfer_result->Caption="Restaurer les données";
//@CODE_ACTION@
$file_path = $path.$filename;
if (!is_file($file_path))
	$xfer_result->message('Fichier non trouvé.', XFER_DBOX_WARNING);
else if($xfer_result->confirme("Voulez-vous réaliser une restauration du fichier '$file_path'?{[newline]}{[bold]}Attention:{[/bold]}{[italic]}Toutes les données actuels seront perdu.{[italic]}")) {
	$xfer_result->m_context['file_path'] = $file_path;
	$xfer_result->redirectAction( new Xfer_Action('_Restaurer','','CORE','restorForm', FORMTYPE_MODAL, CLOSE_YES));
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
