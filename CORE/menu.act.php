<?php
// Action file write by SDK tool
// --- Last modification: Date 21 October 2011 4:34:21 By  ---

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
$xfer_result->signal("menuTab",$menu_tabs,$xfer_result);
$xfer_result->addSubMenu($menu_tabs);
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
