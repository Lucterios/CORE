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
// --- Last modification: Date 11 January 2010 21:44:00 By  ---

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
$xfer_result->Caption="Menu de l application";
//@CODE_ACTION@
global $connect, $login;

require_once('menufunction.inc.php');

// recup du menu dans la base
$last_menuItemId='';
$q = "SELECT menuItemId, extensionId, action, description, icon, shortcut, help FROM CORE_menu WHERE pere='' order by position";
$req = $connect->execute($q);
while(list($menuItemId, $extensionId, $action, $description, $icon, $shortcut, $help) = $connect->getRow($req))
{
	if ($last_menuItemId!=$menuItemId) {
		$menu = createMenuRecurse($menuItemId, $extensionId, $action, $description, $icon,'', $shortcut, $help);
		if($menu!=null)
			$xfer_result->addSubMenu($menu);
		$last_menuItemId=$menuItemId;
	}
}

$menu_tabs=new Xfer_Menu_Item("menu_sup","","");
$menu_status =new Xfer_Menu_Item("menu_status",'Résumé','status.png','CORE',"status",0,"","");
$menu_tabs->addSubMenu($menu_status);

$extpath="extensions";
if ($handle=opendir($extpath))
{
	while ($item=readdir($handle))
	{
		if (($item != ".") && ($item != "..") && is_dir("$extpath/$item") && is_file("$extpath/$item/menuTab.inc.php"))
		{
			$memo.=" file existe ";
			require_once "$extpath/$item/menuTab.inc.php";
			$function_name=$item."_menuTab";
			if (function_exists($function_name))
			{
				$new_Menu=$function_name();
				if (($new_Menu!=null) && (get_class($new_Menu)=='Xfer_Menu_Item'))
					$menu_tabs->addSubMenu($new_Menu);
			}
		}
	}
	closedir($handle);
}

$xfer_result->addSubMenu($menu_tabs);
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
