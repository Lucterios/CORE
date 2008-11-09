<?php

function CORE_extension_APAS_DroitAction($test) 
{
//@CODE_ACTION@
	$rep=$test->CallAction("CORE","extension_APAS_listactions",array("extension"=>'100'),"Xfer_Container_Custom");
	$test->assertEquals(1,COUNT($rep->m_actions));
	$test->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
	$test->assertEquals(2,$rep->getComponentCount());

	$comp=$rep->getComponents(0);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("Comp2",$comp->m_name);

	$comp=$rep->getComponents(1);
	$test->assertClass("Xfer_Comp_Grid",$comp);
	$test->assertEquals("right",$comp->m_name);
	$test->assertEquals(1,count($comp->m_actions));
	$test->assertEquals(2,count($comp->m_headers));
	$headers=array_keys($comp->m_headers);
	$test->assertEquals("description",$headers[0]);
	$test->assertEquals("actions",$headers[1]);

	$test->assertEquals(9,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals(105,$keys[5]);
	$test->assertEquals("acceder au menu de l applis",utf8_decode($comp->m_records[$keys[0]]["description"]));
	$test->assertEquals("Ajout/modification d un utilisateur",utf8_decode($comp->m_records[$keys[1]]["description"]));
	$test->assertEquals("Ajout/modification d un groupe",utf8_decode($comp->m_records[$keys[2]]["description"]));
	$test->assertEquals("modification des paramètres généraux",utf8_decode($comp->m_records[$keys[3]]["description"]));
	$test->assertEquals("activation/desactivation des extensions",utf8_decode($comp->m_records[$keys[4]]["description"]));
	$test->assertEquals("changer de mot de passe",utf8_decode($comp->m_records[$keys[5]]["description"]));
	$test->assertEquals("Impression",utf8_decode($comp->m_records[$keys[6]]["description"]));
	$test->assertEquals("Gestion des autorisation d`acces réseau",utf8_decode($comp->m_records[$keys[7]]["description"]));
	$test->assertEquals("Consultation de session de connexion",utf8_decode($comp->m_records[$keys[8]]["description"]));

	$test->assertEquals(new Xfer_Action("Editer les droits", "lister.gif", "CORE", "extension_rights_APAS_editer","1","0","0"),$comp->m_actions[0]);
//@CODE_ACTION@
}

?>