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
// --- Last modification: Date 03 September 2007 18:30:38 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/group_rights.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Modifier un droit
//@PARAM@ right
//@PARAM@ groupright

//@TRANSACTION:

//@LOCK:0

function group_rights_APAS_modify($Params)
{
if (($ret=checkParams("CORE", "group_rights_APAS_modify",$Params ,"right","groupright"))!=null)
	return $ret;
$right=getParams($Params,"right",0);
$groupright=getParams($Params,"groupright",0);
$self=new DBObj_CORE_group_rights();

global $connect;
$connect->begin();
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","group_rights_APAS_modify",$Params);
$xfer_result->Caption='Modifier un droit';
//@CODE_ACTION@
if ($groupright!=0)
{
   $self->get($groupright);
   $self->value=($self->value=='o'?'n':'o');
   $self->update();
}
else
{
  $self=new DBObj_CORE_group_rights;
  $self->rightref=$right;
  $self->value='n';
  $self->find();
  while ( $self->fetch())
  {
    $self->value='o';
    $self->update();
  }
}
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
