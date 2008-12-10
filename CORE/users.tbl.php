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
// --- Last modification: Date 09 December 2008 23:18:01 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_users extends DBObj_Basic
{
	var $Title="";
	var $tblname="users";
	var $extname="CORE";
	var $__table="CORE_users";

	var $DefaultFields=array(array('login'=>'admin', 'pass'=>'*4ACFE3202A5FF5CF467898FC58AAB1D615029441', 'realName'=>'Administrateur', 'groupId'=>'1', 'actif'=>'o', '@refresh@'=>false, 'id'=>'100'));
	var $NbFieldsCheck=1;
	var $Heritage="";
	var $PosChild=-1;

	var $login;
	var $pass;
	var $realName;
	var $groupId;
	var $actif;
	var $__DBMetaDataField=array('login'=>array('description'=>'Alias', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>20, 'Multi'=>false)), 'pass'=>array('description'=>'Mot de passe', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>100, 'Multi'=>false)), 'realName'=>array('description'=>'Nom réel', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'groupId'=>array('description'=>'Groupe', 'type'=>10, 'notnull'=>true, 'params'=>array('TableName'=>'CORE_groups')), 'actif'=>array('description'=>'Actif', 'type'=>3, 'notnull'=>true, 'params'=>array()));

	var $__toText='$realName';
}

?>
