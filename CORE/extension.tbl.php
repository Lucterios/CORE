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
// --- Last modification: Date 01 March 2008 15:09:49 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_extension extends DBObj_Basic
{
	var $tblname="extension";
	var $extname="CORE";
	var $__table="CORE_extension";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;

	var $extensionId;
	var $titre;
	var $description;
	var $versionMaj;
	var $versionMin;
	var $versionRev;
	var $versionBuild;
	var $validite;
	var $rights;
	var $action;
	var $__DBMetaDataField=array('extensionId'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'titre'=>array('description'=>'Titre', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'description'=>array('description'=>'Description', 'type'=>7, 'notnull'=>true, 'params'=>array()), 'versionMaj'=>array('description'=>'Version Majeur', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99)), 'versionMin'=>array('description'=>'Version Mineur', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999)), 'versionRev'=>array('description'=>'Version Revision', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999)), 'versionBuild'=>array('description'=>'Version Build', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999)), 'validite'=>array('description'=>'validite', 'type'=>3, 'notnull'=>true, 'params'=>array()), 'rights'=>array('description'=>'Droits', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_rights', 'RefField'=>'extension')), 'action'=>array('description'=>'Actions', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_actions', 'RefField'=>'extension')));

	var $__toText='$extensionId';
}

?>
