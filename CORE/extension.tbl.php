<?php
// 	This file is part of Lucterios/Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Lucterios/Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Lucterios/Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY// table file write by SDK tool
// --- Last modification: Date 26 October 2011 6:04:44 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_extension extends DBObj_Basic
{
	public $Title="";
	public $tblname="extension";
	public $extname="CORE";
	public $__table="CORE_extension";

	public $DefaultFields=array();
	public $NbFieldsCheck=1;
	public $Heritage="";
	public $PosChild=-1;

	public $extensionId;
	public $titre;
	public $description;
	public $versionMaj;
	public $versionMin;
	public $versionRev;
	public $versionBuild;
	public $validite;
	public $rights;
	public $action;
	public $__DBMetaDataField=array('extensionId'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'titre'=>array('description'=>'Titre', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'description'=>array('description'=>'Description', 'type'=>7, 'notnull'=>true, 'params'=>array()), 'versionMaj'=>array('description'=>'Version Majeur', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99)), 'versionMin'=>array('description'=>'Version Mineur', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999)), 'versionRev'=>array('description'=>'Version Revision', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999)), 'versionBuild'=>array('description'=>'Version Build', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999)), 'validite'=>array('description'=>'validite', 'type'=>3, 'notnull'=>true, 'params'=>array()), 'rights'=>array('description'=>'Droits', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_rights', 'RefField'=>'extension')), 'action'=>array('description'=>'Actions', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_actions', 'RefField'=>'extension')));

	public $__toText='$extensionId';
}

?>
