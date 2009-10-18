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
//  // Method file write by SDK tool
// --- Last modification: Date 14 October 2009 22:37:36 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_actions.tbl.php');
require_once('CORE/extension_rights.tbl.php');
require_once('CORE/group_rights.tbl.php');
//@TABLES@

//@DESC@getList de group_rights
//@PARAM@ Params

function group_rights_APAS_getGrid(&$self,$Params)
{
//@CODE_ACTION@
$grid = new Xfer_Comp_Grid("groupright");
$grid->setDBObject($self, array('rightref[description]','rightref[actions]','value'),"",$Params);
$grid->setSize(400,750);
$grid->addAction($self->NewAction('Changer','','modify',FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
return $grid;
//@CODE_ACTION@
}

?>
