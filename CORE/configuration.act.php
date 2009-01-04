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
// --- Last modification: Date 12 December 2008 17:09:48 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Configuration
//@PARAM@ 


//@LOCK:0

function configuration($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","configuration",$Params);
$xfer_result->Caption="Configuration";
//@CODE_ACTION@
$img_title = new Xfer_Comp_Image('img_title');
$img_title->setLocation(0,0,1);
$img_title->setValue('config.png');
$xfer_result->addComponent($img_title);
$lab = new Xfer_Comp_LabelForm('title');
$lab->setLocation(1,0,10);
$lab->setValue('{[newline]}{[center]}{[bold]}{[underline]}Configuration de votre système{[/underline]}{[/bold]}{[/center]}');
$xfer_result->addComponent($lab);
global $rootPath;
if(!isset($rootPath)) $rootPath = "";
require_once"CORE/extensionManager.inc.php";
$extlist = getExtensions($rootPath);
$set_of_ext = array();
foreach($extlist as $name => $path) {
	$set_of_ext[] = new Extension($name,$path);
}
$set_of_ext = sortExtension($set_of_ext,"");
foreach($set_of_ext as $ext) {
	if( is_file($ext->Dir."/config.inc.php")) {
		require_once($ext->Dir."/config.inc.php");
		$function_name = $ext->Name."_config";
		if( function_exists($function_name)) {
			$function_name($xfer_result);
		}
	}
}
$xfer_result->addAction( new Xfer_Action("_Imprimer","print.png","CORE","printConf", FORMTYPE_MODAL, CLOSE_NO));
$xfer_result->addAction( new Xfer_Action("_Fermer","close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
