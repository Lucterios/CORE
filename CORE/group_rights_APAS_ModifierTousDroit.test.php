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
// --- Last modification: Date 10 November 2008 12:30:26 By  ---


//@TABLES@
require_once('CORE/group_rights.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ 

function CORE_group_rights_APAS_ModifierTousDroit(&$test)
{
//@CODE_ACTION@
$rep=$test->CallAction("CORE","group_rights_APAS_modify",array("right"=>"11","groupright"=>"0"),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"11"),"Xfer_Container_Custom");
$comp=$rep->getComponents(2);
$test->assertEquals(2,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
$test->assertEquals("Non",$comp->m_records[$keys[1]]['value'],"tous");

$rep=$test->CallAction("CORE","group_rights_APAS_modify",array("right"=>"11","groupright"=>"11"),"Xfer_Container_Acknowledge");
$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"11"),"Xfer_Container_Custom");
$comp=$rep->getComponents(2);
$test->assertEquals(2,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Non",$comp->m_records[$keys[0]]['value'],"Admin");
$test->assertEquals("Non",$comp->m_records[$keys[1]]['value'],"tous");
//@CODE_ACTION@
}

?>
