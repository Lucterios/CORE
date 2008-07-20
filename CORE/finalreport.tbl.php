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
// --- Last modification: Date 01 March 2008 15:14:13 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_finalreport extends DBObj_Basic
{
	var $tblname="finalreport";
	var $extname="CORE";
	var $__table="CORE_finalreport";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;

	var $extensionid;
	var $identify;
	var $reference;
	var $titre;
	var $report;
	var $date;
	var $heure;
	var $__DBMetaDataField=array('extensionid'=>array('description'=>'Extension', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'identify'=>array('description'=>'Identifiant', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'reference'=>array('description'=>'Reference', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>99999999)), 'titre'=>array('description'=>'Titre', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>50, 'Multi'=>false)), 'report'=>array('description'=>'Raport', 'type'=>7, 'notnull'=>false, 'params'=>array()), 'date'=>array('description'=>'Date', 'type'=>4, 'notnull'=>false, 'params'=>array()), 'heure'=>array('description'=>'Heure', 'type'=>5, 'notnull'=>false, 'params'=>array()));

}

?>
