<?php
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//


//@BEGIN@

require_once 'PHPUnit.php';

if (!isset($ServerBaseAddr))
{
	$ServerBaseAddr="http://localhost";
 	$uri=$_SERVER['REQUEST_URI'];
	$pos=strrpos($uri,"/");
	if ($pos>=0)
		$ServerBaseAddr.=substr($uri,0,$pos)."/coreIndex.php";
}

class Xfer_Auth
{
	var $LogName;
	function Xfer_Auth($logName)
	{
		$this->LogName=$logName;
	}
}

class APASUnit_TestSuite extends PHPUnit_TestSuite
{
	var $_IncActions=array();

	function APASUnit_TestSuite($directory)
	{
		parent::PHPUnit_TestSuite();
		if (is_dir($directory))
		{
			$dh=opendir($directory);
			while (($file = readdir($dh)) != false)
				if (is_file($directory.$file) && (substr($file,-8,8)==".act.php"))
					$this->_IncActions[substr($file,0,-8)]=0;
		}
	}

	function run(&$result)
	{
		for ($i = 0; $i < sizeof($this->_tests) && !$result->shouldStop(); $i++)
		{
			$test=$this->_tests[$i];
			$test->SetTestSuite($this);
			$test->run($result);
		}
	}
}

class APASUnit_TestCase extends PHPUnit_TestCase
{
	var $session='';
	var $TestSuite=null;

	function SetTestSuite(&$testSuite)
	{
		$this->TestSuite=& $testSuite;
	}

	function __TraceAction($actionName)
	{
		if ($this->TestSuite!=null)
		{
			if (array_key_exists($actionName,$this->TestSuite->_IncActions))
				$this->TestSuite->_IncActions[$actionName]=$this->TestSuite->_IncActions[$actionName]+1;
		}
	}

	function setUp()
	{
		$this->Login("admin","admin");
	}

    	function assertClass($expected, $actual, $message = '')
	{
		return $this->assertEquals(strtolower($expected),strtolower(get_class($actual)),$message);
	}

    	function assertEquals($expected, $actual, $message = '')
	{
		if (is_object($expected) && is_object($actual))
		{
			if (strtolower(get_class($expected))==strtolower(get_class($actual)))
			{
				$expected_vars=get_object_vars($expected);
				$actual_vars=get_object_vars($actual);
				$expected_txt="";
				$actual_txt="";
				foreach ($expected_vars as $prop => $val)
                           if (array_key_exists($prop,$actual_vars))
					{
						if ("$val"!=("".$actual_vars[$prop]))
						{
						$expected_txt.="$prop='$val';";
						$actual_txt.="$prop='".$actual_vars[$prop]."';";
						}
					}
                           else
					{
						$expected_txt.="$prop='$val';";
						$actual_txt.="$prop=###;";
					}
				foreach ($actual_vars as $prop => $val)
                           if (!array_key_exists($prop,$expected_vars))
					{
						$expected_txt.="$prop=###;";
						$actual_txt.="$prop='$val';";
					}
				return PHPUnit_TestCase::assertEquals($expected_txt,$actual_txt,$message);
			}
			else
				return PHPUnit_TestCase::assertEquals(strtolower($expected),strtolower(get_class($actual)),$message);
		}
		else
			return PHPUnit_TestCase::assertEquals($expected, $actual, $message);
      }

	function __Auth($repItem)
	{
		$this->session="";
		$params=$repItem->getChildsByTagName("PARAM");
		foreach($params as $param)
			if ($param->getAttributeValue("name")=="ses")
				$this->session=$param->getCData();
		if ($this->session!="")
		{
			$login_name="???";
			$cnxs=$repItem->getChildsByTagName("CONNECTION");
			foreach($cnxs as $cnx)
			{
				$logins=$cnx->getChildsByTagName("LOGIN");
				foreach($logins as $login)
					$login_name=$login->getCData();
			}
			return new Xfer_Auth($login_name);
		}
		else
			return null;
	}

	function __Context($repItem)
	{
		$ret_context=array();
		$contexts=$repItem->getChildsByTagName("CONTEXT");
		foreach($contexts as $context)
		{
			$params=$context->getChildsByTagName("PARAM");
			foreach($params as $param)
				$ret_context[$param->getAttributeValue("name")]=$param->getCData();
		}
		return $ret_context;
	}

	function GetMenu($menuItem,$menuId)
	{
		$menu=null;
		foreach($menuItem as $item)
			if (strtolower($item->m_id)==strtolower($menuId))
			{
				$menu=$item;
				break;
			}
		return $menu;
	}

	function __MenuItem($repItem)
	{
		$obj=new Xfer_Menu_Item($repItem->getAttributeValue("id"), $repItem->getCData(), $repItem->getAttributeValue("icon"), $repItem->getAttributeValue("extension"), $repItem->getAttributeValue("action"));
		$menus=$repItem->getChildsByTagName("MENU");
		foreach($menus as $menu)
			$obj->addSubMenu($this->__MenuItem($menu));
		return $obj;
	}

	function __Menu($repItem)
	{
		require_once "CORE/xfer_menu.inc.php";
		$obj=new Xfer_Container_Menu($repItem->getAttributeValue("source_extension"), $repItem->getAttributeValue("source_action"), $this->__Context($repItem));
		$menus=$repItem->getChildsByTagName("MENUS");
		foreach($menus as $menu)
			$obj->addSubMenu($this->__MenuItem($menu));
		return $obj;
	}

	function __Action($repItem)
	{
		$obj=new Xfer_Action(utf8_decode($repItem->getCData()), $repItem->getAttributeValue("icon"), $repItem->getAttributeValue("extension"), $repItem->getAttributeValue("action"), $repItem->getAttributeValue("modal"), $repItem->getAttributeValue("close"), $repItem->getAttributeValue("unique"));
		return $obj;
	}

	function __Component($name,$repItem)
	{
		$obj=null;
		if ($name=='LABEL')
		{
			$obj=new Xfer_Comp_Label($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='LABELFORM')
		{
			$obj=new Xfer_Comp_LabelForm($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='GRID')
		{
			$obj=new Xfer_Comp_Grid($repItem->getAttributeValue("name"));
			$headers=$repItem->getChildsByTagName("HEADER");
			foreach($headers as $header)
				$obj->addHeader($header->getAttributeValue("name"), $header->getCData(), $header->getAttributeValue("type"));
			$records=$repItem->getChildsByTagName("RECORD");
			foreach($records as $record)
			{
				$id=$record->getAttributeValue("id");
				$values=$record->getChildsByTagName("VALUE");
				foreach($values as $val)
					$obj->setValue($id,$val->getAttributeValue("name"),$val->getCData());
			}
			$acts=$repItem->getChildsByTagName("ACTIONS");
			foreach($acts as $act)
			{
				$actions=$act->getChildsByTagName("ACTION");
				foreach($actions as $action)
					array_push($obj->m_actions,$this->__Action($action));
			}
		}
		elseif ($name=='EDIT')
		{
			$obj=new Xfer_Comp_Edit($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='DATE')
		{
			$obj=new Xfer_Comp_Date($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='TIME')
		{
			$obj=new Xfer_Comp_Time($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='PASSWD')
		{
			$obj=new Xfer_Comp_Passwd($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='MEMO')
		{
			$obj=new Xfer_Comp_Memo($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='MEMOFORM')
		{
			$obj=new Xfer_Comp_MemoForm($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='FLOAT')
		{
			$obj=new Xfer_Comp_Float($repItem->getAttributeValue("name"), $repItem->getAttributeValue("min"), $repItem->getAttributeValue("max"), $repItem->getAttributeValue("prec"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='CHECK')
		{
			$obj=new Xfer_Comp_Check($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		elseif ($name=='SELECT')
		{
			$obj=new Xfer_Comp_Select($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
			$cases=$repItem->getChildsByTagName("CASE");
			foreach($cases as $case)
			{
				$id=(int)$case->getAttributeValue("id");
				$obj->m_select[$id]=$case->getCData();
			}
		}
		elseif ($name=='CHECKLIST')
		{
			$obj=new Xfer_Comp_CheckList($repItem->getAttributeValue("name"));
			$obj->setValue($repItem->getCData());
		}
		if ($obj!=null)
		{
			$obj->setLocation($repItem->getAttributeValue("x"), $repItem->getAttributeValue("y"), $repItem->getAttributeValue("colspan"), $repItem->getAttributeValue("rowspan"));
			$obj->setNeeded($repItem->getAttributeValue("needed")==1);
		}
		return $obj;
	}

	function __Custom($repItem)
	{
		require_once "CORE/xfer_custom.inc.php";
		$obj=new Xfer_Container_Custom($repItem->getAttributeValue("source_extension"), $repItem->getAttributeValue("source_action"), $this->__Context($repItem));
		$acts=$repItem->getChildsByTagName("ACTIONS");
		foreach($acts as $act)
		{
			$actions=$act->getChildsByTagName("ACTION");
			foreach($actions as $action)
				array_push($obj->m_actions,$this->__Action($action));
		}
		$cmps=$repItem->getChildsByTagName("COMPONENTS");
		foreach($cmps as $cmp)
		{
			$components=$cmp->getChilds();
			foreach($components as $component)
				$obj->addComponent($this->__Component($component->getTagName(),$component));
		}
		return $obj;
	}

	function __Dialog($repItem)
	{
		require_once "CORE/xfer_dialogBox.inc.php";
		$obj=new Xfer_Container_DialogBox($repItem->getAttributeValue("source_extension"), $repItem->getAttributeValue("source_action"), $this->__Context($repItem));
		$txts=$repItem->getChildsByTagName("TEXT");
		foreach($txts as $txt)
			$obj->setTypeAndText($txt->getCData(),$txt->getAttributeValue("type"));
		$acts=$repItem->getChildsByTagName("ACTIONS");
		foreach($acts as $act)
		{
			$actions=$act->getChildsByTagName("ACTION");
			foreach($actions as $action)
				array_push($obj->m_actions,$this->__Action($action));
		}
		return $obj;
	}

	function __Template($repItem)
	{
		require_once "CORE/xfer_printing.inc.php";
		$obj=new Xfer_Container_Template($repItem->getAttributeValue("source_extension"), $repItem->getAttributeValue("source_action"), $this->__Context($repItem));
		$tmplts=$repItem->getChildsByTagName("TEMPLATE");
		foreach($tmplts as $tmplt)
		{
			$obj->title=$tmplt->getAttributeValue("title");
			$obj->m_model_id=$tmplt->getAttributeValue("model");
			$xml_data_list=$tmplt->getChildsByTagName("XMLOBJECT");
			foreach($xml_data_list as $xml_data)
				$obj->m_xml_data=$xml_data->getCData();
			$model_list=$tmplt->getChildsByTagName("XSLTEXT");
			foreach($model_list as $model)
				$obj->m_model=$model->getCData();
		}
		return $obj;
	}

	function __Print($repItem)
	{
		require_once "CORE/xfer_printing.inc.php";
		$obj=new Xfer_Container_Print($repItem->getAttributeValue("source_extension"), $repItem->getAttributeValue("source_action"), $this->__Context($repItem));
		$prts=$repItem->getChildsByTagName("PRINT");
		foreach($prts as $prt)
		{
			$obj->title=$prt->getAttributeValue("title");
			$obj->fo_text=$prt->getCData();
		}
		return $obj;
	}

	function ConvertReponseToObject($xmlReponse)
	{
		require_once "CORE/XMLparse.inc.php";
		require_once "CORE/xfer.inc.php";
		$parser=new COREParser();
		$parser->setInputString($xmlReponse);
		$parser->parse();
		//$comment=$parser->getComment();
		//if (count($comment)>0)
		//	trace_debug('Comment',$comment);
		$reponses=$parser->getByTagName("REPONSE");
		foreach($reponses as $reponse_item)
		{
			$obsever_name=strtolower($reponse_item->getAttributeValue("observer"));
			if ($obsever_name=="core.auth")
			{
				$auth=$this->__Auth($reponse_item);
				return $auth;
			}
			elseif ($obsever_name=="core.menu")
				return $this->__Menu($reponse_item);
			elseif ($obsever_name=="core.custom")
			{
				//echo htmlentities($xmlReponse)."<br>";
				return $this->__Custom($reponse_item);
			}
			elseif ($obsever_name=="core.acknowledge")
			{
				//echo htmlentities($xmlReponse)."<br>";
				return new xfer_container_acknowledge($reponse_item->getAttributeValue("source_extension"), $reponse_item->getAttributeValue("source_action"), $this->__Context($reponse_item));
			}
			elseif ($obsever_name=="core.dialogBox")
			{
				//echo htmlentities($xmlReponse)."<br>";
				return $this->__Dialog($reponse_item);
			}
			elseif ($obsever_name=="core.template")
			{
				//echo htmlentities($xmlReponse)."<br>";
				return $this->__Template($reponse_item);
			}
			elseif ($obsever_name=="core.print")
			{
				//echo htmlentities($xmlReponse)."<br>";
				return $this->__Print($reponse_item);
			}
			else
			{
				echo htmlentities($xmlReponse)."<br>";
				echo "observer:$obsever_name<br>";
				echo "param:".print_r($reponse_item->attribs).'<br>';
			}
		}
		return null;
	}

	function Login($login,$pass)
	{
		$this->session="";
		return $this->CallAction("common","authentification",array("login"=>$login,"pass"=>$pass));
	}

	function CallAction($extension,$action,$params,$class_name='')
	{
		global $ServerBaseAddr;
		require_once("HTTP/Request.php");
        	$xml_param="<?xml version='1.0' encoding='utf-8'?>";
        	$xml_param.="<REQUETES>\\\\n";
        	if ($this->session!="")
		{
            		$xml_param.="<REQUETE extension='common' action='authentification'>";
            		$xml_param.="<PARAM name='ses'>".$this->session."</PARAM>";
            		$xml_param.="</REQUETE>";
        	}
		$xml_param.="<REQUETE extension='$extension' action='$action'>";
		foreach($params as $key=>$value)
			$xml_param.="<PARAM name='$key'><![CDATA[".utf8_encode($value)."]]></PARAM>";
		$xml_param.="</REQUETE>";
        	$xml_param.="</REQUETES>";

		$req =& new HTTP_Request($ServerBaseAddr);
		$req->setMethod(HTTP_REQUEST_METHOD_POST);
		$req->addPostData("XMLinput", urlencode($xml_param));
		$send=$req->sendRequest();
		if (!PEAR::isError($send))
		{
			$this->__TraceAction($action);
			$XMLResponse = urldecode($req->getResponseBody());
			$converted_object=$this->ConvertReponseToObject($XMLResponse);
			if ($class_name!='')
			{
				$this->assertClass($class_name,$converted_object,"CallAction($extension,$action)");
				if (strtolower($class_name)!=strtolower(get_class($converted_object)))
					$converted_object=null;
			}
			return $converted_object;
		}
		else
		{
			$this->fail($send->toString());
			return null;
		}
	}
}

//@END@
?>
