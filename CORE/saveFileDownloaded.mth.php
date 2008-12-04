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
//  // Method file write by SDK tool
// --- Last modification: Date 03 December 2008 19:22:54 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@

//@DESC@
//@PARAM@ Params
//@PARAM@ Name
//@PARAM@ PathFile

function saveFileDownloaded(&$self,$Params,$Name,$PathFile)
{
//@CODE_ACTION@
$uploadfile = $Params[$Name];
echo "<!-- Name:$Name => PathFile:$PathFile -->\n";
list($name_upload,$value_upload) = split(';',$uploadfile);
if($name_upload != '') {
	require_once "CORE/Lucterios_Error.inc.php";
	$value_upload=str_replace(array("\n"," ","\t"),"",$value_upload);
	echo "\n\n<!-- VALUE=||$value_upload|| -->\n\n";
	$content = base64_decode($value_upload);
	@unlink($PathFile);
	if($handle = @fopen($PathFile,'a')) {
		if( fwrite($handle,$content) == 0)
			throw new LucteriosException(IMPORTANT,"fichier non sauvé!");
		fclose($handle);
	}
	return is_file($PathFile);
}
else
	return false;
//@CODE_ACTION@
}

?>
