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
// --- Last modification: Date 17 June 2009 1:01:42 By  ---


//@TABLES@
require_once('CORE/users.tbl.php');
//@TABLES@

//@DESC@Activation et désactivation d'utilisateur
//@PARAM@ 

function CORE_users_APAS_ModifActivation(&$test)
{
//@CODE_ACTION@
global $connect;
$connect->execute("DELETE FROM CORE_users WHERE id=101");
$connect->execute("INSERT INTO CORE_users (id,login,pass,realName,groupId,actif) VALUES (101,'abc','','abc',1,'o')",true);
try {
	$rep=$test->CallAction("CORE","users_APAS_desactiver",array("user_actif"=>"101"),"Xfer_Container_Acknowledge");

	$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(3);
	$test->assertEquals(1,count($comp->m_records));
	$test->assertEquals("admin",$comp->m_records["100"]["login"]);

	$comp=$rep->getComponents(6);
	$test->assertEquals(2,count($comp->m_records));
	$test->assertEquals("abc",$comp->m_records["101"]["login"]);
	$test->assertEquals("",$comp->m_records["99"]["login"]);

	$rep=$test->CallAction("CORE","users_APAS_reactiver",array("user_desactif"=>"101"),"Xfer_Container_Acknowledge");

	$rep=$test->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(3);
	$test->assertEquals(2,count($comp->m_records));
	$test->assertEquals("admin",$comp->m_records["100"]["login"]);
	$test->assertEquals("abc",$comp->m_records["101"]["login"]);

	$comp=$rep->getComponents(6);
	$test->assertEquals(1,count($comp->m_records));
	$test->assertEquals("",$comp->m_records["99"]["login"]);

	$connect->execute("DELETE FROM CORE_users WHERE id=101");
} catch(Exception $e) {
	$connect->execute("DELETE FROM CORE_users WHERE id=101");
	throw $e;
}
//@CODE_ACTION@
}

?>
