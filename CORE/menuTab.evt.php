<?php
// Event file write by SDK tool
// --- Last modification: Date 21 October 2011 4:36:42 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@

//@DESC@Evenement relatif au signal 'Menus spéciaux lié au volet de gauche' de 'CORE'
//@PARAM@ menuTabs
//@PARAM@ xfer

function CORE_APAS_menuTab(&$menuTabs,$xfer)
{
//@CODE_ACTION@
$new_Menu=new Xfer_Menu_Item("menu_status",'Résumé','status.png','CORE',"status",0,"","");
if ($xfer->checkActionRigth($new_Menu))
	$menuTabs->addSubMenu($new_Menu);
//@CODE_ACTION@
}

?>
