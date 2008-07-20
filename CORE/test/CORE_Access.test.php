<?php
// test file write by SDK tool
// --- Date 09 July 2006 21:55:29 By Laurent GAY ---

require_once('CORE/ApasUnit.inc.php');

class APASUnit_CORE_Access extends APASUnit_TestCase
{
//@BEGIN@
	function testAccessList()
	{
		$rep=$this->CallAction("CORE","access_APAS_list",array(),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Ajouter", "ok.png", "CORE", "access_APAS_ajouter","1","0",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Fermer", "ok.png","","","1","1"),$rep->m_actions[1]);

		$this->assertEquals(1,$rep->getComponentCount());
		$comp=$rep->getComponents(0);

		$this->assertEquals(2,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Modifier", "lister.gif", "CORE", "access_APAS_ajouter","1","0","0"),$comp->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Supprimer", "suppr.png", "CORE", "access_APAS_supprimer","1","0","0"),$comp->m_actions[1]);

		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("access",$comp->m_name);
		$this->assertEquals(1,count($comp->m_headers));
		$this->assertEquals(1,count($comp->m_records));
		$key=array_keys($comp->m_records);
		$this->assertEquals('1',$key[0]);
		$this->assertEquals(1,count($comp->m_records[$key[0]]));
		$this->assertEquals("255.0.0.0/8",$comp->m_records[$key[0]]['inetAddr']);
	}

	function testAccessModifier()
	{
		// List Access
		$rep=$this->CallAction("CORE","access_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(0);
		$key=array_keys($comp->m_records);
		$this->assertEquals("255.0.0.0/8",$comp->m_records[$key[0]]['inetAddr']);

		// Editer inet address
		$rep=$this->CallAction("CORE","access_APAS_ajouter",array('access'=>'1'),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("OK", "ok.png", "CORE", "access_APAS_valider","1","1",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);

		$this->assertEquals(2,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labelinetAddr",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("inetAddr",$comp->m_name);
		$this->assertEquals("255.0.0.0/8",$comp->m_value);

		// Modifier inet address
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>$key[0],'inetAddr'=>"127.0.0.0/8"), "Xfer_Container_Acknowledge");

		// Controle Access
		$rep=$this->CallAction("CORE","access_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(0);
		$this->assertEquals("127.0.0.0/8",$comp->m_records[$key[0]]['inetAddr']);

		// Remodifier inet address
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>$key[0],'inetAddr'=>"255.0.0.0/8"), "Xfer_Container_Acknowledge");
	}

	function testAccessModifierFailed()
	{
		// modifier inet address 1
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"abcdef/32"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau 'abcdef/32' invalide! classes=1 [abcdef]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"A");

		// modifier inet address 2 ---
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"300.400.500.600/16"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '300.400.500.600/16' invalide! classes=1 [300,400,500,600]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"B");

		// modifier inet address 3
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255.0.0/8"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '255.0.0/8' invalide! classes=3 [255,0,0]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"C");

		// modifier inet address 4
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255.0/8"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '255.0/8' invalide! classes=2 [255,0]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"D");

		// modifier inet address 5
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255/8"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '255/8' invalide! classes=1 [255]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"E");

		// modifier inet address 6
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"/8"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '/8' invalide! classes=0 []",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"F");

		// modifier inet address 7
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>""), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '' invalide! classes=0 []",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"G");

		// modifier inet address 8 ---
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255.0.0.0/84"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '255.0.0.0/84' invalide! masque='84'",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"H");

		// modifier inet address 9 ---
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"0.0.0.0/16"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '0.0.0.0/16' invalide! classes=1 [0,0,0,0]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"I");

		// modifier inet address 10
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255.0.0.0/0"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '255.0.0.0/0' invalide! masque='0'",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"J");

		// modifier inet address 11
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255.0.0.0/aze"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau '255.0.0.0/aze' invalide! masque='aze'",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"K");

		// modifier inet address 12
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"n,;jk"), "Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Acces réseau 'n,;jk' invalide! classes=1 [n,;jk]",utf8_decode($rep->m_text));
		}
		else $this->assertTrue(false,"L");

		// modifier inet address 0 - Success
		$rep=$this->CallAction("CORE","access_APAS_valider", array('access'=>'1','inetAddr'=>"255.0.0.0/8"), "Xfer_Container_Acknowledge");
	}

	function testAccessAjouterSupprimer()
	{
		// List Access
		$rep=$this->CallAction("CORE","access_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(0);
		$this->assertEquals(1,count($comp->m_records));
		$key=array_keys($comp->m_records);
		$this->assertEquals("255.0.0.0/8",$comp->m_records[$key[0]]['inetAddr']);

		// Ajouter inet address
		$rep=$this->CallAction("CORE","access_APAS_ajouter",array(),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("OK", "ok.png", "CORE", "access_APAS_valider","1","1",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);

		$this->assertEquals(2,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labelinetAddr",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("inetAddr",$comp->m_name);
		$this->assertEquals("",$comp->m_value);

		// Modifier inet address
		$rep=$this->CallAction("CORE","access_APAS_valider", array('inetAddr'=>"127.0.0.0/8"), "Xfer_Container_Acknowledge");

		// Controle Access
		$rep=$this->CallAction("CORE","access_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(0);
		$this->assertEquals(2,count($comp->m_records));
		$key=array_keys($comp->m_records);
		$this->assertEquals("255.0.0.0/8",$comp->m_records[$key[0]]['inetAddr']);
		$this->assertEquals("127.0.0.0/8",$comp->m_records[$key[1]]['inetAddr']);

		// Suppression
		$rep=$this->CallAction("CORE","access_APAS_supprimer",array('access'=>$key[1]),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(2,$rep->m_type);
			$this->assertEquals("Etes-vous sûre de vouloir supprimer cette acces réseau?",utf8_decode($rep->m_text));
		}
		$this->assertEquals(array('access'=>"".$key[1],"CONFIRME"=>"YES"),$rep->m_context);
		$rep=$this->CallAction("CORE","access_APAS_supprimer",$rep->m_context,"Xfer_Container_Acknowledge");

		// Recontrole Access
		$rep=$this->CallAction("CORE","access_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(0);
		$this->assertEquals(1,count($comp->m_records));
		$this->assertEquals("255.0.0.0/8",$comp->m_records[$key[0]]['inetAddr']);

		// Suppression du dernier -> echec
		$rep=$this->CallAction("CORE","access_APAS_supprimer",array('access'=>'1'),"Xfer_Container_DialogBox");
		$rep=$this->CallAction("CORE","access_APAS_supprimer",$rep->m_context,"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Suppression impossible.",utf8_decode($rep->m_text));
		}
		else  // supression effectuer malgrès tout
		{
			$this->CallAction("CORE","access_APAS_valider", array('inetAddr'=>"255.0.0.0/8"), "Xfer_Container_Acknowledge");
		}
	}

//@END@
}
?>
