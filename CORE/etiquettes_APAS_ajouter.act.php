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
// --- Last modification: Date 10 December 2008 20:00:32 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/etiquettes.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Ajouter/Modifier une étiquette
//@PARAM@ 
//@INDEX:etiquette


//@LOCK:2

function etiquettes_APAS_ajouter($Params)
{
$self=new DBObj_CORE_etiquettes();
$etiquette=getParams($Params,"etiquette",-1);
if ($etiquette>=0) $self->get($etiquette);

$self->lockRecord("etiquettes_APAS_ajouter");
try {
$xfer_result=new Xfer_Container_Custom("CORE","etiquettes_APAS_ajouter",$Params);
$xfer_result->Caption="Ajouter/Modifier une étiquette";
$xfer_result->m_context['ORIGINE']="etiquettes_APAS_ajouter";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
if ($self->id>0)
	$xfer_result->Caption="Modifier une étiquette";
else
	$xfer_result->Caption="Ajouter une étiquette";
$xfer_result->setDBObject($self,'nom',false,0,0,2);
$xfer_result->setDBObject($self,array('largeur_page','hauteur_page','largeur','hauteur','colonnes','lignes','marge_gauche','marge_sup','ecart_horizontal','ecart_vertical'),false,1);
$cmp=& $xfer_result->getComponents('largeur_page');
$cmp->setSize(20,100);

$img=new
Xfer_Comp_Image('img');
$img->setValue('etiquette.png');
$img->setLocation(2,1,1,10);
$xfer_result->addComponent($img);

$xfer_result->addAction($self->newAction("_Valider", "ok.png","ajouteract",FORMTYPE_MODAL,CLOSE_YES));
$xfer_result->addAction(new Xfer_Action("_Fermer", "close.png"));
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("etiquettes_APAS_ajouter");
	throw $e;
}
return $xfer_result;
}

?>
