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
// --- Last modification: Date 05 January 2010 0:14:15 By  ---


//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@

//@DESC@Droit et action
//@PARAM@ 

function CORE_extension_APAS_DroitAction(&$test)
{
//@CODE_ACTION@
$rep=$test->CallAction("CORE","extension_APAS_listactions",array("extension"=>'100'),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions));
$test->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
$test->assertEquals(3,$rep->getComponentCount());

$comp=$rep->getComponents(0);
$test->assertClass("Xfer_Comp_Image",$comp);
$test->assertEquals("img",$comp->m_name);

$comp=$rep->getComponents(1);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("Comp2",$comp->m_name);

$comp=$rep->getComponents(2);
$test->assertClass("Xfer_Comp_Grid",$comp);
$test->assertEquals("right",$comp->m_name);
$test->assertEquals(1,count($comp->m_actions));
$test->assertEquals(2,count($comp->m_headers));
$headers=array_keys($comp->m_headers);
$test->assertEquals("description",$headers[0]);
$test->assertEquals("actions",$headers[1]);

$test->assertEquals(13,count($comp->m_records));
$keys=array_keys($comp->m_records);
$test->assertEquals("Acceder à l`application",$comp->m_records[$keys[0]]["description"]);
$test->assertEquals("Status de l`application",$comp->m_records[$keys[1]]["description"]);
$test->assertEquals("Changer de mot de passe",$comp->m_records[$keys[2]]["description"]);
$test->assertEquals("Consulter les paramètres généreaux",$comp->m_records[$keys[3]]["description"]);
$test->assertEquals("Impression",$comp->m_records[$keys[4]]["description"]);
$test->assertEquals("Activer/Desactiver une extension",$comp->m_records[$keys[5]]["description"]);
$test->assertEquals("Ajouter/modifier un utilisateur",$comp->m_records[$keys[6]]["description"]);
$test->assertEquals("Consultation de session de connexion",$comp->m_records[$keys[7]]["description"]);
$test->assertEquals("Archivage",$comp->m_records[$keys[8]]["description"]);
$test->assertEquals("Gestion des autorisation d`acces réseau",$comp->m_records[$keys[9]]["description"]);
$test->assertEquals("Modifier les paramètres généraux",$comp->m_records[$keys[10]]["description"]);
$test->assertEquals("Ajouter/Modifier un groupe",$comp->m_records[$keys[11]]["description"]);
$test->assertEquals("Paramètres généraux (avancé)",$comp->m_records[$keys[12]]["description"]);

$test->assertEquals(new Xfer_Action("Editer les droits", "lister.gif", "CORE", "extension_rights_APAS_editer","1","0","0"),$comp->m_actions[0]);
//@CODE_ACTION@
}

?>
