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
// --- Last modification: Date 12 March 2009 23:52:56 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@Voir un utilisateur
//@PARAM@ posX
//@PARAM@ posY
//@PARAM@ xfer_result

function users_APAS_show(&$self,$posX,$posY,$xfer_result)
{
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('user.png');
$img->setLocation($posX,$posY++,1,4);
$xfer_result->addComponent($img);

$xfer_result->setDBObject($self,"login",true,$posY++,$posX+1);
$xfer_result->setDBObject($self,"realName",true,$posY++,$posX+1);
return $xfer_result;
//@CODE_ACTION@
}

?>
