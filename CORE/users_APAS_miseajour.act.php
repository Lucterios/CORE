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
// --- Last modification: Date 14 November 2008 18:23:19 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@modifier un utilisateur
//@PARAM@ newpass1
//@PARAM@ newpass2
//@INDEX:user_actif

//@TRANSACTION:

//@LOCK:0

function users_APAS_miseajour($Params)
{
if (($ret=checkParams("CORE", "users_APAS_miseajour",$Params ,"newpass1","newpass2"))!=null)
	return $ret;
$newpass1=getParams($Params,"newpass1",0);
$newpass2=getParams($Params,"newpass2",0);
$self=new DBObj_CORE_users();
$user_actif=getParams($Params,"user_actif",-1);
if ($user_actif>=0) $self->get($user_actif);

global $connect;
$connect->begin();
try {
$xfer_result=&new Xfer_Container_Acknowledge("CORE","users_APAS_miseajour",$Params);
$xfer_result->Caption="modifier un utilisateur";
//@CODE_ACTION@
if ((($newpass1!= "") || ($self->id>0)) && ($newpass1==$newpass2)) {
	if ($self->ModifierUser($Params)) {
  		$self->actif='o';
		if ($user_actif>0)
    			$self->update();
  		else
    			$self->insert();
		if ($newpass1!= "")
  			$self->ChangePWD($newpass1);
	}
  	else
		$xfer_result->message("Ce login exists déjà!",4);
}
else
   $xfer_result->message("Les mots de passe ne sont pas égaux!",4);
//@CODE_ACTION@
	$connect->commit();
}catch(Exception $e) {
	$connect->rollback();
	throw $e;
}
return $xfer_result;
}

?>
