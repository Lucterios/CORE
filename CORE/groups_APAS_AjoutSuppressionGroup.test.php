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
// --- Last modification: Date 17 June 2009 0:42:11 By  ---


//@TABLES@
require_once('CORE/groups.tbl.php');
//@TABLES@

//@DESC@Ajouter et supprimer un groupe
//@PARAM@ 

function CORE_groups_APAS_AjoutSuppressionGroup(&$test)
{
//@CODE_ACTION@
// ajout group
$rep=$test->CallAction("CORE","groups_APAS_ajouter",array("groupName"=>"Truc Muche","weigth"=>"38"),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$comp=$rep->getComponents(2);
$test->assertEquals(1,count($comp->m_records));
$key=array_keys($comp->m_records);
$test->assertEquals(100,$key[0],"Group 'Truc Muche'");
//$test->assertEquals("Truc Muche",$comp->m_records[$keys[0]]['groupName']);

$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
$comp=$rep->getComponents(2);
$test->assertEquals(4,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
$test->assertEquals("Visiteur",$comp->m_records[$keys[1]]['groupref']);
$test->assertEquals("Truc Muche",$comp->m_records[$keys[2]]['groupref']);
$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[3]]['groupref']);
$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
$test->assertEquals("Non",$comp->m_records[$keys[1]]['value'],"Visiteur");
$test->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"Truc Muche");
$test->assertEquals("Non",$comp->m_records[$keys[3]]['value'],"tous");

$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"101"),"Xfer_Container_Custom");
$comp=$rep->getComponents(2);
$test->assertEquals(4,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
$test->assertEquals("Visiteur",$comp->m_records[$keys[1]]['groupref']);
$test->assertEquals("Truc Muche",$comp->m_records[$keys[2]]['groupref']);
$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[3]]['groupref']);
$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
$test->assertEquals("Non",$comp->m_records[$keys[1]]['value'],"Visiteur");
$test->assertEquals("Non",$comp->m_records[$keys[2]]['value'],"Truc Muche");
$test->assertEquals("Non",$comp->m_records[$keys[3]]['value'],"tous");

$rep=$test->CallAction("CORE","groups_APAS_supprimer",array("group"=>100,"CONFIRME"=>"YES"),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
$comp=$rep->getComponents(2);
$test->assertEquals(3,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
$test->assertEquals("Visiteur",$comp->m_records[$keys[1]]['groupref']);
$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[2]]['groupref']);
$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
$test->assertEquals("Non",$comp->m_records[$keys[1]]['value'],"Visiteur");
$test->assertEquals("Non",$comp->m_records[$keys[2]]['value'],"tous");
//@CODE_ACTION@
}

?>
