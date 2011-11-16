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
// --- Last modification: Date 15 November 2011 19:24:23 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_users extends DBObj_Basic
{
	public $Title="";
	public $tblname="users";
	public $extname="CORE";
	public $__table="CORE_users";

	public $DefaultFields=array(array('login'=>'admin', 'pass'=>'*4ACFE3202A5FF5CF467898FC58AAB1D615029441', 'realName'=>'Administrateur', 'groupId'=>'1', 'actif'=>'o', '@refresh@'=>false, 'id'=>'100'), array('@refresh@'=>false, 'id'=>'99', 'login'=>'', 'pass'=>'d41d8cd98f00b204e9800998ecf8427e', 'realName'=>'Visiteur', 'groupId'=>'99', 'actif'=>'n'));
	public $NbFieldsCheck=1;
	public $Heritage="";
	public $PosChild=-1;

	public $login;
	public $pass;
	public $realName;
	public $groupId;
	public $actif;
	public $lastDate;
	public $__DBMetaDataField=array('login'=>array('description'=>'Alias', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>20, 'Multi'=>false)), 'pass'=>array('description'=>'Mot de passe', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>100, 'Multi'=>false)), 'realName'=>array('description'=>'Nom réel', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'groupId'=>array('description'=>'Groupe', 'type'=>10, 'notnull'=>true, 'params'=>array('TableName'=>'CORE_groups')), 'actif'=>array('description'=>'Actif', 'type'=>3, 'notnull'=>true, 'params'=>array()), 'lastDate'=>array('description'=>'Date de dernière connexion', 'type'=>11, 'notnull'=>false, 'params'=>array('Function'=>'CORE_FCT_users_APAS_getLastConnectDate', 'NbField'=>1)));

	public $__toText='$realName';
}

?>
