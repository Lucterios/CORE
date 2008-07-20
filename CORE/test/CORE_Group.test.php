<?php
// test file write by SDK tool
// --- Date 09 July 2006 21:55:40 By Laurent GAY ---

require_once('CORE/ApasUnit.inc.php');

class APASUnit_CORE_Group extends APASUnit_TestCase
{
//@BEGIN@
	function testGroupList()
	{
		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Ajouter", "ok.png", "CORE", "groups_APAS_modifier","1","0",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Fermer", "cancel.png","","","1","1"),$rep->m_actions[1]);

		$this->assertEquals(3,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);

		$comp=$rep->getComponents(2);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);

		$this->assertEquals(2,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Modifier", "lister.gif", "CORE", "groups_APAS_modifier","1","0","0"),$comp->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Supprimer", "suppr.png", "CORE", "groups_APAS_supprimer","1","0","0"),$comp->m_actions[1]);

		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("group",$comp->m_name);
		$this->assertEquals(3,count($comp->m_headers));
		$this->assertEquals(4,count($comp->m_records));
		$key=array_keys($comp->m_records);
		$this->assertEquals('2',$key[0]);
		$this->assertEquals('3',$key[1]);
		$this->assertEquals('4',$key[2]);
		$this->assertEquals('5',$key[3]);
		$this->assertEquals(3,count($comp->m_records[$key[0]]));
		$this->assertEquals("2",$comp->m_records[$key[0]]['groupId']);
		$this->assertEquals("Membre du bureau",$comp->m_records[$key[0]]['groupName']);
		$this->assertEquals("80",$comp->m_records[$key[0]]['weigth']);
		$this->assertEquals("3",$comp->m_records[$key[1]]['groupId']);
		$this->assertEquals("Membre du conseil",$comp->m_records[$key[1]]['groupName']);
		$this->assertEquals("60",$comp->m_records[$key[1]]['weigth']);
		$this->assertEquals("4",$comp->m_records[$key[2]]['groupId']);
		$this->assertEquals("Membre",$comp->m_records[$key[2]]['groupName']);
		$this->assertEquals("40",$comp->m_records[$key[2]]['weigth']);
		$this->assertEquals("5",$comp->m_records[$key[3]]['groupId']);
		$this->assertEquals("Secretaire",$comp->m_records[$key[3]]['groupName']);
		$this->assertEquals("20",$comp->m_records[$key[3]]['weigth']);
	}
	function testGroupAjoutModifSuppression()
	{
		// Verifier list
		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals(4,count($comp->m_records));
		}

		// Ajouter
		$rep=$this->CallAction("CORE","groups_APAS_modifier",array(),"Xfer_Container_Custom");
		if ($rep!=null)
		{
			$this->assertEquals(2,COUNT($rep->m_actions));
			$this->assertEquals(new Xfer_Action("OK", "ok.png", "CORE", "groups_APAS_ajouter","1","1",""),$rep->m_actions[0]);
			$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);

			$this->assertEquals(6,$rep->getComponentCount());
			$comp=$rep->getComponents(0);
			$this->assertClass("Xfer_Comp_LabelForm",$comp);
			$comp=$rep->getComponents(2);
			$this->assertClass("Xfer_Comp_LabelForm",$comp);
			$comp=$rep->getComponents(4);
			$this->assertClass("Xfer_Comp_LabelForm",$comp);

			$comp=$rep->getComponents(1);
			$this->assertClass("Xfer_Comp_Float",$comp);
			$this->assertEquals("groupId",$comp->m_name);
			$this->assertEquals("0",$comp->m_min);
			$this->assertEquals("999",$comp->m_max);
			$this->assertEquals("0",$comp->m_prec);
			$comp=$rep->getComponents(3);
			$this->assertClass("Xfer_Comp_Edit",$comp);
			$this->assertEquals("groupName",$comp->m_name);
			$comp=$rep->getComponents(5);
			$this->assertClass("Xfer_Comp_Float",$comp);
			$this->assertEquals("weigth",$comp->m_name);
			$this->assertEquals("0",$comp->m_min);
			$this->assertEquals("100",$comp->m_max);
			$this->assertEquals("0",$comp->m_prec);
		}

		// Valider ajout
		$rep=$this->CallAction("CORE","groups_APAS_ajouter",array("groupId"=>"12","groupName"=>"Truc Muche","weigth"=>"38"),"xfer_container_acknowledge");

		// Reverifier list
		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		$new_key=0;
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals(5,count($comp->m_records));
			$key=array_keys($comp->m_records);
			$this->assertEquals('2',$key[0],"A");
			$this->assertEquals('3',$key[1],"B");
			$this->assertEquals('4',$key[2],"C");
			$this->assertEquals('101',$key[3],"D");
			$this->assertEquals('5',$key[4],"E");
			$new_key=$key[3];
			$this->assertEquals('101',$new_key,"D+");
			$this->assertEquals("12",$comp->m_records[$new_key]['groupId']);
			$this->assertEquals("Truc Muche",$comp->m_records[$new_key]['groupName']);
			$this->assertEquals("38",$comp->m_records[$new_key]['weigth']);
		}

		// Modifier
		$rep=$this->CallAction("CORE","groups_APAS_ajouter",array("group"=>$new_key, "groupId"=>"13","groupName"=>"Truc Muche Bidon","weigth"=>"83"),"xfer_container_acknowledge");

		// Rereverifier list
		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals(5,count($comp->m_records));
			$this->assertEquals('101',$new_key);
			$this->assertEquals("13",$comp->m_records[$new_key]['groupId']);
			$this->assertEquals("Truc Muche Bidon",$comp->m_records[$new_key]['groupName']);
			$this->assertEquals("83",$comp->m_records[$new_key]['weigth']);
		}

		// supprimer
		$rep=$this->CallAction("CORE","groups_APAS_supprimer",array("group"=>$new_key),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(2,$rep->m_type);
			$this->assertEquals("Etes-vous sûre de vouloir supprimer le groupe 'Truc Muche Bidon'?",utf8_decode($rep->m_text));
		}
		$this->assertEquals(array("group"=>"".$new_key,"CONFIRME"=>"YES"),$rep->m_context);
		$rep=$this->CallAction("CORE","groups_APAS_supprimer",$rep->m_context,"Xfer_Container_Acknowledge");

		// Recontrole Access
		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals(4,count($comp->m_records));
			$key=array_keys($comp->m_records);
			$this->assertEquals('2',$key[0],"a");
			$this->assertEquals('3',$key[1],"b");
			$this->assertEquals('4',$key[2],"c");
			$this->assertEquals('5',$key[3],"d");
		}
	}

//@END@
}
?>
