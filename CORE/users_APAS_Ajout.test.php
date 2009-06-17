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
// --- Last modification: Date 17 June 2009 7:55:42 By  ---


//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@Ajout d'un utilisateur
//@PARAM@ 

function CORE_users_APAS_Ajout(&$test)
{
//@CODE_ACTION@
global $connect;
$connect->execute("DELETE FROM CORE_users WHERE id=101");
$connect->execute("DELETE FROM CORE_groups WHERE id=101");
$connect->execute("INSERT INTO CORE_groups (id,groupName,weigth) VALUES (101,'Truc Muche',38)",true);
$connect->execute("INSERT INTO CORE_users (id,login,pass,realName,groupId,actif) VALUES (101,'abc','','abc',1,'o')",true);
try {
	$rep=$test->CallAction("CORE","users_APAS_ajouter",array(),"Xfer_Container_Custom");
	$test->assertEquals(2,COUNT($rep->m_actions));
	$test->assertEquals(11,$rep->getComponentCount());

	$comp=$rep->getComponents(0);
	$test->assertClass("Xfer_Comp_Image",$comp);
	$test->assertEquals("img",$comp->m_name);

	$comp=$rep->getComponents(1);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("labellogin",$comp->m_name);
	$comp=$rep->getComponents(3);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("labelrealName",$comp->m_name);
	$comp=$rep->getComponents(5);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("labelgroupId",$comp->m_name);
	$comp=$rep->getComponents(7);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("lab1",$comp->m_name);
	$comp=$rep->getComponents(9);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("lab2",$comp->m_name);
	$comp=$rep->getComponents(2);
	$test->assertClass("Xfer_Comp_Edit",$comp);
	$test->assertEquals("login",$comp->m_name);
	$test->assertEquals("",$comp->m_value);
	$comp=$rep->getComponents(4);
	$test->assertClass("Xfer_Comp_Edit",$comp);
	$test->assertEquals("realName",$comp->m_name);
	$test->assertEquals("",$comp->m_value);
	$comp=$rep->getComponents(6);
	$test->assertClass("Xfer_Comp_Select",$comp);
	$test->assertEquals("groupId",$comp->m_name);
	$test->assertEquals(null,$comp->m_value);
	$test->assertEquals(3,count($comp->m_select));
	$test->assertEquals("Admin",$comp->m_select[1]);
	$test->assertEquals("Visiteur",$comp->m_select[99]);
	$test->assertEquals("Truc Muche",$comp->m_select[101]);
	$comp=$rep->getComponents(8);
	$test->assertClass("Xfer_Comp_Passwd",$comp);
	$test->assertEquals("newpass1",$comp->m_name);
	$test->assertEquals("",$comp->m_value);
	$comp=$rep->getComponents(10);
	$test->assertClass("Xfer_Comp_Passwd",$comp);
	$test->assertEquals("newpass2",$comp->m_name);
	$test->assertEquals("",$comp->m_value);

	$connect->execute("DELETE FROM CORE_groups WHERE id=101");
	$connect->execute("DELETE FROM CORE_users WHERE id=101");
} catch(Exception $e) {
	$connect->execute("DELETE FROM CORE_groups WHERE id=101");
	$connect->execute("DELETE FROM CORE_users WHERE id=101");
	throw $e;
}
//@CODE_ACTION@
}

?>
