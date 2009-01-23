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
//  // Method file write by SDK tool
// --- Last modification: Date 08 January 2009 22:07:52 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ xfer_result

function users_APAS_Formulaire(&$self,$xfer_result)
{
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('user.png');
$img->setLocation(0,0,1,6);
$xfer_result->addComponent($img);

if (isset($xfer_result->WithActif))
  $xfer_result->setDBObject($self,array("login","actif","groupId"),($self->id==99) || ($self->id==100),1,1);
else
  $xfer_result->setDBObject($self,array("login","realName","groupId"),($self->id==99) || ($self->id==100),1,1);

if ($self->id!=99) {
	$lab= &new Xfer_Comp_LabelForm('lab1');
	$lab->setValue('{[bold]}mot de passe{[/bold]}');
	$lab->setLocation(1, 4, 1, 1);
	$xfer_result->addComponent($lab);
	$comp1= &new Xfer_Comp_Passwd('newpass1');
	$comp1->setLocation(2, 4, 1, 1);
	$xfer_result->addComponent($comp1);

	$lab= &new Xfer_Comp_LabelForm('lab2');
	$lab->setValue('{[bold]}re-mot de passe{[/bold]}');
	$lab->setLocation(1, 5, 1, 1);
	$xfer_result->addComponent($lab);
	$comp1= &new Xfer_Comp_Passwd('newpass2');
	$comp1->setLocation(2, 5, 1, 1);
	$xfer_result->addComponent($comp1);
}
return $xfer_result;
//@CODE_ACTION@
}

?>
