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
// --- Last modification: Date 01 March 2008 15:15:16 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_menu extends DBObj_Basic
{
	var $tblname="menu";
	var $extname="CORE";
	var $__table="CORE_menu";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;

	var $menuItemId;
	var $extensionId;
	var $action;
	var $pere;
	var $description;
	var $help;
	var $icon;
	var $shortcut;
	var $position;
	var $modal;
	var $__DBMetaDataField=array('menuItemId'=>array('description'=>'Menu', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>40, 'Multi'=>false)), 'extensionId'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'action'=>array('description'=>'Action', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>100, 'Multi'=>false)), 'pere'=>array('description'=>'Pere', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>40, 'Multi'=>false)), 'description'=>array('description'=>'Description', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'help'=>array('description'=>'Aide', 'type'=>7, 'notnull'=>false, 'params'=>array()), 'icon'=>array('description'=>'Icon', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>75, 'Multi'=>false)), 'shortcut'=>array('description'=>'Racourcis clavier', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>30, 'Multi'=>false)), 'position'=>array('description'=>'Position', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>9999)), 'modal'=>array('description'=>'Modal', 'type'=>3, 'notnull'=>true, 'params'=>array()));

}

?>
