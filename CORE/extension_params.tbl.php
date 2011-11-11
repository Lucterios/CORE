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

class DBObj_CORE_extension_params extends DBObj_Basic
{
	public $Title="";
	public $tblname="extension_params";
	public $extname="CORE";
	public $__table="CORE_extension_params";

	public $DefaultFields=array();
	public $NbFieldsCheck=1;
	public $Heritage="";
	public $PosChild=-1;

	public $extensionId;
	public $paramName;
	public $description;
	public $value;
	public $type;
	public $param;
	public $__DBMetaDataField=array('extensionId'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'paramName'=>array('description'=>'Nom', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>25, 'Multi'=>false)), 'description'=>array('description'=>'Description', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>200, 'Multi'=>false)), 'value'=>array('description'=>'Valeur', 'type'=>7, 'notnull'=>true, 'params'=>array()), 'type'=>array('description'=>'Type', 'type'=>8, 'notnull'=>true, 'params'=>array('Enum'=>array('Chaine', 'Entier', 'Réel', 'Booléen', 'Enumération'))), 'param'=>array('description'=>'Parametre de type', 'type'=>2, 'notnull'=>false, 'params'=>array('Size'=>80, 'Multi'=>false)));

	public $__toText='$paramName';
}

?>
