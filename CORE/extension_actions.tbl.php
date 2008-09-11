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
// --- Last modification: Date 10 September 2008 20:05:45 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_extension_actions extends DBObj_Basic
{
	var $Title="";
	var $tblname="extension_actions";
	var $extname="CORE";
	var $__table="CORE_extension_actions";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;
	var $Heritage="";
	var $PosChild=-1;

	var $action;
	var $description;
	var $extension;
	var $rights;
	var $extensionId;
	var $rightId;
	var $__DBMetaDataField=array('action'=>array('description'=>'Action', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>100, 'Multi'=>false)), 'description'=>array('description'=>'Description', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'extension'=>array('description'=>'Extension', 'type'=>10, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension')), 'rights'=>array('description'=>'Droit', 'type'=>10, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_rights')), 'extensionId'=>array('description'=>'extensionId OBSELETE', 'type'=>2, 'notnull'=>false, 'params'=>array('Size'=>50, 'Multi'=>false)), 'rightId'=>array('description'=>'rightId OBSELETE', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>0, 'Max'=>99)));

	var $__toText='$description';
}

?>
