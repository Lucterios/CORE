<?php

require_once("CORE/ApasUnit.inc.php");

class APASUnit_CORE_Extensions extends APASUnit_TestCase
{
//@BEGIN@
	function testExtensionList() 
	{
		$rep=$this->CallAction("CORE","extension_APAS_list",array(),"Xfer_Container_Custom");
		$this->assertEquals(1,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
		$this->assertEquals(2,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("extension",$comp->m_name);
		$this->assertEquals(4,count($comp->m_headers));
		$this->assertEquals(1,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Droits et Actions", "lister.gif", "CORE", "extension_APAS_listactions","1","0","0"),$comp->m_actions[0]);
		$headers=array_keys($comp->m_headers);
		$this->assertEquals("extensionId",$headers[0]);
		$this->assertEquals("description",$headers[1]);
		$this->assertEquals('Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild',$headers[2]);
		$this->assertEquals("validite",$headers[3]);

		$this->assertEquals("CORE",$comp->m_records["100"]["extensionId"]);
		$this->assertEquals("Noyau APAS",$comp->m_records["100"]["description"]);
		$this->assertEquals("0.3",substr($comp->m_records["100"]['Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild'],0,3));
		$this->assertEquals("Oui",$comp->m_records["100"]["validite"]);
	}

	function testDroitAction() 
	{
		$rep=$this->CallAction("CORE","extension_APAS_listactions",array("extension"=>'100'),"Xfer_Container_Custom");
		$this->assertEquals(1,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
		$this->assertEquals(2,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("right",$comp->m_name);
		$this->assertEquals(1,count($comp->m_actions));
		$this->assertEquals(2,count($comp->m_headers));
		$headers=array_keys($comp->m_headers);
		$this->assertEquals("description",$headers[0]);
		$this->assertEquals("actions",$headers[1]);

		$this->assertEquals(9,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals(105,$keys[5]);
		$this->assertEquals("acceder au menu de l applis",utf8_decode($comp->m_records[$keys[0]]["description"]));
		$this->assertEquals("Ajout/modification d un utilisateur",utf8_decode($comp->m_records[$keys[1]]["description"]));
		$this->assertEquals("Ajout/modification d un groupe",utf8_decode($comp->m_records[$keys[2]]["description"]));
		$this->assertEquals("modification des paramètres généraux",utf8_decode($comp->m_records[$keys[3]]["description"]));
		$this->assertEquals("activation/desactivation des extensions",utf8_decode($comp->m_records[$keys[4]]["description"]));
		$this->assertEquals("changer de mot de passe",utf8_decode($comp->m_records[$keys[5]]["description"]));
		$this->assertEquals("Impression",utf8_decode($comp->m_records[$keys[6]]["description"]));
		$this->assertEquals("Gestion des autorisation d`acces réseau",utf8_decode($comp->m_records[$keys[7]]["description"]));
		$this->assertEquals("Consultation de session de connexion",utf8_decode($comp->m_records[$keys[8]]["description"]));

		$this->assertEquals(new Xfer_Action("Editer les droits", "lister.gif", "CORE", "extension_rights_APAS_editer","1","0","0"),$comp->m_actions[0]);
	}

	function testEditerDroit() 
	{
		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$this->assertEquals(1,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
		$this->assertEquals(2,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);
		$this->assertEquals("{[underline]}{[bold]}Droits {[italc]}changer de mot de passe{[/italc]} de l'extension {[italc]}CORE{[/italc]}:{[/bold]}{[/underline]}{[newline]}",$comp->m_value);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("groupright",$comp->m_name);
		$this->assertEquals(1,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Changer le droit", "lister.gif", "CORE", "group_rights_APAS_modify","0","0","0"),$comp->m_actions[0]);
		$this->assertEquals(2,count($comp->m_headers));
		$headers=array_keys($comp->m_headers);
		$this->assertEquals("groupref",$headers[0]);
		$this->assertEquals("value",$headers[1]);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals(127,$keys[2]);
		$this->assertEquals(129,$keys[4]);
		$this->assertEquals(0,$keys[5]);
		$this->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
		$this->assertEquals("Membre du bureau",$comp->m_records[$keys[1]]['groupref']);
		$this->assertEquals("Membre du conseil",$comp->m_records[$keys[2]]['groupref']);
		$this->assertEquals("Membre",$comp->m_records[$keys[3]]['groupref']);
		$this->assertEquals("Secretaire",$comp->m_records[$keys[4]]['groupref']);
		$this->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[5]]['groupref']);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
	}

	function testModifierUnDroit() 
	{
		$rep=$this->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"127"),"Xfer_Container_Acknowledge");
		
		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Non",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");

		$rep=$this->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"127"),"Xfer_Container_Acknowledge");
		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
	}

	function testModifierTousDroit() 
	{
		$rep=$this->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"0"),"Xfer_Container_Acknowledge");
		
		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Oui",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");

		$rep=$this->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"0"),"Xfer_Container_Acknowledge");
		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Oui",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");

		$rep=$this->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"129"),"Xfer_Container_Acknowledge");

		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
	}

	function testAjoutSuppressionGroup() 
	{
		// ajout group
		$rep=$this->CallAction("CORE","groups_APAS_ajouter",array("groupId"=>"12","groupName"=>"Truc Muche","weigth"=>"38"),"xfer_container_acknowledge");

		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		$new_key=0;
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals(5,count($comp->m_records));
			$key=array_keys($comp->m_records);
			$new_key=$key[3];
			$this->assertTrue($new_key>=100,"Group #$new_key");
		}

		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(7,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
		$this->assertEquals("Membre du bureau",$comp->m_records[$keys[1]]['groupref']);
		$this->assertEquals("Membre du conseil",$comp->m_records[$keys[2]]['groupref']);
		$this->assertEquals("Membre",$comp->m_records[$keys[3]]['groupref']);
		$this->assertEquals("Secretaire",$comp->m_records[$keys[4]]['groupref']);
		$this->assertEquals("Truc Muche",$comp->m_records[$keys[5]]['groupref']);
		$this->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[6]]['groupref']);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"truc muche");
		$this->assertEquals("Non",$comp->m_records[$keys[6]]['value'],"tous");

		$rep=$this->CallAction("CORE","groups_APAS_supprimer",array("group"=>"".$new_key,"CONFIRME"=>"YES"),"Xfer_Container_Acknowledge");

		$rep=$this->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(6,count($comp->m_records));
		$keys=array_keys($comp->m_records);
		$this->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
		$this->assertEquals("Membre du bureau",$comp->m_records[$keys[1]]['groupref']);
		$this->assertEquals("Membre du conseil",$comp->m_records[$keys[2]]['groupref']);
		$this->assertEquals("Membre",$comp->m_records[$keys[3]]['groupref']);
		$this->assertEquals("Secretaire",$comp->m_records[$keys[4]]['groupref']);
		$this->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[5]]['groupref']);
		$this->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
		$this->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
		$this->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
		$this->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
		$this->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
		$this->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
	}
//@END@
}

?>