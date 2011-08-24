<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// Action file write by SDK tool
// --- Last modification: Date 23 August 2011 15:07:30 By  ---

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
global $rootPath;
if(!isset($rootPath))
	$rootPath = "";
require_once "CORE/extensionManager.inc.php";
checkExtensions($rootPath);

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

require_once("CORE/extensionManager.inc.php");
$ExtDirList=getExtensions("",false,true);
foreach($ExtDirList as $extName=>$extDir) {
	if (is_file("$extDir/menuTab.inc.php")) {
		$memo.=" file existe ";
		require_once "$extDir/menuTab.inc.php";
		$function_name=$extName."_menuTab";
		if (function_exists($function_name))
		{
			$new_Menu=$function_name();
			if (($new_Menu!=null) && (get_class($new_Menu)=='Xfer_Menu_Item') && $xfer_result->checkActionRigth($new_Menu))
				$menu_tabs->addSubMenu($new_Menu);
		}
	}
}
$xfer_result->addSubMenu($menu_tabs);
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
