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
// --- Last modification: Date 14 November 2008 18:48:38 By  ---


//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@Modifier un utilisateur
//@PARAM@ 

function CORE_users_APAS_Modif(&$test)
{
//@CODE_ACTION@
global $connect;
$connect->execute("DELETE FROM CORE_users WHERE id=101");
$connect->execute("DELETE FROM CORE_groups WHERE id=101");
$connect->execute("INSERT INTO CORE_groups (id,groupName,weigth) VALUES (101,'Truc Muche',38)",true);
$connect->execute("INSERT INTO CORE_users (id,login,pass,realName,groupId,actif) VALUES (101,'abc','','abc',1,'o')",true);
try {
	$rep=$test->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"cbd","realName"=>"efghij","groupId"=>1,"newpass1"=>"xyz","newpass2"=>"xyz"),"Xfer_Container_Acknowledge");
	$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(3);
	$test->assertEquals(2,count($comp->m_records));
	$comp=$rep->getComponents(3);
	$test->assertEquals("cbd",$comp->m_records["101"]["login"]);
	$test->assertEquals("efghij",$comp->m_records["101"]["realName"]);
	$test->assertEquals("Admin",$comp->m_records["101"]["groupId"]);

	$user=new DBObj_CORE_users;
	$user->get(101);
 	$test->assertEquals('*39C549BDECFBA8AFC3CE6B948C9359A0ECE08DE2',$user->pass,'pass 1');

	$rep=$test->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>101,"newpass1"=>"","newpass2"=>""),"Xfer_Container_Acknowledge");
	$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(3);
	$test->assertEquals(2,count($comp->m_records));
	$test->assertEquals("abc",$comp->m_records["101"]["login"]);
	$test->assertEquals("abc",$comp->m_records["101"]["realName"]);
	$test->assertEquals('Truc Muche',$comp->m_records["101"]["groupId"]);

	$rep=$test->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"admin","realName"=>"abc","groupId"=>1,"newpass1"=>"","newpass2"=>""));
	$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(3);
	$test->assertEquals(2,count($comp->m_records));
	$test->assertEquals("admin",$comp->m_records["100"]["login"]);
	$test->assertEquals("abc",$comp->m_records["101"]["login"]);

	$rep=$test->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"aaa","newpass2"=>"bbb"));
	$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(3);
	$test->assertEquals(2,count($comp->m_records));

	$user=new DBObj_CORE_users;
	$user->get(101);
 	$test->assertEquals('*39C549BDECFBA8AFC3CE6B948C9359A0ECE08DE2',$user->pass,'pass 2');

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
