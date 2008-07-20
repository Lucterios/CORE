<?php

require_once("CORE/ApasUnit.inc.php");

class APASUnit_CORE_Session extends APASUnit_TestCase
{
//@BEGIN@
	function testSessionList() 
	{
		$rep=$this->CallAction("CORE","sessions_APAS_list",array(),"Xfer_Container_Custom");
		$this->assertEquals(1,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Fermer", "ok.png", "", "","1","1",""),$rep->m_actions[0]);
		$this->assertEquals(5,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("titre",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("access",$comp->m_name);
		$this->assertEquals(4,count($comp->m_headers));
		$this->assertEquals(0,count($comp->m_actions));

		$comp=$rep->getComponents(2);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("titre",$comp->m_name);

		$comp=$rep->getComponents(3);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("titre",$comp->m_name);

		$comp=$rep->getComponents(4);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("access",$comp->m_name);
		$this->assertEquals(4,count($comp->m_headers));
		$this->assertEquals(0,count($comp->m_actions));
	}
//@END@
}

?>