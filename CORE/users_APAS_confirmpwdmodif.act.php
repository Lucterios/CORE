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
// --- Last modification: Date 14 November 2008 18:59:14 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/sessions.tbl.php');
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:dialogbox
require_once('CORE/xfer_dialogBox.inc.php');
//@XFER:dialogbox@


//@DESC@Changer mot de passe
//@PARAM@ oldpass
//@PARAM@ newpass1
//@PARAM@ newpass2


//@LOCK:0

function users_APAS_confirmpwdmodif($Params)
{
if (($ret=checkParams("CORE", "users_APAS_confirmpwdmodif",$Params ,"oldpass","newpass1","newpass2"))!=null)
	return $ret;
$oldpass=getParams($Params,"oldpass",0);
$newpass1=getParams($Params,"newpass1",0);
$newpass2=getParams($Params,"newpass2",0);
$self=new DBObj_CORE_users();
try {
$xfer_result=&new Xfer_Container_DialogBox("CORE","users_APAS_confirmpwdmodif",$Params);
$xfer_result->Caption="Changer mot de passe";
//@CODE_ACTION@
$ses=new DBObj_CORE_sessions;
$login=$ses->CurrentLogin();

$self->query("SELECT * FROM CORE_users WHERE login='$login' AND pass=PASSWORD('$oldpass')");
if ($self->fetch())
{
  if (($newpass1!= "") && ($newpass1==$newpass2))
  {
    $self->ChangePWD($newpass1);
    $xfer_result->message("Mot de passe changé", 1);
  }
  else
  {
// erreur double newpass, reaffichage du formalaire de changement plus message d'erreur
    $xfer_result->message("Les mots de passe ne sont pas égaux!",4);
  }
}
else
{
// erreur de oldpass, reaffichage du formalaire de changement plus message d'erreur
  $xfer_result->message("Mot de passe actuel érroné.($login)", 4);
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
