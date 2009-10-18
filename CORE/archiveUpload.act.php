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
// --- Last modification: Date 15 October 2009 21:54:24 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Gestion des sauvegardes
//@PARAM@ path
//@PARAM@ filename


//@LOCK:0

function archiveUpload($Params)
{
if (($ret=checkParams("CORE", "archiveUpload",$Params ,"path","filename"))!=null)
	return $ret;
$path=getParams($Params,"path",0);
$filename=getParams($Params,"filename",0);
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","archiveUpload",$Params);
$xfer_result->Caption="Gestion des sauvegardes";
//@CODE_ACTION@
$file_path = $path.$filename;
$path_parts = pathinfo($file_path);
if(isset($path_parts['extension']))
	$file_path = substr($file_path,0,-1* strlen($path_parts['extension'])).'bkf';
else
	$file_path .= '.bkf';

if (!array_key_exists('UpFile',$Params)) {
	require_once "CORE/Lucterios_Error.inc.php";
	require_once "CORE/fichierFonctions.inc.php";
	throw new LucteriosException(IMPORTANT,"Pas de fichier défini!");
}
if ($_FILES['UpFile']['tmp_name']=='') {
	require_once "CORE/Lucterios_Error.inc.php";
	require_once "CORE/fichierFonctions.inc.php";
	throw new LucteriosException(IMPORTANT,"fichier non téléchargé!{[newline]}Taille maximum ".convert_taille(taille_max_dl_fichier()));
}
if(!is_dir($path))
	@mkdir($path,0777);
if (is_file($file_path))
	@unlink($file_path);
require_once("CORE/saveFileDownloaded.mth.php");
$ret = saveFileDownloaded($xfer_result,$Params,'UpFile',$file_path,true);
if (!is_file($file_path)) {
	require_once "CORE/Lucterios_Error.inc.php";
	throw new LucteriosException(IMPORTANT,"fichier '$file_path' non sauvé!");
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
