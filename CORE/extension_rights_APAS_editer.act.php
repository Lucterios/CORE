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
// --- Last modification: Date 10 November 2008 12:19:07 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/group_rights.tbl.php');
require_once('CORE/extension_rights.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des droits de groupes
//@PARAM@ 
//@INDEX:right


//@LOCK:0

function extension_rights_APAS_editer($Params)
{
$self=new DBObj_CORE_extension_rights();
$right=getParams($Params,"right",-1);
if ($right>=0) $self->get($right);
try {
$xfer_result=new Xfer_Container_Custom("CORE","extension_rights_APAS_editer",$Params);
$xfer_result->Caption="Liste des droits de groupes";
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('group.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);

$comp2=new Xfer_Comp_LabelForm('Comp2');
$ext=$self->getField('extension');
$comp2->setValue("{[center]}{[underline]}{[bold]}Droits {[italc]}".$self->description."{[/italc]} de l'extension {[italc]}".$ext->toText()."{[/italc]}{[/bold]}{[/underline]}{[/center]}");
$comp2->setLocation(1, 0);
$xfer_result->addComponent($comp2);

$grouprights=$self->getField("groupright");
$group_right=new Xfer_Comp_Grid('groupright');
$group_right->setDBObject($grouprights,array('groupref','value'));
$group_right->setValue(0,'groupref',"{[italc]}Tous les groupes{[/italc]}");
$group_right->setValue(0,'value','Non');
$group_right->addAction($grouprights->NewAction("_Changer le droit",'edit.png','modify',FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$group_right->setLocation(0, 2,2);
$group_right->setSize(175, 400);
$xfer_result->addComponent($group_right);

$xfer_result->addAction(new Xfer_Action("_Fermer",'close.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
