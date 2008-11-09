<?php

function CORE_extension_APAS_AjoutSuppressionGroup($test) 
{
//@CODE_ACTION@
	// ajout group
	$rep=$test->CallAction("CORE","groups_APAS_ajouter",array("groupId"=>"12","groupName"=>"Truc Muche","weigth"=>"38"),"xfer_container_acknowledge");

	$rep=$test->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
	$comp=$rep->getComponents(1);
	$test->assertEquals(2,count($comp->m_records));
	$key=array_keys($comp->m_records);
	$test->assertEquals(100,$key[0],"Group 'Admin'");
	$test->assertEquals(101,$key[1],"Group 'Truc Muche'");

	$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
	$comp=$rep->getComponents(1);
	$test->assertEquals(3,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
	$test->assertEquals("Truc Muche",$comp->m_records[$keys[5]]['groupref']);
	$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[6]]['groupref']);
	$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
	$test->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"truc muche");
	$test->assertEquals("Non",$comp->m_records[$keys[6]]['value'],"tous");

	$rep=$test->CallAction("CORE","groups_APAS_supprimer",array("group"=>101,"CONFIRME"=>"YES"),"Xfer_Container_Acknowledge");

	$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
	$comp=$rep->getComponents(1);
	$test->assertEquals(2,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
	$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[5]]['groupref']);
	$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
	$test->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
//@CODE_ACTION@
}

?>