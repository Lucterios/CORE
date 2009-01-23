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
// --- Last modification: Date 08 January 2009 21:53:08 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/groups.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des groupes
//@PARAM@ 


//@LOCK:0

function groups_APAS_liste($Params)
{
$self=new DBObj_CORE_groups();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","groups_APAS_liste",$Params);
$xfer_result->Caption="Liste des groupes";
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('group.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);
$img=new  Xfer_Comp_LabelForm('title');
$img->setValue("{[center]}{[underline]}{[bold]}Groupes Existants{[/bold]}{[/underline]}{[/center]}");
$img->setLocation(1,0);
$xfer_result->addComponent($img);

$self->whereAdd('(id!=1) AND (id!=2)');
$self->orderBy('weigth DESC');
$self->find();

$comp8= &new Xfer_Comp_Grid('group','Grille des paramètres');
$comp8->setDBObject($self,array('groupName','weigth'));
$comp8->addAction($self->NewAction("_Modifier",'edit.png','modifier', FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$comp8->addAction($self->NewAction("_Supprimer",'suppr.png','supprimer', FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$comp8->addAction($self->NewAction("_Ajouter",'add.png','modifier',FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
$comp8->setLocation(0, 1, 2, 1);
$xfer_result->addComponent($comp8);

$comp2= &new Xfer_Comp_LabelForm('Comp2');
$comp2->setValue('{[newline]}');
$comp2->setLocation(0, 2, 1, 1);
$xfer_result->addComponent($comp2);

$xfer_result->addAction($self->NewAction("_Fermer",'close.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
