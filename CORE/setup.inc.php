<?php
// 	This file is part of Lucterios/Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Lucterios/Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Lucterios/Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY// setup file write by SDK tool
// --- Last modification: Date 06 March 2012 3:51:44 By  ---

$extention_name="CORE";
$extention_description="Coeur du serveur Lucterios.{[newline]}Gère la connexion au logiciel, les droits d`accès ainsi que l`integration des autres modules.";
$extention_appli="";
$extention_famille="CORE";
$extention_titre="Noyau Lucterios";
$extension_libre=true;

$version_max=1;
$version_min=3;
$version_release=3;
$version_build=768;

$depencies=array();

$rights=array();
$rights[0] = new Param_Rigth("Acceder au menu de l`application",0);
$rights[1] = new Param_Rigth("Ajouter/modifier un utilisateur",70);
$rights[2] = new Param_Rigth("Ajouter/Modifier un groupe",90);
$rights[3] = new Param_Rigth("Modifier les paramètres généraux",90);
$rights[4] = new Param_Rigth("Activer/Desactiver une extension",70);
$rights[5] = new Param_Rigth("Changer de mot de passe",15);
$rights[6] = new Param_Rigth("Impression",50);
$rights[7] = new Param_Rigth("Gestion des autorisation d`acces réseau",90);
$rights[8] = new Param_Rigth("Consultation de session de connexion",70);
$rights[9] = new Param_Rigth("Consulter les paramètres généreaux",30);
$rights[10] = new Param_Rigth("Paramètres généraux (avancé)",101);
$rights[11] = new Param_Rigth("Archivage",90);

$menus=array();
$menus[1] = new Param_Menu("Ad_ministration", "", "", "admin.png", "", 100 , 0, "Adminitration des configurations et des réglages.");
$menus[2] = new Param_Menu("_Utilisateurs", "_Gestion des Droits", "users_APAS_list", "user.png", "", 5 , 0, "Gestion des utilisateurs autorisés à se connecter.");
$menus[4] = new Param_Menu("_Paramètres", "_Avancé", "extension_params_APAS_list", "", "", 20 , 0, "");
$menus[5] = new Param_Menu("_Extensions", "_Gestion des Droits", "extension_APAS_list", "extensions.png", "", 15 , 0, "Gestion des modules et association des droits.");
$menus[6] = new Param_Menu("_Mot de passe", "Ad_ministration", "users_APAS_changerpassword", "passwd.png", "", 5 , 1, "Changement de votre mot de passe.");
$menus[7] = new Param_Menu("_Groupes", "_Gestion des Droits", "groups_APAS_liste", "group.png", "", 10 , 0, "Gestion d'un groupe de droits d'accès.");
$menus[8] = new Param_Menu("_Rapport et Impression", "Ad_ministration", "", "PrintReport.png", "", 25 , 0, "Gestion de vos rapports et des outils d'impression.");
$menus[9] = new Param_Menu("_Modèles des rapports", "_Rapport et Impression", "printmodel_APAS_list", "PrintReportModel.png", "", 30 , 0, "Gestion des différents modèles d'impression.");
$menus[10] = new Param_Menu("Autorisation d`accès _réseau", "_Avancé", "access_APAS_list", "", "", 30 , 0, "");
$menus[11] = new Param_Menu("_Session", "_Avancé", "sessions_APAS_list", "", "", 35 , 0, "");
$menus[12] = new Param_Menu("_Gestion des Droits", "Ad_ministration", "", "gestionDroits.png", "", 40 , 0, "Gestion des utilisateurs et de leurs droits selon les modules.");
$menus[13] = new Param_Menu("_Avancé", "Ad_ministration", "", "", "", 50 , 0, "");
$menus[14] = new Param_Menu("Ra_pports sauvegardés", "_Rapport et Impression", "finalreport_APAS_list", "PrintReportSave.png", "", 20 , 0, "Ré-édition des anciennes impressions sauvegardées");
$menus[15] = new Param_Menu("_Extensions (conf.)", "Ad_ministration", "", "config_ext.png", "", 20 , 0, "Gestion des configurations des différentes modules.");
$menus[16] = new Param_Menu("Configuration _générale", "Ad_ministration", "configuration", "config.png", "shift ctrl alt C", 10 , 1, "Visualisation et modification des paramètres généraux.");
$menus[17] = new Param_Menu("_Etiquettes", "_Rapport et Impression", "etiquettes_APAS_liste", "PrintReportLabel.png", "", 5 , 0, "Gestion des planches d'étiquettes");
$menus[18] = new Param_Menu("Ar_chivage", "Ad_ministration", "", "backup.png", "", 15 , 0, "Outils de sauvegarde et de restoration des données.");
$menus[19] = new Param_Menu("_Sauvegarder", "Ar_chivage", "selectNewArchive", "backup_save.png", "", 10 , 1, "Sauvegarde manuel des données du logiciel.");
$menus[20] = new Param_Menu("_Restauration", "Ar_chivage", "selectRestor", "backup_restor.png", "", 20 , 1, "Restauration d'une archives.");
$menus[21] = new Param_Menu("_Gestion des archives", "Ar_chivage", "toolBackup", "backup_tool.png", "", 30 , 1, "Importer ou télécharger des archives de sauvegarde");

$actions=array();
$actions[0] = new Param_Action("Déverouillage", "UNLOCK", 0);
$actions[1] = new Param_Action("Ajouter/modifier un accès", "access_APAS_ajouter", 7);
$actions[2] = new Param_Action("Liste des accès", "access_APAS_list", 7);
$actions[3] = new Param_Action("Supprimer un accès", "access_APAS_supprimer", 7);
$actions[4] = new Param_Action("Ajouter/modifier un accès", "access_APAS_valider", 7);
$actions[5] = new Param_Action("Suppression d'un archive", "archiveDelete", 11);
$actions[6] = new Param_Action("Sauvegarder les données", "archiveDownload", 11);
$actions[7] = new Param_Action("Sauvegarder les données", "archiveForm", 11);
$actions[8] = new Param_Action("Gestion des sauvegardes", "archiveUpload", 11);
$actions[9] = new Param_Action("Sauvegarder les données", "archive", 11);
$actions[10] = new Param_Action("Configuration", "configuration", 9);
$actions[11] = new Param_Action("Validation", "etiquettes_APAS_ajouteract", 6);
$actions[12] = new Param_Action("Ajouter/Modifier une étiquette", "etiquettes_APAS_ajouter", 6);
$actions[13] = new Param_Action("Liste des étiquettes", "etiquettes_APAS_liste", 6);
$actions[14] = new Param_Action("Supprimer une étiquette", "etiquettes_APAS_supprimer", 6);
$actions[15] = new Param_Action("Desconnection", "exitConnection", 0);
$actions[16] = new Param_Action("Supprimer une extension", "extension_APAS_Delete", 4);
$actions[17] = new Param_Action("Liste des actions d'une extension", "extension_APAS_listactions", 4);
$actions[18] = new Param_Action("Liste des extentions", "extension_APAS_list", 4);
$actions[19] = new Param_Action("Recharger les configurations", "extension_APAS_reload", 10);
$actions[20] = new Param_Action("Liste des paramètres généraux de l`application", "extension_params_APAS_list", 10);
$actions[21] = new Param_Action("Mise à jour", "extension_params_APAS_miseajour", 3);
$actions[22] = new Param_Action("Modifier un paramètre", "extension_params_APAS_modifier", 3);
$actions[23] = new Param_Action("Modifier un paramètre", "extension_params_APAS_validerModif", 3);
$actions[24] = new Param_Action("Liste des droits de groupes", "extension_rights_APAS_editer", 2);
$actions[25] = new Param_Action("Suppression d'impression", "finalreport_APAS_delete", 3);
$actions[26] = new Param_Action("Liste des impression", "finalreport_APAS_list", 6);
$actions[27] = new Param_Action("Regénérer une impression", "finalreport_APAS_regenerer", 3);
$actions[28] = new Param_Action("reimprimer", "finalreport_APAS_reprint", 6);
$actions[29] = new Param_Action("Modifier un droit", "group_rights_APAS_modify", 2);
$actions[30] = new Param_Action("Ajouter un groupe", "groups_APAS_ajouter", 2);
$actions[31] = new Param_Action("Cloner un groupe", "groups_APAS_cloner", 2);
$actions[32] = new Param_Action("Editer les droits d'un groupe", "groups_APAS_editer", 2);
$actions[33] = new Param_Action("Liste des groupes", "groups_APAS_liste", 2);
$actions[34] = new Param_Action("Ajouter/Modifier un groupe", "groups_APAS_modifier", 2);
$actions[35] = new Param_Action("Supprimer un groupe", "groups_APAS_supprimer", 2);
$actions[36] = new Param_Action("Import grille", "importGrid", 0);
$actions[37] = new Param_Action("Menu de l application", "menu", 0);
$actions[38] = new Param_Action("Impression de la configuration", "printConf", 3);
$actions[39] = new Param_Action("Editer un modèle", "printmodel_APAS_edit", 6);
$actions[40] = new Param_Action("Liste des modèles d`impression", "printmodel_APAS_list", 6);
$actions[41] = new Param_Action("Réinitialiser un modèle", "printmodel_APAS_reinit", 6);
$actions[42] = new Param_Action("Restauration de données", "restorForm", 11);
$actions[43] = new Param_Action("Restaurer les données", "restor", 11);
$actions[44] = new Param_Action("Sauvegarde d'archive", "selectNewArchive", 11);
$actions[45] = new Param_Action("Restaurer les données", "selectRestor", 11);
$actions[46] = new Param_Action("Tuer une session", "sessions_APAS_killsession", 8);
$actions[47] = new Param_Action("Consultation des session", "sessions_APAS_list", 8);
$actions[48] = new Param_Action("Résumé", "status", 0);
$actions[49] = new Param_Action("Gestion des sauvegardes", "toolBackup", 11);
$actions[50] = new Param_Action("Promouvoir un enregistrement", "upRecordClassAct", 1);
$actions[51] = new Param_Action("Promouvoir un enregistrement", "upRecordClass", 1);
$actions[52] = new Param_Action("Modifier mon compte", "users_APAS_AddModifyAct", 0);
$actions[53] = new Param_Action("Modifier mon compte", "users_APAS_AddModify", 0);
$actions[54] = new Param_Action("Supprimer un utilisateur", "users_APAS_Del", 1);
$actions[55] = new Param_Action("Ajouter un utilisateur", "users_APAS_ajouter", 1);
$actions[56] = new Param_Action("Changer de mot de passe", "users_APAS_changerpassword", 5);
$actions[57] = new Param_Action("Changer mot de passe", "users_APAS_confirmpwdmodif", 5);
$actions[58] = new Param_Action("Désactiver un utilisateur", "users_APAS_desactiver", 1);
$actions[59] = new Param_Action("Liste des utilisateurs", "users_APAS_list", 1);
$actions[60] = new Param_Action("Modifier un utilisateur", "users_APAS_miseajour", 1);
$actions[61] = new Param_Action("Modifier un utilisateur", "users_APAS_modifier", 1);
$actions[62] = new Param_Action("Résactiver un utilisateur", "users_APAS_reactiver", 1);

$params=array();

$extend_tables=array();
$extend_tables["access"] = array("CORE.access","",array());
$extend_tables["etiquettes"] = array("CORE.etiquettes","",array());
$extend_tables["extension"] = array("CORE.extension","",array());
$extend_tables["extension_actions"] = array("CORE.extension_actions","",array("CORE_extension"=>"extension","CORE_extension_rights"=>"rights",));
$extend_tables["extension_params"] = array("CORE.extension_params","",array());
$extend_tables["extension_rights"] = array("CORE.extension_rights","",array("CORE_extension"=>"extension",));
$extend_tables["finalreport"] = array("CORE.finalreport","",array());
$extend_tables["group_rights"] = array("CORE.group_rights","",array("CORE_extension_rights"=>"rightref","CORE_groups"=>"groupref",));
$extend_tables["groups"] = array("CORE.groups","",array());
$extend_tables["menu"] = array("CORE.menu","",array());
$extend_tables["printmodel"] = array("CORE.printmodel","",array());
$extend_tables["sessions"] = array("CORE.sessions","",array());
$extend_tables["users"] = array("CORE.users","",array("CORE_groups"=>"groupId",));
$signals=array();

?>