<?php
// This file is part of Lucterios/Diacamma, a software developped by 'Le Sanglier du Libre' (http://www.sd-libre.fr)
// thanks to have payed a retribution for using this module.
// 
// Lucterios/Diacamma is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios/Diacamma is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Lucterios; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// library file write by Lucterios SDK tool

//@BEGIN@
function install_CORE($ExensionVersions) {
	if( is_dir("./extensions/"))
		$rootPath = "./";
	else
		$rootPath = "../";
	$ext_list = getExtensions($rootPath);
	$text = "## install_CORE ##{[newline]}";
	$right_null_nb = 0;
	global $connect;
	$q = "SELECT extension,action FROM CORE_extension_actions WHERE (rights IS NULL) OR (rights=0)";
	$ref = $connect->execute($q);
	while( is_array($row = $connect->getRow($ref))) {
		$extension_Id = $row[0];
		$action_name = $row[1];
		$DBextension = new DBObj_CORE_extension;
		$DBextension->get($extension_Id);
		$Name = $DBextension->extensionId;
		$Dir = $ext_list[$Name];
		require($Dir."setup.inc.php");
		foreach($actions as $act) {
			if(($act->action == $action_name) && file_exists($Dir.$act->action.".act.php")) {
				$DBext_rights = new DBObj_CORE_extension_rights;
				$DBext_rights->extension = $extension_Id;
				$DBext_rights->rightId = $act->rightNumber;
				$DBext_rights->find();
				$DBext_rights->fetch();
				$q = "UPDATE CORE_extension_actions SET rights='".$DBext_rights->id."' WHERE extension='$extension_Id' AND action='$action_name'";
				$connect->execute($q);
				$text .= "Correction:$Name|$extension_Id.$action_name- Right=".$DBext_rights->id."{[newline]}";
			}
		}
	}

	require_once("CORE/extension_params.tbl.php");
	$params = new DBObj_CORE_extension_params;
	$params->extensionId = 'CORE';
	$params->paramName = 'GUID';
	$params->find(false);
	if($params->fetch()) {
		require_once("CORE/GUIDGenerator.inc.php");
		$decode = uuidDecode($params->value);
		if(($params->value == '') || !$decode['check']) {
			$params->value = '';
			$ref = $connect->execute("SELECT value FROM CORE_extension_params WHERE extensionId='org_lucterios_updates' AND paramName='GUID'", true);
			if (is_array($row = $connect->getRow($ref))) {
				if ($row[0]!='')
					$params->value = $row[0];
			}
			if ($params->value=='')
				$params->value = uuid();
			$params->update();
		}
	}

	return $text;
}
//@END@
?>
