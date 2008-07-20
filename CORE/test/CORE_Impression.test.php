<?php
// test file write by SDK tool
// --- Date 24 February 2006 22:54:46 By Laurent GAY ---

require_once('CORE/ApasUnit.inc.php');

class APASUnit_CORE_Impression extends APASUnit_TestCase
{
//@BEGIN@
	function testModelList()
	{
		$rep=$this->CallAction("CORE","printmodel_APAS_list",array(),"Xfer_Container_Custom");
		$this->assertEquals(1,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Fermer", "cancel.png","","","1","1"),$rep->m_actions[0]);
		$this->assertEquals(1,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("print_model",$comp->m_name);
		$this->assertEquals(3,count($comp->m_headers));
		$this->assertEquals(2,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Editer", "", "CORE", "printmodel_APAS_edit","1","0","0"),$comp->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Réinitialiser", "", "CORE", "printmodel_APAS_reinit","1","0","0"),$comp->m_actions[1]);
	}

	function testModelModify()
	{
		$rep=$this->CallAction("CORE","printmodel_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(0);
		$records=array_keys($comp->m_records);
		$this->assertTrue(count($records)>0);
		$impress_id=$records[0];

		$title="";
		$model="";
		$model_id="";
		$rep=$this->CallAction("CORE","printmodel_APAS_edit",array("print_model"=>$impress_id),"Xfer_Container_Template");
		if ($rep!=null)
		{
			$this->assertTrue($rep->title!="");
			$this->assertTrue($rep->m_xml_data!="");
			$this->assertTrue($rep->m_model!="");
			$this->assertTrue($rep->m_model_id!="");
			$title=$rep->title;
			$model=$rep->m_model;
			$model_id=$rep->m_model_id;
		}

		$rep=$this->CallAction("CORE","printmodel_APAS_edit",array("print_model"=>$impress_id,"model"=>$model."<TRUC/>","titre"=>"$$".$title."##"),"xfer_container_acknowledge");

		$rep=$this->CallAction("CORE","printmodel_APAS_edit",array("print_model"=>$impress_id),"Xfer_Container_Template");
		if ($rep!=null)
		{
			$this->assertEquals("$$".$title."##",$rep->title);
			$this->assertEquals($model."<TRUC/>",$rep->m_model);
			$this->assertEquals($model_id,$rep->m_model_id);
		}

		$rep=$this->CallAction("CORE","printmodel_APAS_reinit",array("print_model"=>$impress_id),"xfer_container_dialogbox");
		if ($rep!=null)
		{
			$this->assertEquals(2,$rep->m_type);
			$this->assertEquals("Etes-vous sûre de réinitialiser ce model?",utf8_decode($rep->m_text));
		}
		$rep=$this->CallAction("CORE","printmodel_APAS_reinit",array("CONFIRME"=>"YES","print_model"=>$impress_id),"xfer_container_acknowledge");

		$rep=$this->CallAction("CORE","printmodel_APAS_edit",array("print_model"=>$impress_id),"Xfer_Container_Template");
		if ($rep!=null)
		{
			$this->assertEquals("$$".$title."##",$rep->title);
			$this->assertEquals($model,$rep->m_model);
			$this->assertEquals($model_id,$rep->m_model_id);
		}
	}

//@END@
}
?>
