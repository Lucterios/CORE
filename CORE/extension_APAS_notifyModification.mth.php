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
// --- Last modification: Date 04 July 2011 22:47:35 By  ---

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
$extpath=$rootPath."extensions";
if ($handle=opendir($extpath))
{
	while ($item=readdir($handle))
	{
		if (($item != ".") && ($item != "..") && is_dir("$extpath/$item") && is_file("$extpath/$item/extensionNotify.inc.php"))
		{
			$memo.=" file existe ";
			require_once "$extpath/$item/extensionNotify.inc.php";
			$function_name=$item."_extensionNotify";
			if (function_exists($function_name))
			{
				$function_name($value);
			}
		}
	}
	closedir($handle);
}
//@CODE_ACTION@
}

?>
