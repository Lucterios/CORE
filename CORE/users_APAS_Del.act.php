<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// Action file write by SDK tool
// --- Last modification: Date 15 November 2011 19:31:50 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:acknowledge
require_once('CORE/xfer.inc.php');
//@XFER:acknowledge@


//@DESC@Supprimer un utilisateur
//@PARAM@ user_actif=''
//@PARAM@ user_desactif=''

//@TRANSACTION:

//@LOCK:0

function users_APAS_Del($Params)
{
$user_actif=getParams($Params,"user_actif",'');
$user_desactif=getParams($Params,"user_desactif",'');
$self=new DBObj_CORE_users();

global $connect;
$connect->begin();
try {
$xfer_result=new Xfer_Container_Acknowledge("CORE","users_APAS_Del",$Params);
$xfer_result->Caption="Supprimer un utilisateur";
//@CODE_ACTION@
$user_list=explode(';',$user_actif.$user_desactif);
foreach($user_list as $user_id) {
	$DBUser=new DBObj_CORE_users;
	$DBUser->get((int)$user_id);
	if (($res=$DBUser->canBeDelete())!=0) {
		require_once("CORE/Lucterios_Error.inc.php");
		throw new LucteriosException(IMPORTANT,"Suppression de ".$DBUser->toText()." impossible");
	}
}
if ($xfer_result->confirme("Voulez vous supprimer ".count($user_list)." utilisateur(s)?")) {
	foreach($user_list as $user_id) {
		$DBUser=new DBObj_CORE_users;
		$DBUser->get((int)$user_id);
		$DBUser->deleteCascade();
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
