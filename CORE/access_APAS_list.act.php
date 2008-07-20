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
// --- Last modification: Date 09 November 2007 11:19:01 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/access.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des acces


//@LOCK:0

function access_APAS_list($Params)
{
$self=new DBObj_CORE_access();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","access_APAS_list",$Params);
$xfer_result->Caption='Liste des acces';
//@CODE_ACTION@
$self->find();
$DB_grid=new Xfer_Comp_Grid("access");
$DB_grid->setLocation(0,1);
$DB_grid->setDBObject($self);
$DB_grid->addAction($self->NewAction("_Modifier","edit.png","ajouter",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$DB_grid->addAction($self->NewAction("_Supprimer","suppr.png","supprimer",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
$DB_grid->addAction($self->NewAction("_Ajouter","ok.png","ajouter",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
$xfer_result->addComponent($DB_grid);

$xfer_result->addAction($self->NewAction("_Fermer","close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
