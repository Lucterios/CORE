<?php
// This file is part of Lucterios/Diacamma, a software developped by 'Le Sanglier du Libre' (http://www.sd-libre.fr)
// thanks to have payed a retribution for using this module.
// 
// Lucterios/Diacamma is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios/Diacamma is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Lucterios; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// Action file write by Lucterios SDK tool

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Sauvegarder les données
//@PARAM@ path
//@PARAM@ filename


//@LOCK:0

function archiveDownload($Params)
{
if (($ret=checkParams("CORE", "archiveDownload",$Params ,"path","filename"))!=null)
	return $ret;
$path=getParams($Params,"path",0);
$filename=getParams($Params,"filename",0);
try {
$xfer_result=&new Xfer_Container_Custom("CORE","archiveDownload",$Params);
$xfer_result->Caption="Sauvegarder les données";
//@CODE_ACTION@
$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1,2);
$img_title->setValue('backup_save.png');
$xfer_result->addComponent($img_title);

$lbl = new Xfer_Comp_LabelForm("info");
$lbl->setLocation(1,0);
$xfer_result->addComponent($lbl);
$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png'));

$file_path = $path.$filename;
$path_parts = pathinfo($file_path);
if(isset($path_parts['extension']))
	$file_path = substr($file_path,0,-1* strlen($path_parts['extension'])).'bkf';
else
	$file_path .= '.bkf';

if( is_file($file_path)) {
	$lbl->setValue("{[center]}{[bold]}Ancienne archive.{[/bold]}{[/center]}");

	$down=new Xfer_Comp_DownLoad('archive');
	$down->compress=false;
	$down->HttpFile=false;
	$down->setValue($filename);
	$down->setFileName("CORE/loadfile.inc.php?pass=".md5_file($file_path)."&filename=".urlencode($file_path));
	$down->setLocation(0,2,2);
	$xfer_result->addComponent($down);
}
else
	$lbl->setValue("{[center]}{[bold]}Fichier non trouvé!!{[/bold]}{[/center]}");
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
