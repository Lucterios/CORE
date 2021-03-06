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
// --- Last modification: Date 14 November 2008 1:03:59 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@Modifier l'utilisateur
//@PARAM@ Params

function users_APAS_ModifierUser(&$self,$Params)
{
//@CODE_ACTION@
$res=true;
if (array_key_exists('login',$Params))
{
	$users=new DBObj_CORE_users;
	$users->login=$Params['login'];
  	$users->find();
	while ($users->fetch()) {
		if ($users->id!=$self->id)
			$res=false;
	}
}
$self->setFrom($Params);
return $res;
//@CODE_ACTION@
}

?>
