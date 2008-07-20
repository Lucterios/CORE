<?php
// test file write by SDK tool
// --- Date 09 July 2006 21:55:54 By Laurent GAY ---

require_once('CORE/ApasUnit.inc.php');

class APASUnit_CORE_Utilisateurs extends APASUnit_TestCase
{
//@BEGIN@
	function setUp()
	{
		APASUnit_TestCase::setUp();
		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$grid_actif=$rep->getComponents(1);
		if (count($grid_actif->m_records)==1)
		{
			$rep=$this->CallAction("CORE","users_APAS_miseajour",array("login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_Acknowledge");
		}
		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$grid_actif=$rep->getComponents(1);
		$grid_desactif=$rep->getComponents(4);
		$this->assertEquals(2,count($grid_actif->m_records));
		$this->assertEquals(0,count($grid_desactif->m_records));
	}

	function testUtilisateurList()
	{
		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Ajouter un utilisateur", "ok.png", "CORE", "users_APAS_ajouter","1","0",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Fermer", "cancel.png","","","1","1"),$rep->m_actions[1]);
		$this->assertEquals(5,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("user_actif",$comp->m_name);
		$this->assertEquals(3,count($comp->m_headers));
		$this->assertEquals(2,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Modifier", "lister.gif", "CORE", "users_APAS_modifier","1","0","0"),$comp->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Désactiver", "suppr.png", "CORE", "users_APAS_desactiver","1","0","0"),$comp->m_actions[1]);
		$this->assertEquals(3,count($comp->m_headers));
		$headers=array_keys($comp->m_headers);
		$this->assertEquals("login",$headers[0]);
		$this->assertEquals("realName",$headers[1]);
		$this->assertEquals("groupId",$headers[2]);
		$this->assertEquals(2,count($comp->m_records));
		$this->assertEquals("admin",$comp->m_records["100"]["login"]);
		$this->assertEquals("Administrateur",$comp->m_records["100"]["realName"]);
		$this->assertEquals("Admin",$comp->m_records["100"]["groupId"]);
		$this->assertEquals("abc",$comp->m_records["101"]["login"]);
		$this->assertEquals("abc",$comp->m_records["101"]["realName"]);
		$this->assertEquals("Admin",$comp->m_records["101"]["groupId"]);

		$comp=$rep->getComponents(2);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("separator",$comp->m_name);

		$comp=$rep->getComponents(3);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);

		$comp=$rep->getComponents(4);
		$this->assertClass("Xfer_Comp_Grid",$comp);
		$this->assertEquals("user_desactif",$comp->m_name);
		$this->assertEquals(3,count($comp->m_headers));
		$this->assertEquals(1,count($comp->m_actions));
		$this->assertEquals(new Xfer_Action("Réactiver", "ok.png", "CORE", "users_APAS_reactiver","1","0","0"),$comp->m_actions[0]);
		$this->assertEquals(3,count($comp->m_headers));
		$headers=array_keys($comp->m_headers);
		$this->assertEquals("login",$headers[0]);
		$this->assertEquals("realName",$headers[1]);
		$this->assertEquals("groupId",$headers[2]);
		$this->assertEquals(0,count($comp->m_records));
	}

	function testUtilisateurActivation()
	{
		$rep=$this->CallAction("CORE","users_APAS_desactiver",array("user_actif"=>"101"),"Xfer_Container_Acknowledge");

		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(1,count($comp->m_records));
		$this->assertEquals("admin",$comp->m_records["100"]["login"]);

		$comp=$rep->getComponents(4);
		$this->assertEquals(1,count($comp->m_records));
		$this->assertEquals("abc",$comp->m_records["101"]["login"]);

		$rep=$this->CallAction("CORE","users_APAS_reactiver",array("user_desactif"=>"101"),"Xfer_Container_Acknowledge");

		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals(2,count($comp->m_records));
		$this->assertEquals("admin",$comp->m_records["100"]["login"]);
		$this->assertEquals("abc",$comp->m_records["101"]["login"]);

		$comp=$rep->getComponents(4);
		$this->assertEquals(0,count($comp->m_records));
	}

	function testUtilisateurEdition()
	{
		$rep=$this->CallAction("CORE","users_APAS_modifier",array("user_actif"=>"101"),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("OK", "ok.png", "CORE", "users_APAS_miseajour","1","1",""),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);
		$this->assertEquals(11,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labellogin",$comp->m_name);
		$comp=$rep->getComponents(3);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labelrealName",$comp->m_name);
		$comp=$rep->getComponents(5);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labelgroupId",$comp->m_name);
		$comp=$rep->getComponents(7);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("lab",$comp->m_name);
		$comp=$rep->getComponents(9);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("lab",$comp->m_name);

		$comp=$rep->getComponents(2);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("login",$comp->m_name);
		$this->assertEquals("abc",$comp->m_value);
		$comp=$rep->getComponents(4);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("realName",$comp->m_name);
		$this->assertEquals("abc",$comp->m_value);
		$comp=$rep->getComponents(6);
		$this->assertClass("Xfer_Comp_Select",$comp);
		$this->assertEquals("groupId",$comp->m_name);
		$this->assertEquals("1",$comp->m_value);
		$this->assertEquals(5,count($comp->m_select));
		$this->assertEquals("Admin",$comp->m_select[1]);
		$this->assertEquals("Membre du bureau",$comp->m_select[2]);
		$this->assertEquals("Membre du conseil",$comp->m_select[3]);
		$this->assertEquals("Membre",$comp->m_select[4]);
		$this->assertEquals("Secretaire",$comp->m_select[5]);
		$comp=$rep->getComponents(8);
		$this->assertClass("Xfer_Comp_Passwd",$comp);
		$this->assertEquals("newpass1",$comp->m_name);
		$this->assertEquals("",$comp->m_value);
		$comp=$rep->getComponents(10);
		$this->assertClass("Xfer_Comp_Passwd",$comp);
		$this->assertEquals("newpass2",$comp->m_name);
		$this->assertEquals("",$comp->m_value);
	}

	function testUtilisateurAjout()
	{
		$rep=$this->CallAction("CORE","users_APAS_ajouter",array(),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("OK", "ok.png", "CORE", "users_APAS_miseajour","1","1"),$rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);
		$this->assertEquals(11,$rep->getComponentCount());

		$comp=$rep->getComponents(0);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("Comp2",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labellogin",$comp->m_name);
		$comp=$rep->getComponents(3);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labelrealName",$comp->m_name);
		$comp=$rep->getComponents(5);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("labelgroupId",$comp->m_name);
		$comp=$rep->getComponents(7);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("lab",$comp->m_name);
		$comp=$rep->getComponents(9);
		$this->assertClass("Xfer_Comp_LabelForm",$comp);
		$this->assertEquals("lab",$comp->m_name);

		$comp=$rep->getComponents(2);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("login",$comp->m_name);
		$this->assertEquals("",$comp->m_value);
		$comp=$rep->getComponents(4);
		$this->assertClass("Xfer_Comp_Edit",$comp);
		$this->assertEquals("realName",$comp->m_name);
		$this->assertEquals("",$comp->m_value);
		$comp=$rep->getComponents(6);
		$this->assertClass("Xfer_Comp_Select",$comp);
		$this->assertEquals("groupId",$comp->m_name);
		$this->assertEquals("0",$comp->m_value);
		$this->assertEquals(5,count($comp->m_select));
		$this->assertEquals("Admin",$comp->m_select[1]);
		$this->assertEquals("Membre du bureau",$comp->m_select[2]);
		$this->assertEquals("Membre du conseil",$comp->m_select[3]);
		$this->assertEquals("Membre",$comp->m_select[4]);
		$this->assertEquals("Secretaire",$comp->m_select[5]);
		$comp=$rep->getComponents(8);
		$this->assertClass("Xfer_Comp_Passwd",$comp);
		$this->assertEquals("newpass1",$comp->m_name);
		$this->assertEquals("",$comp->m_value);
		$comp=$rep->getComponents(10);
		$this->assertClass("Xfer_Comp_Passwd",$comp);
		$this->assertEquals("newpass2",$comp->m_name);
		$this->assertEquals("",$comp->m_value);

	}

	function testUtilisateurModification()
	{
		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"cbd","realName"=>"efghij","groupId"=>2,"newpass1"=>"xyz","newpass2"=>"xyz"),"Xfer_Container_Acknowledge");
		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals("cbd",$comp->m_records["101"]["login"]);
		$this->assertEquals("efghij",$comp->m_records["101"]["realName"]);
		$this->assertEquals("Membre du bureau",$comp->m_records["101"]["groupId"]);

		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_Acknowledge");
		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals("abc",$comp->m_records["101"]["login"]);
		$this->assertEquals("abc",$comp->m_records["101"]["realName"]);
		$this->assertEquals("Admin",$comp->m_records["101"]["groupId"]);
	}

	function testUtilisateurPassword()
	{
		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"","newpass2"=>""),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Les mots de passe ne sont pas égaux!",utf8_decode($rep->m_text));
		}
		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"xyz","newpass2"=>"xyz0"),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Les mots de passe ne sont pas égaux!",utf8_decode($rep->m_text));
		}
		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"xyz","newpass2"=>"xzy"),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Les mots de passe ne sont pas égaux!",utf8_decode($rep->m_text));
		}
		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_Acknowledge");

		$rep=$this->CallAction("CORE","users_APAS_changerpassword",array("user_actif"=>"101"),"Xfer_Container_Custom");
		$this->assertEquals(2,COUNT($rep->m_actions));
		$this->assertEquals(new Xfer_Action("Ok", "ok.png", "CORE", "users_APAS_confirmpwdmodif","1","1",""), $rep->m_actions[0]);
		$this->assertEquals(new Xfer_Action("Annuler", "cancel.png","","","1","1"),$rep->m_actions[1]);

		$this->assertEquals(6,$rep->getComponentCount());
		$comp=$rep->getComponents(0);
		$this->assertClass("xfer_comp_label",$comp);
		$this->assertEquals("lab",$comp->m_name);
		$comp=$rep->getComponents(2);
		$this->assertClass("xfer_comp_label",$comp);
		$this->assertEquals("lab",$comp->m_name);
		$comp=$rep->getComponents(4);
		$this->assertClass("xfer_comp_label",$comp);
		$this->assertEquals("lab",$comp->m_name);

		$comp=$rep->getComponents(1);
		$this->assertClass("xfer_comp_passwd",$comp);
		$this->assertEquals("oldpass",$comp->m_name);
		$comp=$rep->getComponents(3);
		$this->assertClass("Xfer_Comp_Passwd",$comp);
		$this->assertEquals("newpass1",$comp->m_name);
		$comp=$rep->getComponents(5);
		$this->assertClass("Xfer_Comp_Passwd",$comp);
		$this->assertEquals("newpass2",$comp->m_name);
	}

	function testSuppressionGroup()
	{
		// ajout group
		$rep=$this->CallAction("CORE","groups_APAS_ajouter",array("groupId"=>"12","groupName"=>"Truc Muche","weigth"=>"38"),"xfer_container_acknowledge");

		$rep=$this->CallAction("CORE","groups_APAS_liste",array(),"Xfer_Container_Custom");
		$new_key=0;
		if ($rep!=null)
		{
			$comp=$rep->getComponents(1);
			$this->assertEquals(5,count($comp->m_records));
			$key=array_keys($comp->m_records);
			$new_key=$key[3];
			$this->assertTrue($new_key>=100,"Group #$new_key");
		}

		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>$new_key,"newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_Acknowledge");

		$rep=$this->CallAction("CORE","users_APAS_list",array(),"Xfer_Container_Custom");
		$comp=$rep->getComponents(1);
		$this->assertEquals("abc",$comp->m_records["101"]["login"]);
		$this->assertEquals("abc",$comp->m_records["101"]["realName"]);
		$this->assertEquals("Truc Muche",$comp->m_records["101"]["groupId"]);

		$rep=$this->CallAction("CORE","groups_APAS_supprimer",array("group"=>"".$new_key,"CONFIRME"=>"YES"),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Suppression impossible: Groupe non vide.",utf8_decode($rep->m_text));

			$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_Acknowledge");

			$rep=$this->CallAction("CORE","groups_APAS_supprimer",array("group"=>"".$new_key,"CONFIRME"=>"YES"),"Xfer_Container_Acknowledge");
		}
		else
			$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_Acknowledge");

	}

	function testLogin()
	{
		$rep=$this->Login("abc","cbd");
		$this->assertEquals(null,$rep,"A");

		$rep=$this->Login("abc","abc");
		$this->assertEquals(new Xfer_Auth("abc"),$rep,"B");

		$this->Login("admin","admin");

		$rep=$this->CallAction("CORE","users_APAS_miseajour",array("user_actif"=>"101","login"=>"abc","realName"=>"abc","groupId"=>1,"newpass1"=>"xyz","newpass2"=>"xyz"),"Xfer_Container_Acknowledge");

		$rep=$this->Login("abc","cbd");
		$this->assertEquals(null,$rep,"C");

		$rep=$this->Login("abc","abc");
		$this->assertEquals(null,$rep,"D");

		$rep=$this->Login("abc","xyz");
		$this->assertEquals(new Xfer_Auth("abc"),$rep,"E");

		$rep=$this->CallAction("CORE","users_APAS_confirmpwdmodif",array("oldpass"=>"xyz","newpass1"=>"xyz","newpass2"=>"abc"),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(4,$rep->m_type);
			$this->assertEquals("Les mots de passe ne sont pas égaux!",utf8_decode($rep->m_text));
		}

		$rep=$this->CallAction("CORE","users_APAS_confirmpwdmodif",array("oldpass"=>"xyz","newpass1"=>"abc","newpass2"=>"abc"),"Xfer_Container_DialogBox");
		if ($rep!=null)
		{
			$this->assertEquals(1,$rep->m_type);
			$this->assertEquals("Mot de passe changé",utf8_decode($rep->m_text));
		}

		$rep=$this->Login("abc","xyz");
		$this->assertEquals(null,$rep,"F");

		$rep=$this->Login("abc","abc");
		$this->assertEquals(new Xfer_Auth("abc"),$rep,"G");

		$rep=$this->Login("","");
		$this->assertEquals(null,$rep,"H");

		$rep=$this->Login("www","qqq");
		$this->assertEquals(null,$rep,"I");
	}

//@END@
}
?>
