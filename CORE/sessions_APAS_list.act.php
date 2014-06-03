<?php
// 	This file is part of Lucterios/Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Lucterios/Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Lucterios/Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY// Action file write by SDK tool
// --- Last modification: Date 14 May 2012 7:57:05 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/sessions.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Consultation des session
//@PARAM@ 


//@LOCK:0

function sessions_APAS_list($Params)
{
$self=new DBObj_CORE_sessions();
try {
$xfer_result=new Xfer_Container_Custom("CORE","sessions_APAS_list",$Params);
$xfer_result->Caption="Consultation des session";
//@CODE_ACTION@
$titre_actuel=new Xfer_Comp_LabelForm("titre_actuel");
$titre_actuel->setLocation(0,0,2,1);
$titre_actuel->setValue("{[bold]}Utilisateurs connectés actuellement{[/bold]}");
$xfer_result->addComponent($titre_actuel);

//$q="SELECT id, sid, uid, FROM_UNIXTIME( dtcreate, '%d/%m/%Y %T' ) AS dtcreate, FROM_UNIXTIME( dtmod, '%d/%m/%Y %T' ) AS dtmod, ip, valid FROM CORE_sessions WHERE valid = 'o' ORDER BY dtcreate";
//$self->query($q);
$self->whereAdd("valid='o'");
$self->orderBy('dtcreate DESC');
$self->find();
$access_actuel=new Xfer_Comp_Grid("access_actuel");
$access_actuel->setLocation(0,1,1,1);
$access_actuel->setDBObject($self,array("uid","dateCreate","dateModify","ip"));
$access_actuel->addAction($self->NewAction("_Tuer","suppr.png","killsession", FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$xfer_result->addComponent($access_actuel);

$sep=new Xfer_Comp_LabelForm("sep");
$sep->setLocation(0,2,2,1);
$sep->setValue("{[newline]}");
$xfer_result->addComponent($sep);

$titre_ancien=new Xfer_Comp_LabelForm("titre_ancien");
$titre_ancien->setLocation(0,3,2,1);
$titre_ancien->setValue("{[bold]}Utilisateurs anciennement connectés{[/bold]}");
$xfer_result->addComponent($titre_ancien);

//$q="SELECT id, sid, uid, FROM_UNIXTIME( dtcreate, '%d/%m/%Y %T' ) AS dtcreate, FROM_UNIXTIME( dtmod, '%d/%m/%Y %T' ) AS dtmod, ip, valid FROM CORE_sessions WHERE valid = 'n' ORDER BY dtcreate";
//$self->query($q);
$DBSess=new DBObj_CORE_sessions;
$DBSess->whereAdd("valid='n'");
$DBSess->orderBy('dtcreate DESC');
$DBSess->find();
$access_ancien=new Xfer_Comp_Grid("access_ancien");
$access_ancien->setLocation(0,4,1,1);
$access_ancien->setDBObject($DBSess,array("uid","dateCreate","dateModify","ip"));
$xfer_result->addComponent($access_ancien);


$xfer_result->addAction($self->NewAction("_Fermer","close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
