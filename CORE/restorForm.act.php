<?php
// This file is part of Lucterios, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// Thanks to have payed a donation for using this module.
// 
// Lucterios is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios is distributed in the hope that it will be useful,
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
require_once('CORE/sessions.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Restauration de données
//@PARAM@ file_path


//@LOCK:0

function restorForm($Params)
{
if (($ret=checkParams("CORE", "restorForm",$Params ,"file_path"))!=null)
	return $ret;
$file_path=getParams($Params,"file_path",0);
try {
$xfer_result=new Xfer_Container_Custom("CORE","restorForm",$Params);
$xfer_result->Caption="Restauration de données";
//@CODE_ACTION@
global $SECURITY_LOCK;
$SECURITY_LOCK->open(true);

$sep = new Xfer_Comp_Image('sep');
$sep->setLocation(1,0);
$sep->setSize(1,350);
$sep->setValue('');
$xfer_result->addComponent($sep);

$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1,2);
$img_title->setSize(200,70);
$img_title->setValue('backup_restor.png');
$xfer_result->addComponent($img_title);
$lbl = new Xfer_Comp_LabelForm("info");
$lbl->setLocation(1,1);
$xfer_result->addComponent($lbl);

if(isset($xfer_result->m_context['RESTOR']))
	$restor=(int)$xfer_result->m_context['RESTOR'];
else
	$restor=0;
switch($restor) {
	case 0:
		$lbl->setValue("{[center]}{[bold]}Restauration en cours{[newline]}Merci de patienter.{[/bold]}{[/center]}");
		$btn = new Xfer_Comp_Button("Next");
		$btn->setLocation(1,1);
		$btn->setAction($xfer_result->getRefreshAction('Restaurer'));
		$btn->JavaScript = "parent.refresh();";
		$xfer_result->addComponent($btn);
		$xfer_result->m_context['RESTOR'] = 1;
		$xfer_result->addAction( new Xfer_Action('_Annuler','cancel.png'));
		break;
	case 1:
		require_once("CORE/ArchiveRestore.inc.php");	
		list($status, $result) = restorArchive($file_path);

		$lbl->setValue("{[center]}{[bold]}Restauration $status{[/bold]}{[/center]}{[newline]}$result");
		if ($status=='terminer')
			$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png','CORE','menu', FORMTYPE_MODAL, CLOSE_YES));
		else
			$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png'));
		break;
	default:
		$lbl->setValue("");
		$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png','CORE','menu', FORMTYPE_MODAL, CLOSE_YES));
		break;
}
$SECURITY_LOCK->close();
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
