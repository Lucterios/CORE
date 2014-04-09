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
//  // Action file write by SDK tool
// --- Last modification: Date 27 August 2007 19:08:24 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/etiquettes.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Validation
//@PARAM@ 
//@INDEX:etiquette

//@TRANSACTION:

//@LOCK:0

function etiquettes_APAS_ajouteract($Params)
{
$self=new DBObj_CORE_etiquettes();
$etiquette=getParams($Params,"etiquette",-1);
if ($etiquette>=0) $self->get($etiquette);

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Custom("CORE","etiquettes_APAS_ajouteract",$Params);
$xfer_result->Caption='Validation';
//@CODE_ACTION@
$self->setFrom($Params);
if ($etiquette>=0)
	$self->update();
else
	$self->insert();
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
