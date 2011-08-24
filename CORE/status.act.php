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
// --- Last modification: Date 23 August 2011 14:46:33 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Résumé
//@PARAM@ 


//@LOCK:0

function status($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","status",$Params);
$xfer_result->Caption="Résumé";
//@CODE_ACTION@
global $rootPath;
if(!isset($rootPath)) $rootPath = "";
require_once("CORE/extensionManager.inc.php");
$ExtDirList=getExtensions($rootPath,false,true);
foreach($ExtDirList as $extName=>$extDir) {
	if (is_file("$extDir/status.inc.php")){
		$memo.=" file existe ";
		require_once("$extDir/status.inc.php");
		$function_name=$extName."_status";
		if (function_exists($function_name)) {
			$function_name($xfer_result);
		}
	}
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
