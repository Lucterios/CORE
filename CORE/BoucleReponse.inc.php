<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//  // library file write by SDK tool
// --- Last modification: Date 17 June 2009 0:20:51 By  ---

//@BEGIN@
function studyReponse($current_reponse)
{
	$Params=array();
	if (is_string($current_reponse))
	{
		$p = &new COREParser();
		$p->setInputString($current_reponse);
		$p->parse();
		$Reponse_trans = $p->getResult();
		if (("xmlelement" == strtolower(get_class($Reponse_trans))) && ($Reponse_trans->getTagName()=="REPONSE"))
		{
			if ($Reponse_trans->getAttributeValue('observer')=='CORE.Exception')
				return null;
			$lesReponses = $Reponse_trans->getChildsByTagName("PARAM");
			foreach($lesReponses as $uneReponse)
				$params[utf8_decode($uneReponse->getAttributeValue('name'))]=utf8_decode($uneReponse->getCData());
			return $params;
		}
		else
			return null;
	}
	else if (is_object($current_reponse))
	{
		if ("Xfer_Container_Exception" == strtolower(get_class($current_reponse))
)
			return null;
		else
			return $current_reponse->m_context;
	}
	else
		return null;
}

function callAction($extension,$action,$params,$internal) {
	global $login,$dbcnf;
	global $rootPath;
	if(!isset($rootPath)) $rootPath = "";
	if (strtoupper($extension)=="CORE") {
		$extension=strtoupper($extension);
		$EXT_FOLDER=$rootPath.$extension;
	}
	else
		$EXT_FOLDER=$rootPath."extensions/$extension";
	$ACTION_FILE_NAME = "$EXT_FOLDER/$action.act.php";
	if (!is_dir($EXT_FOLDER))
		throw new LucteriosException(CRITIC,"Extension '$extension' inconnue !");
	else if (!is_file($ACTION_FILE_NAME))
		throw new LucteriosException(CRITIC,"Action '$action' inconnue !");
	else if($internal or checkRight($login, $extension, $action)){
		 // verif des droits d'executions
		require_once $ACTION_FILE_NAME;
		if (!function_exists($action))
			throw new LucteriosException(CRITIC,"Function '$action' inconnue !");
		else {
			if (is_file("$EXT_FOLDER/includes.inc.php"))
				require_once("$EXT_FOLDER/includes.inc.php");
			// l'action existe, on la lance:
			$current_reponse=call_user_func($action,$params);
		}
	}
	else
		throw new LucteriosException(CRITIC,'Mauvais droit');
	return $current_reponse;
}

function BoucleReponse($lesRequettes,$internal=false)
{
	global $login,$dbcnf;
	require_once("CORE/Lucterios_Error.inc.php");
	require_once("CORE/xfer.inc.php");
	$REPONSE="";
	$params = array();
	foreach($lesRequettes as $req)
	{
		$extension = "";
		$action = "";
		try {
			$current_reponse="";
			global $extension;
			$extension = $req->getAttributeValue("EXTENSION");
			$action = utf8_decode ($req->getAttributeValue("ACTION"));

			if($extension=="common" && $action=="authentification") {
				// l'authentification est deja g?e plus haut, on la zap!
				continue;
			}

			// on recupere les param?es pour l'action
			$paramTable = $req->getChildsByTagName("PARAM");
			foreach($paramTable as $par) {
				$params[utf8_decode($par->getAttributeValue("NAME"))] = utf8_decode($par->getCData());
			}

			// on sait maintenant qu'on ?es droits d'executer l'action voulue
			$current_reponse=callAction($extension,$action,$params,$internal);

			if (is_string($current_reponse)){
				if ($current_reponse!="")
					$REPONSE.=$current_reponse."\n";
				else
					$REPONSE.=xfer_returnError($extension,$action,$params,new Xfer_Error("Résultat vide!!",10003));
			}
			else{
				$REPONSE.=$current_reponse->getReponseXML()."\n";
			}

			if ($internal)
			{
				if (($params = studyReponse($current_reponse))==null)
					return $REPONSE;
			}
			else
				$params = array();
		} catch (Exception $e) {              // Devrait être attrapée
			logAutre("[Exception] extension=$extension - action=$action - ".$e->__toString());
			require_once "CORE/xfer_exception.inc.php";
  			$Xfer_erro=new Xfer_Container_Exception("CORE","coreIndex");
	  		$Xfer_erro->setData($e);
  			$REPONSE.= $Xfer_erro->getReponseXML()."\n";
		}
	}
	return $REPONSE;
}
//@END@
?>
