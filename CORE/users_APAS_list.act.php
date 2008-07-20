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
// --- Last modification: Date 05 February 2008 23:29:40 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des utilisateurs
//@PARAM@ 


//@LOCK:0

function users_APAS_list($Params)
{
$self=new DBObj_CORE_users();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","users_APAS_list",$Params);
$xfer_result->Caption='Liste des utilisateurs';
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('user.png');
$img->setLocation(0,0);
$xfer_result->addComponent($img);
$img=new  Xfer_Comp_LabelForm('title');
$img->setValue("{[center]}{[underline]}{[bold]}Utilisateurs du logiciel{[/bold]}{[/underline]}{[/center]}");
$img->setLocation(1,0);
$xfer_result->addComponent($img);

// Les actifs
$self->actif='o';
$self->orderBy("groupId, realName, login");
$self->find();

$lbl_actifs= &new Xfer_Comp_LabelForm('lbl_actifs');
$lbl_actifs->setValue('{[bold]}Liste des utilisateurs actifs:{[/bold]}{[newline]}{[newline]}');
$lbl_actifs->setLocation(0, 1, 2, 1);
$xfer_result->addComponent($lbl_actifs);

$user_actif= &new Xfer_Comp_Grid('user_actif','Grille des utilisateurs');
$user_actif->setDBObject($self,array("login","realName","groupId"));
$user_actif->addAction($self->NewAction("_Modifier",'edit.png','modifier',FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$user_actif->addAction($self->NewAction("_Désactiver",'suppr.png','desactiver',FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$user_actif->addAction($self->NewAction("_Ajouter",'add.png','ajouter',FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
$user_actif->setLocation(0, 2,2);
$xfer_result->addComponent($user_actif);

$sep= &new Xfer_Comp_LabelForm('separator');
$sep->setValue('');
$sep->setLocation(0, 3);
$xfer_result->addComponent($sep);

// Les inactifs
$self=new DBObj_CORE_users;
$self->actif='n';
$self->orderBy("groupId, realName, login");
$self->find();

$lbl_inactif= &new Xfer_Comp_LabelForm('lbl_inactif');
$lbl_inactif->setValue('{[bold]}Liste des utilisateurs inactifs:{[/bold]}{[newline]}{[newline]}');
$lbl_inactif->setLocation(0, 4,2);
$xfer_result->addComponent($lbl_inactif);

$user_desactif= &new Xfer_Comp_Grid('user_desactif','Grille des utilisateurs');
$user_desactif->setDBObject($self,array("login","realName","groupId"));
$user_desactif->addAction($self->NewAction("_Réactiver",'ok.png','reactiver', FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$user_desactif->setLocation(0, 5,2);
$xfer_result->addComponent($user_desactif);

$xfer_result->addAction($self->NewAction("_Fermer",'close.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
