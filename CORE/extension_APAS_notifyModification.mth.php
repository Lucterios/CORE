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
// Method file write by SDK tool
// --- Last modification: Date 23 August 2011 20:08:40 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ value=0

function extension_APAS_notifyModification(&$self,$value=0)
{
//@CODE_ACTION@
global $rootPath;
if(!isset($rootPath)) $rootPath = "";
require_once("CORE/extensionManager.inc.php");
$ExtDirList=getExtensions($rootPath,false,true);
foreach($ExtDirList as $extName=>$extDir) {
	if (is_file("$extDir/extensionNotify.inc.php")){
			$memo.=" file existe ";
			require_once "$extDir/extensionNotify.inc.php";
			$function_name=$extName."_extensionNotify";
			if (function_exists($function_name)) {
				$function_name($value);
			}
	}
}
//@CODE_ACTION@
}

?>
