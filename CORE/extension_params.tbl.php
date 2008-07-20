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
// --- Last modification: Date 07 March 2008 12:10:50 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_extension_params extends DBObj_Basic
{
	var $tblname="extension_params";
	var $extname="CORE";
	var $__table="CORE_extension_params";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;

	var $extensionId;
	var $paramName;
	var $description;
	var $value;
	var $type;
	var $param;
	var $__DBMetaDataField=array('extensionId'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'paramName'=>array('description'=>'Nom', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>20, 'Multi'=>false)), 'description'=>array('description'=>'Description', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'value'=>array('description'=>'Valeur', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>100, 'Multi'=>false)), 'type'=>array('description'=>'Type', 'type'=>8, 'notnull'=>false, 'params'=>array('Enum'=>array('Chaine', 'Entier', 'Réel'))), 'param'=>array('description'=>'Parametre de type', 'type'=>2, 'notnull'=>false, 'params'=>array('Size'=>80, 'Multi'=>false)));

	var $__toText='$paramName';
}

?>
