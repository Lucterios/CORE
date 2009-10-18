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
// --- Last modification: Date 18 October 2009 15:15:16 By  ---


//@TABLES@
//@TABLES@

//@DESC@Controle des menus par defaut de CORE
//@PARAM@ 

function CORE_DefaultMenu(&$test)
{
//@CODE_ACTION@
$rep=$test->CallAction("CORE","menu",array(),"Xfer_Container_Menu");
$test->assertEquals(1,count($rep->m_main_menus->m_sub_menus));

// Administration
$menu_admin=$rep->m_main_menus->m_sub_menus[0];
$test->assertEquals("Ad_ministration",$menu_admin->m_id);
$test->assertEquals("Ad_ministration",$menu_admin->m_title);
$test->assertEquals("CORE",$menu_admin->m_extension);
$test->assertEquals("",$menu_admin->m_action);
$test->assertEquals(6,count($menu_admin->m_sub_menus));

// Mot de passe
$menu_item=$menu_admin->m_sub_menus[0];
$test->assertEquals("_Motdepasse",$menu_item->m_id);
$test->assertEquals("_Mot de passe",$menu_item->m_title);
$test->assertEquals("CORE",$menu_item->m_extension);
$test->assertEquals("users_APAS_changerpassword",$menu_item->m_action);
$test->assertEquals(0,count($menu_item->m_sub_menus));

// Configuration generale
$menu_item=$menu_admin->m_sub_menus[1];
$test->assertEquals("Configuration_generale",$menu_item->m_id);
$test->assertEquals("Configuration _générale",$menu_item->m_title);
$test->assertEquals("CORE",$menu_item->m_extension);
$test->assertEquals("configuration",$menu_item->m_action);
$test->assertEquals(0,count($menu_item->m_sub_menus));

// Archivage
$menu_item=$menu_admin->m_sub_menus[2];
$test->assertEquals("Ar_chivage",$menu_item->m_id);
$test->assertEquals("Ar_chivage",$menu_item->m_title);
$test->assertEquals("CORE",$menu_item->m_extension);
$test->assertEquals("",$menu_item->m_action);
$test->assertEquals(3,count($menu_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[0];
$test->assertEquals("_Sauvegarder",$menu_sub_item->m_id);
$test->assertEquals("_Sauvegarder",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("selectNewArchive",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[1];
$test->assertEquals("_Restauration",$menu_sub_item->m_id);
$test->assertEquals("_Restauration",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("selectRestor",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[2];
$test->assertEquals("_Gestiondesarchives",$menu_sub_item->m_id);
$test->assertEquals("_Gestion des archives",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("toolBackup",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

//Impression
$menu_item=$menu_admin->m_sub_menus[3];
$test->assertEquals("_RapportetImpression",$menu_item->m_id);
$test->assertEquals("_Rapport et Impression",$menu_item->m_title);
$test->assertEquals("CORE",$menu_item->m_extension);
$test->assertEquals("",$menu_item->m_action);
$test->assertEquals(3,count($menu_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[0];
$test->assertEquals("_Etiquettes",$menu_sub_item->m_id);
$test->assertEquals("_Etiquettes",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("etiquettes_APAS_liste",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[1];
$test->assertEquals("Ra_pportssauvegardes",$menu_sub_item->m_id);
$test->assertEquals("Ra_pports sauvegardés",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("finalreport_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[2];
$test->assertEquals("_Modelesdesrapports",$menu_sub_item->m_id);
$test->assertEquals("_Modèles des rapports",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("printmodel_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

//Gestion des droits
$menu_item=$menu_admin->m_sub_menus[4];
$test->assertEquals("_GestiondesDroits",$menu_item->m_id);
$test->assertEquals("_Gestion des Droits",$menu_item->m_title);
$test->assertEquals("CORE",$menu_item->m_extension);
$test->assertEquals("",$menu_item->m_action);
$test->assertEquals(3,count($menu_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[0];
$test->assertEquals("_Utilisateurs",$menu_sub_item->m_id);
$test->assertEquals("_Utilisateurs",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("users_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[1];
$test->assertEquals("_Groupes",$menu_sub_item->m_id);
$test->assertEquals("_Groupes",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("groups_APAS_liste",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[2];
$test->assertEquals("_Extensions",$menu_sub_item->m_id);
$test->assertEquals("_Extensions",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("extension_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

//Avance
$menu_item=$menu_admin->m_sub_menus[5];
$test->assertEquals("_Avance",$menu_item->m_id);
$test->assertEquals("_Avancé",$menu_item->m_title);
$test->assertEquals("CORE",$menu_item->m_extension);
$test->assertEquals("",$menu_item->m_action);
$test->assertEquals(2,count($menu_item->m_sub_menus));

/*$menu_sub_item=$menu_item->m_sub_menus[0];
$test->assertEquals("_Parametres",$menu_sub_item->m_id);
$test->assertEquals("_Paramètres",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("extension_params_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));*/

$menu_sub_item=$menu_item->m_sub_menus[0];
$test->assertEquals("Autorisationd`acces_reseau",$menu_sub_item->m_id);
$test->assertEquals("Autorisation d`accès _réseau",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("access_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));

$menu_sub_item=$menu_item->m_sub_menus[1];
$test->assertEquals("_Session",$menu_sub_item->m_id);
$test->assertEquals("_Session",$menu_sub_item->m_title);
$test->assertEquals("CORE",$menu_sub_item->m_extension);
$test->assertEquals("sessions_APAS_list",$menu_sub_item->m_action);
$test->assertEquals(0,count($menu_sub_item->m_sub_menus));
//@CODE_ACTION@
}

?>
