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
//  // Test file write by SDK tool
// --- Last modification: Date 18 November 2009 9:01:38 By  ---


//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@Liste des utilisateurs
//@PARAM@ 

function CORE_users_APAS_List(&$test)
{
//@CODE_ACTION@
// Initial
global $connect;
$connect->execute("DELETE FROM CORE_users WHERE id=101");
$connect->execute("INSERT INTO CORE_users (id,login,pass,realName,groupId,actif) VALUES (101,'abc','','abc',1,'o')",true);

//tests
$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions));
$test->assertEquals(7,$rep->getComponentCount());

$comp=$rep->getComponents(0);
$test->assertClass("Xfer_Comp_Image",$comp);
$test->assertEquals("img",$comp->m_name);

$comp=$rep->getComponents(1);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("title",$comp->m_name);

$comp=$rep->getComponents(2);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("lbl_actifs",$comp->m_name);

$comp=$rep->getComponents(3);
$test->assertClass("Xfer_Comp_Grid",$comp);
$test->assertEquals("user_actif",$comp->m_name);
$test->assertEquals(3,count($comp->m_headers));
$test->assertEquals(3,count($comp->m_actions));
$headers=array_keys($comp->m_headers);
$test->assertEquals("login",$headers[0]);
$test->assertEquals("realName",$headers[1]);
$test->assertEquals("groupId",$headers[2]);
$test->assertEquals(2,count($comp->m_records));
$test->assertEquals("admin",$comp->m_records["100"]["login"]);
$test->assertEquals("Administrateur",$comp->m_records["100"]["realName"]);
$test->assertEquals("Admin",$comp->m_records["100"]["groupId"]);
$test->assertEquals("abc",$comp->m_records["101"]["login"]);
$test->assertEquals("abc",$comp->m_records["101"]["realName"]);
$test->assertEquals("Admin",$comp->m_records["101"]["groupId"]);

$comp=$rep->getComponents(4);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("separator",$comp->m_name);

$comp=$rep->getComponents(5);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("lbl_inactif",$comp->m_name);

$comp=$rep->getComponents(6);
$test->assertClass("Xfer_Comp_Grid",$comp);
$test->assertEquals("user_desactif",$comp->m_name);
$test->assertEquals(3,count($comp->m_headers));
$test->assertEquals(1,count($comp->m_actions));
$headers=array_keys($comp->m_headers);
$test->assertEquals("login",$headers[0]);
$test->assertEquals("realName",$headers[1]);
$test->assertEquals("groupId",$headers[2]);
$test->assertEquals(1,count($comp->m_records));
$test->assertEquals("",$comp->m_records["99"]["login"]);
$test->assertEquals("Visiteur",$comp->m_records["99"]["realName"]);
$test->assertEquals("Visiteur",$comp->m_records["99"]["groupId"]);
//@CODE_ACTION@
}

?>
