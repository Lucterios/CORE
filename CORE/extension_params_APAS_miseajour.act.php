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


require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_params.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Mise à jour
//@INDEX:paramid

function extension_params_APAS_miseajour($Params)
{
$self=new DBObj_CORE_extension_params();
$paramid=getParams($Params,"paramid",-1);
if ($paramid>=0) $self->get($paramid);
$xfer_result=new Xfer_Container_Acknowledge("CORE","extension_params_APAS_miseajour",$Params);
//@CODE_ACTION@
$self->setFrom($Params);
$self->update();
//@CODE_ACTION@
return $xfer_result->getReponseXML();
}

?>
