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
// --- Last modification: Date 18 November 2008 18:23:58 By  ---


//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@

//@DESC@List des extensions
//@PARAM@ 

function CORE_extension_APAS_List(&$test)
{
//@CODE_ACTION@
$rep=$test->CallAction("CORE","extension_APAS_list",array(),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions));
$test->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
$test->assertEquals(3,$rep->getComponentCount());

$comp=$rep->getComponents(0);
$test->assertClass("Xfer_Comp_Image",$comp);
$test->assertEquals("img",$comp->m_name);

$comp=$rep->getComponents(1);
$test->assertClass("Xfer_Comp_LabelForm",$comp);
$test->assertEquals("title",$comp->m_name);

$comp=$rep->getComponents(2);
$test->assertClass("Xfer_Comp_Grid",$comp);
$test->assertEquals("extension",$comp->m_name);
$test->assertEquals(4,count($comp->m_headers),"headers");
$test->assertEquals(2,count($comp->m_actions),"actions");
$test->assertEquals(1,count($comp->m_records),"records");
$test->assertEquals(new Xfer_Action("_Droits et Actions",'edit.png', "CORE", "extension_APAS_listactions",FORMTYPE_MODAL, CLOSE_NO, SELECT_SINGLE),$comp->m_actions[0]);
$test->assertEquals(new Xfer_Action('_Supprimer','suppr.png', "CORE", "extension_APAS_Delete",FORMTYPE_MODAL, CLOSE_NO, SELECT_SINGLE),$comp->m_actions[1]);
$headers=array_keys($comp->m_headers);
$test->assertEquals("titre",$headers[0]);
$test->assertEquals('Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild',$headers[1]);
$test->assertEquals("description",$headers[2]);
$test->assertEquals("validite",$headers[3]);

$test->assertEquals("Noyau Lucterios",$comp->m_records["100"]["titre"]);
$test->assertEquals("0.16",substr($comp->m_records["100"]['Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild'],0,4));
$test->assertEquals("Coeur du serveur Lucterios.{[newline]}Gère la connexion au logiciel, les droits d'accès ainsi que l'integration des autres modules.",$comp->m_records["100"]["description"]);
$test->assertEquals("Oui",$comp->m_records["100"]["validite"]);
//@CODE_ACTION@
}

?>
