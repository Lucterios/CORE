<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// Test file write by SDK tool
// --- Last modification: Date 15 November 2011 20:14:02 By  ---


//@TABLES@
require_once('CORE/groups.tbl.php');
//@TABLES@

//@DESC@Ajouter et cloner un groupe
//@PARAM@ 

function CORE_groups_APAS_ListAjoutClone(&$test)
{
//@CODE_ACTION@
$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
$act=$rep->m_actions[0];
$test->assertEquals("_Fermer",$act->m_title,'Titre action #1');
$test->assertEquals("",$act->m_extension,'Ext action #1');
$test->assertEquals("",$act->m_action,'Act action #1');
$test->assertEquals(4,$rep->getComponentCount(),'nb component');
//IMAGE - img
$comp=$rep->getComponents('img');
$test->assertClass("Xfer_Comp_Image",$comp,"Classe de img");
$test->assertEquals("images/group.png","".$comp->m_value,"Valeur de img");
//LABELFORM - title
$comp=$rep->getComponents('title');
$test->assertClass("Xfer_Comp_LabelForm",$comp,"Classe de title");
$test->assertEquals("{[center]}{[underline]}{[bold]}Groupes Existants{[/bold]}{[/underline]}{[/center]}","".$comp->m_value,"Valeur de title");
//GRID - group
$comp=$rep->getComponents('group');
$test->assertEquals(5,count($comp->m_actions),"Nb grid actions de group");
$test->assertEquals(2,count($comp->m_headers),"Nb grid headers de group");
$test->assertEquals(0,count($comp->m_records),"Nb grid records de group");
$act=$comp->m_actions[0];
$test->assertEquals("_Editer",$act->m_title,'Titre grid action #1');
$test->assertEquals("CORE",$act->m_extension,'Ext grid action #1');
$test->assertEquals("groups_APAS_editer",$act->m_action,'Act grid action #1');
$act=$comp->m_actions[1];
$test->assertEquals("_Modifier",$act->m_title,'Titre grid action #2');
$test->assertEquals("CORE",$act->m_extension,'Ext grid action #2');
$test->assertEquals("groups_APAS_modifier",$act->m_action,'Act grid action #2');
$act=$comp->m_actions[2];
$test->assertEquals("_Supprimer",$act->m_title,'Titre grid action #3');
$test->assertEquals("CORE",$act->m_extension,'Ext grid action #3');
$test->assertEquals("groups_APAS_supprimer",$act->m_action,'Act grid action #3');
$act=$comp->m_actions[3];
$test->assertEquals("_Cloner",$act->m_title,'Titre grid action #4');
$test->assertEquals("CORE",$act->m_extension,'Ext grid action #4');
$test->assertEquals("groups_APAS_cloner",$act->m_action,'Act grid action #4');
$act=$comp->m_actions[4];
$test->assertEquals("_Ajouter",$act->m_title,'Titre grid action #5');
$test->assertEquals("CORE",$act->m_extension,'Ext grid action #5');
$test->assertEquals("groups_APAS_modifier",$act->m_action,'Act grid action #5');
$headers=$comp->m_headers;
$test->assertEquals("Nom du groupe",$headers["groupName"]->m_descript,'Header #1');
$test->assertEquals("Poids",$headers["weigth"]->m_descript,'Header #2');

$rep=$test->CallAction("CORE","groups_APAS_modifier",array(),"Xfer_Container_Custom");
$test->assertEquals(2,COUNT($rep->m_actions),'nb action');
$act=$rep->m_actions[0];
$test->assertEquals("_OK",$act->m_title,'Titre action #1');
$test->assertEquals("CORE",$act->m_extension,'Ext action #1');
$test->assertEquals("groups_APAS_ajouter",$act->m_action,'Act action #1');
$act=$rep->m_actions[1];
$test->assertEquals("_Annuler",$act->m_title,'Titre action #2');
$test->assertEquals("",$act->m_extension,'Ext action #2');
$test->assertEquals("",$act->m_action,'Act action #2');
$test->assertEquals(5,$rep->getComponentCount(),'nb component');
//IMAGE - img
$comp=$rep->getComponents('img');
$test->assertClass("Xfer_Comp_Image",$comp,"Classe de img");
$test->assertEquals("images/group.png","".$comp->m_value,"Valeur de img");
//EDIT - groupName
$comp=$rep->getComponents('groupName');
$test->assertClass("Xfer_Comp_Edit",$comp,"Classe de groupName");
$test->assertEquals("","".$comp->m_value,"Valeur de groupName");
//FLOAT - weigth
$comp=$rep->getComponents('weigth');
$test->assertClass("Xfer_Comp_Float",$comp,"Classe de weigth");
$test->assertEquals("0","".$comp->m_value,"Valeur de weigth");

$test->CallAction("CORE","groups_APAS_ajouter",array("groupName"=>"Essai","weigth"=>"45",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
//GRID - group
$comp=$rep->getComponents('group');
$test->assertEquals(5,count($comp->m_actions),"Nb grid actions de group");
$test->assertEquals(2,count($comp->m_headers),"Nb grid headers de group");
$test->assertEquals(1,count($comp->m_records),"Nb grid records de group");
$rec=$comp->m_records[100];
$test->assertEquals("Essai",$rec["groupName"],"Valeur de grid [100,groupName]");
$test->assertEquals("45",$rec["weigth"],"Valeur de grid [100,weigth]");

$rep=$test->CallAction("CORE","groups_APAS_editer",array("ORIGINE"=>"groups_APAS_editer","extension"=>"100","group"=>"100",),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
$act=$rep->m_actions[0];
$test->assertEquals("_Fermer",$act->m_title,'Titre action #1');
$test->assertEquals("",$act->m_extension,'Ext action #1');
$test->assertEquals("",$act->m_action,'Act action #1');
$test->assertEquals(8,$rep->getComponentCount(),'nb component');
//IMAGE - img
$comp=$rep->getComponents('img');
$test->assertClass("Xfer_Comp_Image",$comp,"Classe de img");
$test->assertEquals("images/group.png","".$comp->m_value,"Valeur de img");
//LABEL - groupName
$comp=$rep->getComponents('groupName');
$test->assertClass("Xfer_Comp_Label",$comp,"Classe de groupName");
$test->assertEquals("Essai","".$comp->m_value,"Valeur de groupName");
//LABEL - weigth
$comp=$rep->getComponents('weigth');
$test->assertClass("Xfer_Comp_Label",$comp,"Classe de weigth");
$test->assertEquals("45","".$comp->m_value,"Valeur de weigth");
//SELECT - extension
$comp=$rep->getComponents('extension');
$test->assertClass("Xfer_Comp_Select",$comp,"Classe de extension");
//GRID - groupright
$comp=$rep->getComponents('groupright');
$test->assertEquals(1,count($comp->m_actions),"Nb grid actions de groupright");
$test->assertEquals(3,count($comp->m_headers),"Nb grid headers de groupright");
$test->assertEquals(12,count($comp->m_records),"Nb grid records de groupright");
$act=$comp->m_actions[0];
$test->assertEquals("Changer",$act->m_title,'Titre grid action #1');
$test->assertEquals("CORE",$act->m_extension,'Ext grid action #1');
$test->assertEquals("group_rights_APAS_modify",$act->m_action,'Act grid action #1');
$headers=$comp->m_headers;
$test->assertEquals("Description",$headers["description"]->m_descript,'Header #1');
$test->assertEquals("Actions",$headers["actions"]->m_descript,'Header #2');
$test->assertEquals("Valeur",$headers["value"]->m_descript,'Header #3');
$key=array_keys($comp->m_records);
$rec=$comp->m_records[$key[0]];
$test->assertEquals("Acceder au menu de l`application",$rec["description"],"Valeur de grid [0,description]");
$test->assertEquals("Déverouillage{[newline]}Desconnection{[newline]}Import grille{[newline]}Menu de l application{[newline]}Résumé{[newline]}Valider la modification de mon compte{[newline]}Modifier mon compte{[newline]}",$rec["actions"],"Valeur de grid [0,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [0,value]");
$rec=$comp->m_records[$key[1]];
$test->assertEquals("Activer/Desactiver une extension",$rec["description"],"Valeur de grid [1,description]");
$test->assertEquals("Supprimer une extension{[newline]}Liste des actions d`une extension{[newline]}Liste des extentions{[newline]}",$rec["actions"],"Valeur de grid [1,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [1,value]");
$rec=$comp->m_records[$key[2]];
$test->assertEquals("Ajouter/Modifier un groupe",$rec["description"],"Valeur de grid [2,description]");
$test->assertEquals("Liste des droits de groupes{[newline]}Modifier un droit{[newline]}Ajouter un groupe{[newline]}Cloner un groupe{[newline]}Editer les droits d`un groupe{[newline]}Liste des groupes{[newline]}Ajouter/Modifier un groupe{[newline]}Supprimer un groupe{[newline]}",$rec["actions"],"Valeur de grid [2,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [2,value]");
$rec=$comp->m_records[$key[3]];
$test->assertEquals("Ajouter/modifier un utilisateur",$rec["description"],"Valeur de grid [3,description]");
$test->assertEquals("Supprimer un utilisateur{[newline]}Ajouter un utilisateur{[newline]}Désactiver un utilisateur{[newline]}Liste des utilisateurs{[newline]}modifier un utilisateur{[newline]}Modifier un utilisateur{[newline]}Résactiver un utilisateur{[newline]}",$rec["actions"],"Valeur de grid [3,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [3,value]");
$rec=$comp->m_records[$key[4]];
$test->assertEquals("Archivage",$rec["description"],"Valeur de grid [4,description]");
$test->assertEquals("Suppression d`un archive{[newline]}Sauvegarder les données{[newline]}Sauvegarder les données{[newline]}Gestion des sauvegardes{[newline]}Sauvegarder les données{[newline]}Restauration de données{[newline]}Restaurer les données{[newline]}Sauvegarde des données{[newline]}Restaurer les données{[newline]}Gestion des sauvegardes{[newline]}",$rec["actions"],"Valeur de grid [4,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [4,value]");
$rec=$comp->m_records[$key[5]];
$test->assertEquals("Changer de mot de passe",$rec["description"],"Valeur de grid [5,description]");
$test->assertEquals("Changer de mot de passe{[newline]}Changer mot de passe{[newline]}",$rec["actions"],"Valeur de grid [5,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [5,value]");
$rec=$comp->m_records[$key[6]];
$test->assertEquals("Consultation de session de connexion",$rec["description"],"Valeur de grid [6,description]");
$test->assertEquals("Tuer une session{[newline]}Consultation des session{[newline]}",$rec["actions"],"Valeur de grid [6,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [6,value]");
$rec=$comp->m_records[$key[7]];
$test->assertEquals("Consulter les paramètres généreaux",$rec["description"],"Valeur de grid [7,description]");
$test->assertEquals("Configuration{[newline]}",$rec["actions"],"Valeur de grid [7,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [7,value]");
$rec=$comp->m_records[$key[8]];
$test->assertEquals("Gestion des autorisation d`acces réseau",$rec["description"],"Valeur de grid [8,description]");
$test->assertEquals("Ajouter/modifier un accès{[newline]}Liste des accès{[newline]}Supprimer un accès{[newline]}Valider l`ajouts d`accès{[newline]}",$rec["actions"],"Valeur de grid [8,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [8,value]");
$rec=$comp->m_records[$key[9]];
$test->assertEquals("Impression",$rec["description"],"Valeur de grid [9,description]");
$test->assertEquals("Validation{[newline]}Ajouter/Modifier une étiquette{[newline]}Liste des étiquettes{[newline]}Supprimer une étiquette{[newline]}Liste des impression{[newline]}reimprimer{[newline]}Editer un modèle{[newline]}Liste des modèles d`impression{[newline]}Réinitialiser un modèle{[newline]}",$rec["actions"],"Valeur de grid [9,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [9,value]");
$rec=$comp->m_records[$key[10]];
$test->assertEquals("Modifier les paramètres généraux",$rec["description"],"Valeur de grid [10,description]");
$test->assertEquals("Mise à jour{[newline]}Modifier un paramètre{[newline]}Valider une modification de paramètres{[newline]}Suppression d`impression{[newline]}Regénérer une impression{[newline]}Impression de la configuration{[newline]}",$rec["actions"],"Valeur de grid [10,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [10,value]");
$rec=$comp->m_records[$key[11]];
$test->assertEquals("Paramètres généraux (avancé)",$rec["description"],"Valeur de grid [11,description]");
$test->assertEquals("Recharger les configurations{[newline]}Liste des paramètres généraux de l`application{[newline]}",$rec["actions"],"Valeur de grid [11,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [11,value]");

$test->CallAction("CORE","group_rights_APAS_modify",array("ORIGINE"=>"groups_APAS_editer","extension"=>"100","group"=>"100","groupright"=>"$key[3]",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_editer",array("ORIGINE"=>"groups_APAS_editer","extension"=>"100","group"=>"100",),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
$test->assertEquals(8,$rep->getComponentCount(),'nb component');
//GRID - groupright
$comp=$rep->getComponents('groupright');
$test->assertEquals(1,count($comp->m_actions),"Nb grid actions de groupright");
$test->assertEquals(3,count($comp->m_headers),"Nb grid headers de groupright");
$test->assertEquals(12,count($comp->m_records),"Nb grid records de groupright");
$act=$comp->m_actions[0];
$headers=$comp->m_headers;
$key=array_keys($comp->m_records);
$rec=$comp->m_records[$key[0]];
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [0,value]");
$rec=$comp->m_records[$key[1]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [1,value]");
$rec=$comp->m_records[$key[2]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [2,value]");
$rec=$comp->m_records[$key[3]];
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [3,value]");
$rec=$comp->m_records[$key[4]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [4,value]");
$rec=$comp->m_records[$key[5]];
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [5,value]");
$rec=$comp->m_records[$key[6]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [6,value]");
$rec=$comp->m_records[$key[7]];
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [7,value]");
$rec=$comp->m_records[$key[8]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [8,value]");
$rec=$comp->m_records[$key[9]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [9,value]");
$rec=$comp->m_records[$key[10]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [10,value]");
$rec=$comp->m_records[$key[11]];
$test->assertEquals("Non",$rec["value"],"Valeur de grid [11,value]");

$test->CallAction("CORE","UNLOCK",array("ORIGINE"=>"groups_APAS_editer","RECORD_ID"=>"100","TABLE_NAME"=>"CORE_groups","group"=>"100",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
$test->assertEquals(4,$rep->getComponentCount(),'nb component');
//GRID - group
$comp=$rep->getComponents('group');
$test->assertEquals(5,count($comp->m_actions),"Nb grid actions de group");
$test->assertEquals(2,count($comp->m_headers),"Nb grid headers de group");
$test->assertEquals(1,count($comp->m_records),"Nb grid records de group");
$rec=$comp->m_records[100];
$test->assertEquals("Essai",$rec["groupName"],"Valeur de grid [100,groupName]");
$test->assertEquals("45",$rec["weigth"],"Valeur de grid [100,weigth]");

$rep=$test->CallAction("CORE","groups_APAS_cloner",array("group"=>"100",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
//GRID - group
$comp=$rep->getComponents('group');
$test->assertEquals(5,count($comp->m_actions),"Nb grid actions de group");
$test->assertEquals(2,count($comp->m_headers),"Nb grid headers de group");
$test->assertEquals(2,count($comp->m_records),"Nb grid records de group");
$rec=$comp->m_records[100];
$test->assertEquals("Essai",$rec["groupName"],"Valeur de grid [100,groupName]");
$test->assertEquals("45",$rec["weigth"],"Valeur de grid [100,weigth]");
$rec=$comp->m_records[101];
$test->assertEquals("Copie de Essai",$rec["groupName"],"Valeur de grid [101,groupName]");
$test->assertEquals("45",$rec["weigth"],"Valeur de grid [101,weigth]");

$rep=$test->CallAction("CORE","groups_APAS_editer",array("ORIGINE"=>"groups_APAS_editer","extension"=>"100","group"=>"101",),"Xfer_Container_Custom");
$test->assertEquals(1,COUNT($rep->m_actions),'nb action');
//LABEL - groupName
$comp=$rep->getComponents('groupName');
$test->assertClass("Xfer_Comp_Label",$comp,"Classe de groupName");
$test->assertEquals("Copie de Essai","".$comp->m_value,"Valeur de groupName");
//LABEL - weigth
$comp=$rep->getComponents('weigth');
$test->assertClass("Xfer_Comp_Label",$comp,"Classe de weigth");
$test->assertEquals("45","".$comp->m_value,"Valeur de weigth");
//GRID - groupright
$comp=$rep->getComponents('groupright');
$test->assertEquals(1,count($comp->m_actions),"Nb grid actions de groupright");
$test->assertEquals(3,count($comp->m_headers),"Nb grid headers de groupright");
$test->assertEquals(12,count($comp->m_records),"Nb grid records de groupright");
$key=array_keys($comp->m_records);
$rec=$comp->m_records[$key[0]];
$test->assertEquals("Acceder au menu de l`application",$rec["description"],"Valeur de grid [0,description]");
$test->assertEquals("Déverouillage{[newline]}Desconnection{[newline]}Import grille{[newline]}Menu de l application{[newline]}Résumé{[newline]}Valider la modification de mon compte{[newline]}Modifier mon compte{[newline]}",$rec["actions"],"Valeur de grid [0,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [0,value]");
$rec=$comp->m_records[$key[1]];
$test->assertEquals("Activer/Desactiver une extension",$rec["description"],"Valeur de grid [1,description]");
$test->assertEquals("Supprimer une extension{[newline]}Liste des actions d`une extension{[newline]}Liste des extentions{[newline]}",$rec["actions"],"Valeur de grid [1,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [1,value]");
$rec=$comp->m_records[$key[2]];
$test->assertEquals("Ajouter/Modifier un groupe",$rec["description"],"Valeur de grid [2,description]");
$test->assertEquals("Liste des droits de groupes{[newline]}Modifier un droit{[newline]}Ajouter un groupe{[newline]}Cloner un groupe{[newline]}Editer les droits d`un groupe{[newline]}Liste des groupes{[newline]}Ajouter/Modifier un groupe{[newline]}Supprimer un groupe{[newline]}",$rec["actions"],"Valeur de grid [2,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [2,value]");
$rec=$comp->m_records[$key[3]];
$test->assertEquals("Ajouter/modifier un utilisateur",$rec["description"],"Valeur de grid [3,description]");
$test->assertEquals("Supprimer un utilisateur{[newline]}Ajouter un utilisateur{[newline]}Désactiver un utilisateur{[newline]}Liste des utilisateurs{[newline]}modifier un utilisateur{[newline]}Modifier un utilisateur{[newline]}Résactiver un utilisateur{[newline]}",$rec["actions"],"Valeur de grid [3,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [3,value]");
$rec=$comp->m_records[$key[4]];
$test->assertEquals("Archivage",$rec["description"],"Valeur de grid [4,description]");
$test->assertEquals("Suppression d`un archive{[newline]}Sauvegarder les données{[newline]}Sauvegarder les données{[newline]}Gestion des sauvegardes{[newline]}Sauvegarder les données{[newline]}Restauration de données{[newline]}Restaurer les données{[newline]}Sauvegarde des données{[newline]}Restaurer les données{[newline]}Gestion des sauvegardes{[newline]}",$rec["actions"],"Valeur de grid [4,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [4,value]");
$rec=$comp->m_records[$key[5]];
$test->assertEquals("Changer de mot de passe",$rec["description"],"Valeur de grid [5,description]");
$test->assertEquals("Changer de mot de passe{[newline]}Changer mot de passe{[newline]}",$rec["actions"],"Valeur de grid [5,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [5,value]");
$rec=$comp->m_records[$key[6]];
$test->assertEquals("Consultation de session de connexion",$rec["description"],"Valeur de grid [6,description]");
$test->assertEquals("Tuer une session{[newline]}Consultation des session{[newline]}",$rec["actions"],"Valeur de grid [6,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [6,value]");
$rec=$comp->m_records[$key[7]];
$test->assertEquals("Consulter les paramètres généreaux",$rec["description"],"Valeur de grid [7,description]");
$test->assertEquals("Configuration{[newline]}",$rec["actions"],"Valeur de grid [7,actions]");
$test->assertEquals("Oui",$rec["value"],"Valeur de grid [7,value]");
$rec=$comp->m_records[$key[8]];
$test->assertEquals("Gestion des autorisation d`acces réseau",$rec["description"],"Valeur de grid [8,description]");
$test->assertEquals("Ajouter/modifier un accès{[newline]}Liste des accès{[newline]}Supprimer un accès{[newline]}Valider l`ajouts d`accès{[newline]}",$rec["actions"],"Valeur de grid [8,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [8,value]");
$rec=$comp->m_records[$key[9]];
$test->assertEquals("Impression",$rec["description"],"Valeur de grid [9,description]");
$test->assertEquals("Validation{[newline]}Ajouter/Modifier une étiquette{[newline]}Liste des étiquettes{[newline]}Supprimer une étiquette{[newline]}Liste des impression{[newline]}reimprimer{[newline]}Editer un modèle{[newline]}Liste des modèles d`impression{[newline]}Réinitialiser un modèle{[newline]}",$rec["actions"],"Valeur de grid [9,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [9,value]");
$rec=$comp->m_records[$key[10]];
$test->assertEquals("Modifier les paramètres généraux",$rec["description"],"Valeur de grid [10,description]");
$test->assertEquals("Mise à jour{[newline]}Modifier un paramètre{[newline]}Valider une modification de paramètres{[newline]}Suppression d`impression{[newline]}Regénérer une impression{[newline]}Impression de la configuration{[newline]}",$rec["actions"],"Valeur de grid [10,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [10,value]");
$rec=$comp->m_records[$key[11]];
$test->assertEquals("Paramètres généraux (avancé)",$rec["description"],"Valeur de grid [11,description]");
$test->assertEquals("Recharger les configurations{[newline]}Liste des paramètres généraux de l`application{[newline]}",$rec["actions"],"Valeur de grid [11,actions]");
$test->assertEquals("Non",$rec["value"],"Valeur de grid [11,value]");
$test->CallAction("CORE","UNLOCK",array("ORIGINE"=>"groups_APAS_editer","RECORD_ID"=>"101","TABLE_NAME"=>"CORE_groups","extension"=>"100","group"=>"101",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_supprimer",array("group"=>"100",),"Xfer_Container_DialogBox");
$test->assertEquals(2,$rep->m_type,'Type dialogue');
$test->assertEquals("Etes vous sûre de vouloir supprimer ce groupe?",$rep->m_text,'Text dialogue');
$act=$rep->m_actions[0];
$test->assertEquals("Oui",$act->m_title,'Titre action #1');
$test->assertEquals("CORE",$act->m_extension,'Ext action #1');
$test->assertEquals("groups_APAS_supprimer",$act->m_action,'Act action #1');
$act=$rep->m_actions[1];
$test->assertEquals("Non",$act->m_title,'Titre action #2');
$test->assertEquals("",$act->m_extension,'Ext action #2');
$test->assertEquals("",$act->m_action,'Act action #2');
$test->CallAction("CORE","UNLOCK",array("CONFIRME"=>"YES","ORIGINE"=>"groups_APAS_supprimer","RECORD_ID"=>"100","TABLE_NAME"=>"CORE_groups","group"=>"100",),"Xfer_Container_Acknowledge");
$test->CallAction("CORE","groups_APAS_supprimer",array("CONFIRME"=>"YES","ORIGINE"=>"groups_APAS_supprimer","RECORD_ID"=>"100","TABLE_NAME"=>"CORE_groups","group"=>"100",),"Xfer_Container_Acknowledge");
$test->CallAction("CORE","UNLOCK",array("CONFIRME"=>"YES","ORIGINE"=>"groups_APAS_supprimer","RECORD_ID"=>"100","TABLE_NAME"=>"CORE_groups","group"=>"100",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$comp=$rep->getComponents('group');
$test->assertEquals(1,count($comp->m_records),"Nb grid records de group");
$rec=$comp->m_records[101];
$test->assertEquals("Copie de Essai",$rec["groupName"],"Valeur de grid [101,groupName]");
$test->assertEquals("45",$rec["weigth"],"Valeur de grid [101,weigth]");

$rep=$test->CallAction("CORE","groups_APAS_supprimer",array("group"=>"101",),"Xfer_Container_DialogBox");
$test->assertEquals(2,$rep->m_type,'Type dialogue');
$test->assertEquals("Etes vous sûre de vouloir supprimer ce groupe?",$rep->m_text,'Text dialogue');
$test->CallAction("CORE","UNLOCK",array("CONFIRME"=>"YES","ORIGINE"=>"groups_APAS_supprimer","RECORD_ID"=>"101","TABLE_NAME"=>"CORE_groups","group"=>"101",),"Xfer_Container_Acknowledge");
$test->CallAction("CORE","groups_APAS_supprimer",array("CONFIRME"=>"YES","ORIGINE"=>"groups_APAS_supprimer","RECORD_ID"=>"101","TABLE_NAME"=>"CORE_groups","group"=>"101",),"Xfer_Container_Acknowledge");
$test->CallAction("CORE","UNLOCK",array("CONFIRME"=>"YES","ORIGINE"=>"groups_APAS_supprimer","RECORD_ID"=>"101","TABLE_NAME"=>"CORE_groups","group"=>"101",),"Xfer_Container_Acknowledge");

$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
$comp=$rep->getComponents('group');
$test->assertEquals(0,count($comp->m_records),"Nb grid records de group");
//@CODE_ACTION@
}

?>
