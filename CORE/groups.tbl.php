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
// --- Last modification: Date 26 January 2009 21:40:21 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_groups extends DBObj_Basic
{
	var $Title="";
	var $tblname="groups";
	var $extname="CORE";
	var $__table="CORE_groups";

	var $DefaultFields=array(array('id'=>'1', 'groupId'=>'1', 'groupName'=>'Admin', 'weigth'=>'100'), array('@refresh@'=>false, 'id'=>'99', 'groupName'=>'Visiteur', 'weigth'=>'0', 'groupId'=>'0'));
	var $NbFieldsCheck=1;
	var $Heritage="";
	var $PosChild=-1;

	var $groupName;
	var $weigth;
	var $groupId;
	var $GroupRights;
	var $__DBMetaDataField=array('groupName'=>array('description'=>'Nom du groupe', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>30, 'Multi'=>false)), 'weigth'=>array('description'=>'Poids', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>100)), 'groupId'=>array('description'=>'groupId OBSELETE', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>0, 'Max'=>999)), 'GroupRights'=>array('description'=>'GroupRights', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_group_rights', 'RefField'=>'groupref')));

	var $__toText='$groupName';
}

?>
