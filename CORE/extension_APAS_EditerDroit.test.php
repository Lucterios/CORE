<?php

function CORE_extension_APAS_EditerDroit($test) 
{
//@CODE_ACTION@
	$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
	$test->assertEquals(1,COUNT($rep->m_actions));
	$test->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
	$test->assertEquals(2,$rep->getComponentCount());

	$comp=$rep->getComponents(0);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("Comp2",$comp->m_name);
	$test->assertEquals("{[underline]}{[bold]}Droits {[italc]}changer de mot de passe{[/italc]} de l'extension {[italc]}CORE{[/italc]}:{[/bold]}{[/underline]}{[newline]}",$comp->m_value);

	$comp=$rep->getComponents(1);
	$test->assertClass("Xfer_Comp_Grid",$comp);
	$test->assertEquals("groupright",$comp->m_name);
	$test->assertEquals(1,count($comp->m_actions));
	$test->assertEquals(new Xfer_Action("Changer le droit", "lister.gif", "CORE", "group_rights_APAS_modify","0","0","0"),$comp->m_actions[0]);
	$test->assertEquals(2,count($comp->m_headers));
	$headers=array_keys($comp->m_headers);
	$test->assertEquals("groupref",$headers[0]);
	$test->assertEquals("value",$headers[1]);
	$test->assertEquals(6,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals(127,$keys[2]);
	$test->assertEquals(129,$keys[4]);
	$test->assertEquals(0,$keys[5]);
	$test->assertEquals("Admin",$comp->m_records[$keys[0]]['groupref']);
	$test->assertEquals("Membre du bureau",$comp->m_records[$keys[1]]['groupref']);
	$test->assertEquals("Membre du conseil",$comp->m_records[$keys[2]]['groupref']);
	$test->assertEquals("Membre",$comp->m_records[$keys[3]]['groupref']);
	$test->assertEquals("Secretaire",$comp->m_records[$keys[4]]['groupref']);
	$test->assertEquals("{[italc]}Tous les groupes{[/italc]}",$comp->m_records[$keys[5]]['groupref']);
	$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
	$test->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
	$test->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
	$test->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
	$test->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
	$test->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
//@CODE_ACTION@
}

?>