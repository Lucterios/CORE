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
// --- Last modification: Date 10 November 2008 12:16:15 By  ---


//@TABLES@
require_once('CORE/extension_rights.tbl.php');
//@TABLES@

//@DESC@Editer un droit
//@PARAM@ 

function CORE_extension_rights_APAS_EditerDroit(&$test)
{
//@CODE_ACTION@
$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"6"),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions));
$test->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
$test->assertEquals(3,$rep->getComponentCount());

$comp=$rep->getComponents(0);
$test->assertClass("Xfer_Comp_Image",$comp);
$test->assertEquals("img",$comp->m_name);

$comp=$rep->getComponents(1);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("Comp2",$comp->m_name);
$test->assertEquals("{[center]}{[underline]}{[bold]}Droits {[italc]}Changer de mot de passe{[/italc]} de l'extension {[italc]}CORE{[/italc]}{[/bold]}{[/underline]}{[/center]}",$comp->m_value);

$comp=$rep->getComponents(2);
$test->assertClass("Xfer_Comp_Grid",$comp);
$test->assertEquals("groupright",$comp->m_name);
$test->assertEquals(1,count($comp->m_actions));
$test->assertEquals(new Xfer_Action("_Changer le droit", "lister.gif", "CORE", "group_rights_APAS_modify",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE),$comp->m_actions[0]);
$test->assertEquals(2,count($comp->m_headers));
$headers=array_keys($comp->m_headers);
$test->assertEquals("groupref",$headers[0]);
$test->assertEquals("value",$headers[1]);
$test->assertEquals(2,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[1]]['groupref']);
$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
$test->assertEquals("Non",$comp->m_records[$keys[1]]['value'],"tous");
//@CODE_ACTION@
}

?>
