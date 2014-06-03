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
// --- Last modification: Date 02 February 2008 12:06:52 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Changer de mot de passe
//@PARAM@ 


//@LOCK:0

function users_APAS_changerpassword($Params)
{
$self=new DBObj_CORE_users();
try {
$xfer_result=new Xfer_Container_Custom("CORE","users_APAS_changerpassword",$Params);
$xfer_result->Caption='Changer de mot de passe';
//@CODE_ACTION@
$img=new Xfer_Comp_Image('img');
$img->setValue('passwd.png');
$img->setLocation(0, 0, 1, 3);
$xfer_result->addComponent($img);

$lab=new Xfer_Comp_Label('lab1');
$lab->setValue('Ancien mot de passe');
$lab->setLocation(1, 0);
$xfer_result->addComponent($lab);
$comp1=new Xfer_Comp_Passwd('oldpass');
$comp1->setLocation(2, 0);
$xfer_result->addComponent($comp1);

$lab=new Xfer_Comp_Label('lab2');
$lab->setValue('Nouveau mot de passe');
$lab->setLocation(1, 1);
$xfer_result->addComponent($lab);
$comp1=new Xfer_Comp_Passwd('newpass1');
$comp1->setLocation(2, 1);
$xfer_result->addComponent($comp1);

$lab=new Xfer_Comp_Label('lab3');
$lab->setValue('Re-nouveau mot de passe');
$lab->setLocation(1, 2);
$xfer_result->addComponent($lab);
$comp1=new Xfer_Comp_Passwd('newpass2');
$comp1->setLocation(2, 2);
$xfer_result->addComponent($comp1);

$xfer_result->addAction($self->NewAction("_Ok",'ok.png','confirmpwdmodif'));
$xfer_result->addAction($self->NewAction("_Annuler",'cancel.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
