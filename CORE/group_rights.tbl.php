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
// --- Last modification: Date 06 January 2010 23:54:49 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_group_rights extends DBObj_Basic
{
	var $Title="";
	var $tblname="group_rights";
	var $extname="CORE";
	var $__table="CORE_group_rights";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;
	var $Heritage="";
	var $PosChild=-1;

	var $rightref;
	var $groupref;
	var $value;
	var $__DBMetaDataField=array('rightref'=>array('description'=>'Droit', 'type'=>10, 'notnull'=>true, 'params'=>array('TableName'=>'CORE_extension_rights')), 'groupref'=>array('description'=>'Groupe', 'type'=>10, 'notnull'=>true, 'params'=>array('TableName'=>'CORE_groups')), 'value'=>array('description'=>'Valeur', 'type'=>3, 'notnull'=>true, 'params'=>array()));

}

?>
