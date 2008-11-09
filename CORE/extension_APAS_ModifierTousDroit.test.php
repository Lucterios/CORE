<?php

function CORE_extension_APAS_ModifierTousDroit($test) 
{
//@CODE_ACTION@
	$rep=$test->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"0"),"Xfer_Container_Acknowledge");
	
	$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
	$comp=$rep->getComponents(1);
	$test->assertEquals(6,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
	$test->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
	$test->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
	$test->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
	$test->assertEquals("Oui",$comp->m_records[$keys[4]]['value'],"secretaire");
	$test->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");

	$rep=$test->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"0"),"Xfer_Container_Acknowledge");
	$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
	$comp=$rep->getComponents(1);
	$test->assertEquals(6,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
	$test->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
	$test->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
	$test->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
	$test->assertEquals("Oui",$comp->m_records[$keys[4]]['value'],"secretaire");
	$test->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");

	$rep=$test->CallAction("CORE","group_rights_APAS_modify",array("right"=>"105","groupright"=>"129"),"Xfer_Container_Acknowledge");

	$rep=$test->CallAction("CORE","extension_rights_APAS_editer",array("right"=>"105"),"Xfer_Container_Custom");
	$comp=$rep->getComponents(1);
	$test->assertEquals(6,count($comp->m_records));
	$keys=array_keys($comp->m_records);
	$test->assertEquals("Oui",$comp->m_records[$keys[0]]['value'],"Admin");
	$test->assertEquals("Oui",$comp->m_records[$keys[1]]['value'],"burean");
	$test->assertEquals("Oui",$comp->m_records[$keys[2]]['value'],"conseil");
	$test->assertEquals("Oui",$comp->m_records[$keys[3]]['value'],"membre");
	$test->assertEquals("Non",$comp->m_records[$keys[4]]['value'],"secretaire");
	$test->assertEquals("Non",$comp->m_records[$keys[5]]['value'],"tous");
//@CODE_ACTION@
}

?>