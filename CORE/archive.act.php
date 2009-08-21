<?php
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//
// Action file write by SDK tool
// --- Last modification: Date 16 June 2008 22:36:03 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Sauvegarder les donn�es
//@PARAM@ path
//@PARAM@ filename


//@LOCK:0

function archive($Params)
{
if (($ret=checkParams("CORE", "archive",$Params ,"path","filename"))!=null)
	return $ret;
$path=getParams($Params,"path",0);
$filename=getParams($Params,"filename",0);
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","archive",$Params);
$xfer_result->Caption="Sauvegarder les donn�es";
//@CODE_ACTION@
if(! is_dir($path))$path = getcwd();
$file_path = $path.$filename;
$path_parts = pathinfo($file_path);
if(isset($path_parts['extension']))$file_path = substr($file_path,0,-1* strlen($path_parts['extension'])).'bkf';
else $file_path .= '.bkf';
if($xfer_result->confirme("Voulez-vous r�aliser une sauvegarde vers le fichier '$file_path'?")) {
	$xfer_result->m_context['file_path'] = $file_path;
	$xfer_result->redirectAction( new Xfer_Action('_Archiver','','CORE','archiveForm', FORMTYPE_MODAL, CLOSE_YES));
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
