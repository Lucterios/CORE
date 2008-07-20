<?php
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//


require_once('CORE/DBObject.inc.php');

class DBObj_CORE_sessions extends DBObj_Basic
{
	var $tblname="sessions";
	var $extname="CORE";
	var $__table="CORE_sessions";

	var $DefaultFields=array();
	var $NbFieldsCheck=1;

	var $sid;
	var $uid;
	var $dtcreate;
	var $dtmod;
	var $valid;
	var $ip;
	var $__DBMetaDataField=array('sid'=>array('description'=>'Session', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>30, 'Multi'=>false)), 'uid'=>array('description'=>'Login', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>20, 'Multi'=>false)), 'dtcreate'=>array('description'=>'Date/heure de connexion', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>0)), 'dtmod'=>array('description'=>'Date/heure de dernière modification', 'type'=>0, 'notnull'=>true, 'params'=>array('Min'=>0, 'Max'=>0)), 'valid'=>array('description'=>'Validé', 'type'=>3, 'notnull'=>true, 'params'=>array()), 'ip'=>array('description'=>'Adresse IP', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>16, 'Multi'=>false)));

}

?>
