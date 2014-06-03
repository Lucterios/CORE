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
// --- Last modification: Date 05 January 2008 17:40:26 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/finalreport.tbl.php');
//@TABLES@
//@XFER:print
require_once('CORE/xfer_printing.inc.php');
//@XFER:print@


//@DESC@reimprimer
//@PARAM@ 
//@INDEX:print_report


//@LOCK:0

function finalreport_APAS_reprint($Params)
{
$self=new DBObj_CORE_finalreport();
$print_report=getParams($Params,"print_report",-1);
if ($print_report>=0) $self->get($print_report);
try {
$xfer_result=new Xfer_Container_Print("CORE","finalreport_APAS_reprint",$Params);
$xfer_result->Caption='reimprimer';
//@CODE_ACTION@
$xfer_result->ReportTitle=$self->titre;
$xfer_result->ReportContent=$self->getXap();
$xfer_result->ReportType=1;
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
