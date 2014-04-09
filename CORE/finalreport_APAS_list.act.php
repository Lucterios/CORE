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
// --- Last modification: Date 21 October 2011 19:02:50 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/finalreport.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des impression
//@PARAM@ 


//@LOCK:0

function finalreport_APAS_list($Params)
{
$self=new DBObj_CORE_finalreport();
try {
$xfer_result=new Xfer_Container_Custom("CORE","finalreport_APAS_list",$Params);
$xfer_result->Caption="Liste des impression";
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('PrintReportSave.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);
$img=new  Xfer_Comp_LabelForm('title');
$img->setValue("{[center]}{[underline]}{[bold]}Impression sauvegard�es{[/bold]}{[/underline]}{[/center]}");
$img->setLocation(1,0);
$xfer_result->addComponent($img);

$q="SELECT * FROM CORE_finalreport ";
$q.="ORDER BY date DESC,heure DESC,extensionid,identify,reference";
$self->query($q);

$DB_grid=new Xfer_Comp_Grid("print_report");
$DB_grid->setLocation(0,1,2);
$DB_grid->setDBObject($self,array('titre','nature','date','heure'),"",$Params);
$DB_grid->addAction($self->NewAction("_R�imprimer", "print.png", "reprint", FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
//$DB_grid->addAction($self->NewAction('R_eg�n�rer','refresh.png','regenerer',FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$DB_grid->addAction($self->NewAction('_Suppression','suppr.png','delete',FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));

$xfer_result->addComponent($DB_grid);
$xfer_result->addAction($self->NewAction("_Fermer",'close.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
