<?php

function CORE_extension_APAS_List($test) 
{
//@CODE_ACTION@
	$rep=$test->CallAction("CORE","extension_APAS_list",array(),"Xfer_Container_Custom");
	$test->assertEquals(1,COUNT($rep->m_actions));
	$test->assertEquals(new Xfer_Action("Fermer","ok.png"),$rep->m_actions[0]);
	$test->assertEquals(2,$rep->getComponentCount());

	$comp=$rep->getComponents(0);
	$test->assertClass("Xfer_Comp_LabelForm",$comp);
	$test->assertEquals("Comp2",$comp->m_name);

	$comp=$rep->getComponents(1);
	$test->assertClass("Xfer_Comp_Grid",$comp);
	$test->assertEquals("extension",$comp->m_name);
	$test->assertEquals(4,count($comp->m_headers));
	$test->assertEquals(1,count($comp->m_actions));
	$test->assertEquals(new Xfer_Action("Droits et Actions", "lister.gif", "CORE", "extension_APAS_listactions","1","0","0"),$comp->m_actions[0]);
	$headers=array_keys($comp->m_headers);
	$test->assertEquals("extensionId",$headers[0]);
	$test->assertEquals("description",$headers[1]);
	$test->assertEquals('Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild',$headers[2]);
	$test->assertEquals("validite",$headers[3]);

	$test->assertEquals("CORE",$comp->m_records["100"]["extensionId"]);
	$test->assertEquals("Noyau APAS",$comp->m_records["100"]["description"]);
	$test->assertEquals("0.3",substr($comp->m_records["100"]['Version#|#$versionMaj.$versionMin.$versionRev.$versionBuild'],0,3));
	$test->assertEquals("Oui",$comp->m_records["100"]["validite"]);
//@CODE_ACTION@
}

?>