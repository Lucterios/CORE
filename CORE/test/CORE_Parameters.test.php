<?php
// test file write by SDK tool
// --- Date 08 March 2006 15:10:39 By Laurent GAY ---

require_once('CORE/ApasUnit.inc.php');

class APASUnit_CORE_Parameters extends APASUnit_TestCase
{
//@BEGIN@
	function testParameterList()
	{
		$rep=$this->CallAction("CORE","extension_params_APAS_list",array(),"Xfer_Container_Custom");
		$this->assertEquals(1,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("OK", "ok.png","","","1","1"),$rep->m_actions[0]);

		$this->assertEquals(2,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);

		$this->assertEquals(1,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Modifier", "lister.gif", "CORE", "extension_params_APAS_modifier","1","0","0"),$comp->m_actions[0]);

		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("paramid",$comp->m_name);
		$this->assertEquals(6,count($comp->m_headers));
	}

	function testParameterModifier()
	{
		$rep=$this->CallAction("CORE","extension_params_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$key=array_keys($comp->m_records);
		$first_key=$key[0];

		$rep=$this->CallAction("CORE","extension_params_APAS_modifier",array("paramid"=>$first_key),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("OK", "ok.png", "CORE", "extension_params_APAS_miseajour","0","1",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);

		$this->assertEquals(10,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$comp=$rep->getComponents(2);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$comp=$rep->getComponents(4);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$comp=$rep->getComponents(6);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$comp=$rep->getComponents(8);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Label",$comp);
		$comp=$rep->getComponents(3);
		$this->assertClass("Xfer_Comp_Label",$comp);
		$comp=$rep->getComponents(5);
		$this->assertClass("Xfer_Comp_Label",$comp);
		$comp=$rep->getComponents(7);
		$this->assertClass("Xfer_Comp_Label",$comp);
		$comp=$rep->getComponents(9);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("value",$comp->m_name);
		$old_value=$comp->m_value;

		$rep=$this->CallAction("CORE","extension_params_APAS_miseajour",array("paramid"=>$first_key,"value"=>"abc def"),"xfer_container_acknowledge");

		$rep=$this->CallAction("CORE","extension_params_APAS_list",array(),"Xfer_Container_Custom");
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals("abc def",$comp->m_records[$first_key]["value"]);
		}

		$rep=$this->CallAction("CORE","extension_params_APAS_miseajour",array("paramid"=>$first_key,"value"=>$old_value),"xfer_container_acknowledge");

		$rep=$this->CallAction("CORE","extension_params_APAS_list",array(),"Xfer_Container_Custom");
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals($old_value,$comp->m_records[$first_key]["value"]);
		}
	}

//@END@
}
?>
