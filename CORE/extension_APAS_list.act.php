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
// --- Last modification: Date 17 June 2008 22:32:03 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des extentions
//@PARAM@ 


//@LOCK:0

function extension_APAS_list($Params)
{
$self=new DBObj_CORE_extension();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","extension_APAS_list",$Params);
$xfer_result->Caption="Liste des extentions";
//@CODE_ACTION@
$img = new Xfer_Comp_Image('img');
$img->setValue('extensions.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);
$img = new Xfer_Comp_LabelForm('title');
$img->setValue("{[center]}{[underline]}{[bold]}Extensions actuellement installées{[/bold]}{[/underline]}{[/center]}");
$img->setLocation(1,0);
$xfer_result->addComponent($img);
$self->orderBy("extensionId");
$self->find();
$comp8 = & new Xfer_Comp_Grid('extension');
$comp8->setDBObject($self,array("titre",'Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild',"description","validite"));
$comp8->addAction($self->NewAction("_Droits et Actions",'edit.png','listactions', FORMTYPE_MODAL, CLOSE_NO, SELECT_SINGLE));
$comp8->addAction($self->NewAction('_Supprimer','suppr.png','Delete', FORMTYPE_MODAL, CLOSE_NO, SELECT_SINGLE));
$comp8->setLocation(0,1,2,2);
$xfer_result->addComponent($comp8);
$xfer_result->addAction( new Xfer_Action("_Fermer",'close.png'));
$xfer_result->addAction($self->NewAction('_Recharger','','reload', FORMTYPE_MODAL, CLOSE_NO));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
