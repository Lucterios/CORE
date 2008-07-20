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
// --- Last modification: Date 01 February 2008 17:56:27 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:menu
require_once('CORE/xfer_menu.inc.php');
//@XFER:menu@


//@DESC@Menu de l application
//@PARAM@ 


//@LOCK:0

function menu($Params)
{
try {
$xfer_result=&new Xfer_Container_Menu("CORE","menu",$Params);
$xfer_result->Caption='Menu de l application';
//@CODE_ACTION@
global $connect, $login;

require_once('menufunction.inc.php');

// recup du menu dans la base
$q = "SELECT menuItemId, extensionId, action, description, icon, shortcut, help FROM CORE_menu WHERE pere='' order by position";
$req = $connect->execute($q);
while(list($menuItemId, $extensionId, $action, $description, $icon, $shortcut, $help) = $connect->getRow($req))
{
	$menu = createMenuRecurse($menuItemId, $extensionId, $action, $description, $icon,'', $shortcut, $help);
	if($menu!=null)
		$xfer_result->addSubMenu($menu);
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
