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

class DBObj_CORE_finalreport extends DBObj_Basic
{
	public $Title="";
	public $tblname="finalreport";
	public $extname="CORE";
	public $__table="CORE_finalreport";

	public $DefaultFields=array();
	public $NbFieldsCheck=1;
	public $Heritage="";
	public $PosChild=-1;

	public $extensionid;
	public $identify;
	public $reference;
	public $titre;
	public $report;
	public $date;
	public $heure;
	public $nature;
	public $__DBMetaDataField=array('extensionid'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'identify'=>array('description'=>'Identifiant', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'reference'=>array('description'=>'Reference', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99999999)), 'titre'=>array('description'=>'Titre', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'report'=>array('description'=>'Raport', 'type'=>7, 'notnull'=>false, 'params'=>array()), 'date'=>array('description'=>'Date', 'type'=>4, 'notnull'=>false, 'params'=>array()), 'heure'=>array('description'=>'Heure', 'type'=>5, 'notnull'=>false, 'params'=>array()), 'nature'=>array('description'=>'Nature', 'type'=>12, 'notnull'=>false, 'params'=>array('MethodGet'=>'getIdent', 'MethodSet'=>'getIdent')));

}

?>
