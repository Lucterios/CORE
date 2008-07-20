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
//  // table file write by SDK tool
// --- Last modification: Date 01 March 2008 15:14:42 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_group_rights extends DBObj_Basic
{
	var $tblname="group_rights";
	var $extname="CORE";
	var $__table="CORE_group_rights";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;

	var $rightref;
	var $groupref;
	var $rightId;
	var $extensionId;
	var $groupId;
	var $value;
	var $__DBMetaDataField=array('rightref'=>array('description'=>'Droit', 'type'=>10, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_rights')), 'groupref'=>array('description'=>'Groupe', 'type'=>10, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_groups')), 'rightId'=>array('description'=>'Droit', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99)), 'extensionId'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'groupId'=>array('description'=>'Groupe', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99)), 'value'=>array('description'=>'Valeur', 'type'=>3, 'notnull'=>true, 'params'=>array()));

}

?>
