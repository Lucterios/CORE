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

class DBObj_CORE_access extends DBObj_Basic
{
	var $tblname="access";
	var $extname="CORE";
	var $__table="CORE_access";

	var $DefaultFields=array(array('@refresh@'=>false, 'id'=>'1', 'inetAddr'=>'255.0.0.0/8'));
	var $NbFieldsCheck=1;

	var $inetAddr;
	var $__DBMetaDataField=array('inetAddr'=>array('description'=>'Addresse', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>18, 'Multi'=>false)));

}

?>
