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

class DBObj_CORE_extension_rights extends DBObj_Basic
{
	public $Title="";
	public $tblname="extension_rights";
	public $extname="CORE";
	public $__table="CORE_extension_rights";

	public $DefaultFields=array();
	public $NbFieldsCheck=1;
	public $Heritage="";
	public $PosChild=-1;

	public $extension;
	public $description;
	public $rightId;
	public $weigth;
	public $actions;
	public $groupright;
	public $__DBMetaDataField=array('extension'=>array('description'=>'Extension', 'type'=>10, 'notnull'=>true, 'params'=>array('TableName'=>'CORE_extension')), 'description'=>array('description'=>'Description', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'rightId'=>array('description'=>'Droit', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99)), 'weigth'=>array('description'=>'poids', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>100)), 'actions'=>array('description'=>'Actions', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_extension_actions', 'RefField'=>'rights')), 'groupright'=>array('description'=>'Group/Right', 'type'=>9, 'notnull'=>false, 'params'=>array('TableName'=>'CORE_group_rights', 'RefField'=>'rightref')));

}

?>
