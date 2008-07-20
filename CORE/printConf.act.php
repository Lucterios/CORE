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
// --- Last modification: Date 07 November 2007 22:49:41 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:print
require_once('CORE/xfer_printing.inc.php');
//@XFER:print@


//@DESC@Impression de la configuration
//@PARAM@ 


//@LOCK:0

function printConf($Params)
{
try {
$xfer_result=&new Xfer_Container_Print("CORE","printConf",$Params);
$xfer_result->Caption='Impression de la configuration';
//@CODE_ACTION@

require_once 'CORE/PrintAction.inc.php';

$Params['NOPLAN']=1;

$print_action=new PrintAction('CORE','configuration',$Params);
$print_action->TabChangePage=false;
$print_action->Extended=false;
$print_action->Title="Configuration";
$xfer_result->printListing($print_action);
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
