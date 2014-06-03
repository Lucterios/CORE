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
// --- Last modification: Date 04 February 2008 20:24:56 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/etiquettes.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des étiquettes
//@PARAM@ 


//@LOCK:0

function etiquettes_APAS_liste($Params)
{
$self=new DBObj_CORE_etiquettes();
try {
$xfer_result=new Xfer_Container_Custom("CORE","etiquettes_APAS_liste",$Params);
$xfer_result->Caption='Liste des étiquettes';
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('PrintReportLabel.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);
$img=new  Xfer_Comp_LabelForm('title');
$img->setValue("{[center]}{[underline]}{[bold]}Planche d'étiquettes{[/bold]}{[/underline]}{[/center]}");
$img->setLocation(1,0);
$xfer_result->addComponent($img);

$self->orderBy('nom');
$self->find();

$grid = new Xfer_Comp_Grid("etiquette");
$grid->setDBObject($self, array("nom","colonnes","lignes"));
$grid->addAction($self->newAction("_Modifier", "edit.png", "ajouter", FORMTYPE_MODAL, CLOSE_NO,SELECT_SINGLE));
$grid->addAction($self->newAction("_Ajouter", "add.png", "ajouter", FORMTYPE_MODAL, CLOSE_NO, SELECT_NONE));
$grid->addAction($self->newAction("_Supprimer", "del.png", "supprimer", FORMTYPE_MODAL, CLOSE_NO, SELECT_SINGLE));
$grid->setLocation(0,1,2);
$xfer_result->addComponent($grid);

$xfer_result->addAction(new Xfer_Action("_Fermer", "close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
