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
// --- Last modification: Date 08 August 2008 22:46:45 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_extension_rights extends DBObj_Basic
{
	var $Title="";
	var $tblname="extension_rights";
	var $extname="CORE";
	var $__table="CORE_extension_rights";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;
	var $Heritage="";
	var $PosChild=-1;

	var $extension;
	var $description;
	var $rightId;
	var $weigth;
	var $actions;
	var $groupright;
	var $__DBMetaDataField=array('extension'=>array('description'=>'Extension', 'type'=>10, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension')), 'description'=>array('description'=>'Description', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'rightId'=>array('description'=>'Droit', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99)), 'weigth'=>array('description'=>'poids', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>100)), 'actions'=>array('description'=>'Actions', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_actions', 'RefField'=>'rights')), 'groupright'=>array('description'=>'Group/Right', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_group_rights', 'RefField'=>'rightref')));

}

?>
