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
// --- Last modification: Date 04 March 2009 19:27:08 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@

//@DESC@
//@PARAM@ Params
//@PARAM@ Name
//@PARAM@ PathFile
//@PARAM@ httpFile

function saveFileDownloaded(&$self,$Params,$Name,$PathFile,$httpFile)
{
//@CODE_ACTION@
if ($httpFile) {
	if (is_array($_FILES[$Name])) {
		@move_uploaded_file($_FILES[$Name]['tmp_name'],$PathFile);
		return is_file($PathFile);
	}
	else
		throw new LucteriosException(IMPORTANT,"fichier $Name non transfèré");
}
else {
	$uploadfile = $Params[$Name];
	list($name_upload,$value_upload) = explode(';',$uploadfile);
	if($name_upload != '') {
		require_once "CORE/Lucterios_Error.inc.php";
		$value_upload=str_replace(array("\n"," ","\t"),"",$value_upload);
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
}
//@CODE_ACTION@
}

?>
