<?php
// test file write by SDK tool
// --- Date 16 August 2006 20:54:16 By Laurent GAY ---

require_once('CORE/ApasUnit.inc.php');

class APASUnit_CORE extends APASUnit_TestCase
{
//@BEGIN@
	function testMenu()
	{
		$rep=$this->CallAction("CORE","menu",array(),"Xfer_Container_Menu");
		$this->assertEquals(1,count($rep->m_main_menus->m_sub_menus));
		$this->assertEquals(3,count($rep->m_main_menus->m_sub_menus[0]->m_sub_menus));
		$menu_admin=$this->GetMenu($rep->m_main_menus->m_sub_menus[0]->m_sub_menus,"Ad_ministration");
		$this->assertEquals("Ad_ministration",$menu_admin->m_title);
		$this->assertEquals("CORE",$menu_admin->m_extension);
		$this->assertEquals("",$menu_admin->m_action);
		$this->assertEquals(6,count($menu_admin->m_sub_menus));

		$menu_item=$this->GetMenu($menu_admin->m_sub_menus,"Changerdemotdepasse");
		$this->assertEquals("_Changer de mot de passe",$menu_item->m_title);
		$this->assertEquals("CORE",$menu_item->m_extension);
		$this->assertEquals("users_APAS_changerpassword",$menu_item->m_action);
		$this->assertEquals(0,count($menu_item->m_sub_menus));

		$menu_item=$this->GetMenu($menu_admin->m_sub_menus,"Misesajouretinstalls");
		$this->assertEquals("Mises à jour et installs",utf8_decode($menu_item->m_title));
		$this->assertEquals("updates",$menu_item->m_extension);
		$this->assertEquals("searchnew",$menu_item->m_action);
		$this->assertEquals(0,count($menu_item->m_sub_menus));

		//Gestion des droits
		$menu_item=$this->GetMenu($menu_admin->m_sub_menus,"_GestiondesDroits");
		$this->assertEquals("_Gestion des Droits",$menu_item->m_title);
		$this->assertEquals("CORE",$menu_item->m_extension);
		$this->assertEquals("",$menu_item->m_action);
		$this->assertEquals(3,count($menu_item->m_sub_menus));

		$menu_sub_item=$this->GetMenu($menu_item->m_sub_menus,"_Utilisateurs");
		$this->assertEquals("_Utilisateurs",$menu_sub_item->m_title);
		$this->assertEquals("CORE",$menu_sub_item->m_extension);
		$this->assertEquals("users_APAS_list",$menu_sub_item->m_action);
		$this->assertEquals(0,count($menu_sub_item->m_sub_menus));

		$menu_sub_item=$this->GetMenu($menu_item->m_sub_menus,"_Groupes");
		$this->assertEquals("_Groupes",$menu_sub_item->m_title);
		$this->assertEquals("CORE",$menu_sub_item->m_extension);
		$this->assertEquals("groups_APAS_liste",$menu_sub_item->m_action);
		$this->assertEquals(0,count($menu_sub_item->m_sub_menus));

		$menu_sub_item=$this->GetMenu($menu_item->m_sub_menus,"_Extensions");
		$this->assertEquals("_Extensions",$menu_sub_item->m_title);
		$this->assertEquals("CORE",$menu_sub_item->m_extension);
		$this->assertEquals("extension_APAS_list",$menu_sub_item->m_action);
		$this->assertEquals(0,count($menu_sub_item->m_sub_menus));

		//Avance
		$menu_item=$this->GetMenu($menu_admin->m_sub_menus,"_Avance");
		$this->assertEquals("_Avancé",$menu_item->m_title);
		$this->assertEquals("CORE",$menu_item->m_extension);
		$this->assertEquals("",$menu_item->m_action);
		$this->assertEquals(3,count($menu_item->m_sub_menus));

		$menu_sub_item=$this->GetMenu($menu_item->m_sub_menus,"_Parametres");
		$this->assertEquals("_Paramètres",utf8_decode($menu_sub_item->m_title));
		$this->assertEquals("CORE",$menu_sub_item->m_extension);
		$this->assertEquals("extension_params_APAS_list",$menu_sub_item->m_action);
		$this->assertEquals(0,count($menu_sub_item->m_sub_menus));

		$menu_sub_item=$this->GetMenu($menu_item->m_sub_menus,"Autorisationd`acces_reseau");
		$this->assertEquals("Autorisation d`acces _réseau",utf8_decode($menu_sub_item->m_title));
		$this->assertEquals("CORE",$menu_sub_item->m_extension);
		$this->assertEquals("access_APAS_list",$menu_sub_item->m_action);
		$this->assertEquals(0,count($menu_sub_item->m_sub_menus));

		$menu_sub_item=$this->GetMenu($menu_item->m_sub_menus,"_Session");
		$this->assertEquals("_Session",$menu_sub_item->m_title);
		$this->assertEquals("CORE",$menu_sub_item->m_extension);
		$this->assertEquals("sessions_APAS_list",$menu_sub_item->m_action);
		$this->assertEquals(0,count($menu_sub_item->m_sub_menus));

		//Impression
		$menu_item=$this->GetMenu($menu_admin->m_sub_menus,"_Impression");
		$this->assertEquals("_Impression",$menu_item->m_title);
		$this->assertEquals("CORE",$menu_item->m_extension);
		$this->assertEquals("",$menu_item->m_action);
		$this->assertEquals(2,count($menu_item->m_sub_menus));

		$menu_item2=$this->GetMenu($menu_item->m_sub_menus,"Listesdes_models");
		$this->assertEquals("CORE",$menu_item2->m_extension);
		$this->assertEquals("printmodel_APAS_list",$menu_item2->m_action);
		$this->assertEquals("Listes des _models",$menu_item2->m_title);
		$this->assertEquals(count($menu_item2->m_sub_menus),0);

		$menu_item2=$this->GetMenu($menu_item->m_sub_menus,"Im_pressionsauvegardees");
		$this->assertEquals("CORE",$menu_item2->m_extension);
		$this->assertEquals("finalreport_APAS_list",$menu_item2->m_action);
		$this->assertEquals("Im_pression sauvegardées",utf8_decode($menu_item2->m_title));
		$this->assertEquals(count($menu_item2->m_sub_menus),0);
	}
	function testImport()
	{
            $textCVS="";
            $textCVS.="Ordre;Nom;Prenom;Sexe;Adresse;Cdpostal;Ville{[newline]}";
            $textCVS.="3;MACHIN;Céline;1;41 rue du Grand;38600;FONTAINE{[newline]}";
            $textCVS.="8;BIDULE;Steve;0;21 rue H.;38360;SASSENAGE{[newline]}";
            $textCVS.="78;TRUC;Laurent;0;74 rue Charles de GAULLE;38600;FONTAINE{[newline]}";

		$rep=$this->CallAction("CORE","importGrid",array("extension"=>"CORE", "action"=>"importGrid", "textCVS"=>$textCVS),"Xfer_Container_Custom");
		if ($rep!=null)
		{
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Valider", "ok.png", "CORE", "importGrid","0","1"),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png"),$rep->m_actions[1]);

		$this->assertEquals(2,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_Labelform",$comp);

		$comp=$rep->getComponents(1);
		$this->assertEquals(0,count($comp->m_actions));

		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("gridcvs",$comp->m_name);
		$this->assertEquals(7,count($comp->m_headers));
		$headers=array_keys($comp->m_headers);
		$this->assertEquals('Ordre',$headers[0]);
		$this->assertEquals('Ordre',$comp->m_headers['Ordre']->m_descript);
		$this->assertEquals('Nom',$headers[1]);
		$this->assertEquals('Nom',$comp->m_headers['Nom']->m_descript);
		$this->assertEquals('Prenom',$headers[2]);
		$this->assertEquals('Prenom',$comp->m_headers['Prenom']->m_descript);
		$this->assertEquals('Sexe',$headers[3]);
		$this->assertEquals('Sexe',$comp->m_headers['Sexe']->m_descript);
		$this->assertEquals('Adresse',$headers[4]);
		$this->assertEquals('Adresse',$comp->m_headers['Adresse']->m_descript);
		$this->assertEquals('Cdpostal',$headers[5]);
		$this->assertEquals('Cdpostal',$comp->m_headers['Cdpostal']->m_descript);
		$this->assertEquals('Ville',$headers[6]);
		$this->assertEquals('Ville',$comp->m_headers['Ville']->m_descript);

		$this->assertEquals(3,count($comp->m_records));
		$key=array_keys($comp->m_records);
		$this->assertEquals('0',$key[0]);
		$this->assertEquals('1',$key[1]);
		$this->assertEquals('2',$key[2]);

		$this->assertEquals(7,count($comp->m_records[$key[0]]));
		$this->assertEquals("3",$comp->m_records[$key[0]]['Ordre']);
		$this->assertEquals("MACHIN",$comp->m_records[$key[0]]['Nom']);
		$this->assertEquals("1",$comp->m_records[$key[0]]['Sexe']);
		$this->assertEquals("41 rue du Grand",$comp->m_records[$key[0]]['Adresse']);
		$this->assertEquals("38600",$comp->m_records[$key[0]]['Cdpostal']);
		$this->assertEquals("FONTAINE",$comp->m_records[$key[0]]['Ville']);

		$this->assertEquals("8",$comp->m_records[$key[1]]['Ordre']);
		$this->assertEquals("BIDULE",$comp->m_records[$key[1]]['Nom']);
		$this->assertEquals("0",$comp->m_records[$key[1]]['Sexe']);
		$this->assertEquals("21 rue H.",$comp->m_records[$key[1]]['Adresse']);
		$this->assertEquals("38360",$comp->m_records[$key[1]]['Cdpostal']);
		$this->assertEquals("SASSENAGE",$comp->m_records[$key[1]]['Ville']);

		$this->assertEquals("78",$comp->m_records[$key[2]]['Ordre']);
		$this->assertEquals("TRUC",$comp->m_records[$key[2]]['Nom']);
		$this->assertEquals("0",$comp->m_records[$key[2]]['Sexe']);
		$this->assertEquals("74 rue Charles de GAULLE",$comp->m_records[$key[2]]['Adresse']);
		$this->assertEquals("38600",$comp->m_records[$key[2]]['Cdpostal']);
		$this->assertEquals("FONTAINE",$comp->m_records[$key[2]]['Ville']);
		}
	}

//@END@
}
?>
