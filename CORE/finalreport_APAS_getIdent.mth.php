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
//  // Method file write by SDK tool
// --- Last modification: Date 05 January 2008 18:16:07 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/finalreport.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ 

function finalreport_APAS_getIdent(&$self)
{
//@CODE_ACTION@

$ident=$self->identify.':'.$self->reference;
if (($pos=strpos($self->identify,'_APAS_'))>0) {
	$table=substr($self->identify,0,$pos);
	$file='extensions/'.$self->extensionid.'/'.$table.'.tbl.php';
	if (is_file($file)){
		$table_name='DBObj_'.$self->extensionid.'_'.$table;
		require_once $file;
		$DBTbl=new $table_name;
	  	$DBTbl->get($self->reference);
		$ident=$DBTbl->toText();
	}
}
return $ident;
//@CODE_ACTION@
}

?>
