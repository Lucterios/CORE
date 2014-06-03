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
// --- Last modification: Date 03 March 2008 21:21:40 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_rights.tbl.php');
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des actions d'une extension
//@PARAM@ 
//@INDEX:extension


//@LOCK:0

function extension_APAS_listactions($Params)
{
$self=new DBObj_CORE_extension();
$extension=getParams($Params,"extension",-1);
if ($extension>=0) $self->get($extension);
try {
$xfer_result=new Xfer_Container_Custom("CORE","extension_APAS_listactions",$Params);
$xfer_result->Caption="Liste des actions d'une extension";
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('extensions.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);

$comp2=new Xfer_Comp_LabelForm('Comp2');
$comp2->setValue("{[center]}{[underline]}{[bold]}Droits et Actions de l'extension {[italc]}".$self->titre."{[/italc]}{[/bold]}{[/underline]}{[/center]}");
$comp2->setLocation(1, 0);
$xfer_result->addComponent($comp2);

$rights=$self->getField("rights",'','weigth');
$comp8=new Xfer_Comp_Grid('right');
$comp8->setDBObject($rights,array("description","actions"));
$comp8->addAction($rights->NewAction("_Editer les droits",'edit.png','editer', FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$comp8->setLocation(0, 1,2);
$comp8->setSize(450, 700);
$xfer_result->addComponent($comp8);

$xfer_result->addAction(new Xfer_Action("_Fermer",'close.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
