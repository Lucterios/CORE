<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// table file write by SDK tool
// --- Last modification: Date 20 October 2011 22:32:44 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_printmodel extends DBObj_Basic
{
	var $Title="";
	var $tblname="printmodel";
	var $extname="CORE";
	var $__table="CORE_printmodel";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;
	var $Heritage="";
	var $PosChild=-1;

	var $extensionid;
	var $identify;
	var $reference;
	var $titre;
	var $model;
	var $modify;
	var $extension;
	var $__DBMetaDataField=array('extensionid'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'identify'=>array('description'=>'Indentifiant', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'reference'=>array('description'=>'Reference', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>999999)), 'titre'=>array('description'=>'Titre', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'model'=>array('description'=>'Model', 'type'=>7, 'notnull'=>false, 'params'=>array()), 'modify'=>array('description'=>'Modifié', 'type'=>3, 'notnull'=>true, 'params'=>array()), 'extension'=>array('description'=>'Extension', 'type'=>12, 'notnull'=>false, 'params'=>array('MethodGet'=>'getExtension', 'MethodSet'=>'getExtension')));

}

?>
