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
// --- Last modification: Date 20 August 2007 17:18:54 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/sessions.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Tuer une session
//@PARAM@ 
//@INDEX:access_actuel

function sessions_APAS_killsession($Params)
{
$self=new DBObj_CORE_sessions();
$access_actuel=getParams($Params,"access_actuel",-1);
if ($access_actuel>=0) $self->get($access_actuel);
$xfer_result=&new Xfer_Container_Acknowledge("CORE","sessions_APAS_killsession",$Params);
//@CODE_ACTION@

if ($xfer_result->confirme("Etes-vous sure de vouloir tuer cette session?"))
{
	$self->valid='n';
	$self->update();
}
//@CODE_ACTION@
return $xfer_result->getReponseXML();
}

?>
