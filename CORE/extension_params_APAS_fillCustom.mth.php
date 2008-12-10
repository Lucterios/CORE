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
// --- Last modification: Date 05 December 2008 21:49:46 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_params.tbl.php');
//@TABLES@

//@DESC@Ajouter un ensemble de parametres d'une extension
//@PARAM@ extensionName
//@PARAM@ xfer_result

function extension_params_APAS_fillCustom(&$self,$extensionName,$xfer_result)
{
//@CODE_ACTION@
$ParamsDesc=$xfer_result->ParamsDesc;
$DBParam=new DBObj_CORE_extension_params();
$DBParam->extensionId=$extensionName;
$DBParam->find();
while ($DBParam->fetch()) {
	$name_comp=$DBParam->paramName;
	if (array_key_exists($name_comp,$ParamsDesc)) {
		$lbl=new Xfer_Comp_LabelForm($name_comp.'Lbl');
		$lbl->setValue("{[bold]}".$DBParam->description."{[/bold]}");
		$lbl->setLocation($ParamsDesc[$name_comp][0], $ParamsDesc[$name_comp][1]);
		$xfer_result->addComponent($lbl);
		if ($xfer_result->ReadOnly)
		    	$cmp=$DBParam->getParamComponentReadOnly($name_comp);
		else
		    	$cmp=$DBParam->getParamComponent($name_comp);
		$cmp->setLocation($ParamsDesc[$name_comp][0]+1, $ParamsDesc[$name_comp][1]);
		$xfer_result->addComponent($cmp);
	}
}
return $xfer_result;
//@CODE_ACTION@
}

?>
