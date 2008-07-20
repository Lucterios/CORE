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
//  // library file write by SDK tool
// --- Last modification: Date 03 January 2008 22:04:13 By Laurent GAY ---

//@BEGIN@

require_once('CORE/xfer_menu.inc.php');

function createMenuRecurse($menuItemId, $extensionId, $action, $description, $icon, $modal, $shortcurt, $help)
{
	global $connect, $login;
	logAutre("MENU: $menuItemId, $extensionId, $action, $description, $help");
	if(($extensionId!="") && ($action!="") && !checkRight($login, $extensionId, $action))
		return null;
	else
	{
		$menu =& new Xfer_Menu_Item($menuItemId,$description,$icon,$extensionId,$action,$modal,$shortcurt,$help);
		// recup des sous menus:
		$asSubMenu = false;
		$q = "SELECT menuItemId, extensionId, action, description, icon, modal ,shortcut,help FROM CORE_menu WHERE pere='$menuItemId' order by position";
		$req2 = $connect->execute($q);
		while(list($menuItemId2, $extensionId2, $action2, $description2, $icon2, $modal2, $shortcut2,$help2) = $connect->getRow($req2))
		{
			if ($modal2=='o') $txtmodal=1; else $txtmodal='';
			$sub_menu=createMenuRecurse($menuItemId2, $extensionId2, $action2, $description2, $icon2, $txtmodal, $shortcut2,$help2);
			if ($sub_menu!=null)
			{
				$menu->addSubMenu($sub_menu);
				$asSubMenu = true;
			}
		}
		if($asSubMenu || ($extensionId!="" && $action!=""))
			return $menu;
		else
			return null;
	}
}












//@END@
?>
