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
// --- Last modification: Date 14 July 2009 18:11:41 By  ---

//@BEGIN@
/**
 * fichier gérant le classe de base de transfert
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer
 */require_once'PEAR.php';
require_once"XML/Beautifier.php";
/**
* CLOSE_NO
* Constante de non fermeture de fenetre
*/ define('CLOSE_NO',0);
/**
* CLOSE_YES
* Constante de fermeture de fenetre
*/ define('CLOSE_YES',1);
/**
* FORMTYPE_NOMODAL
* Constante d'un type de fiche no-modal
*/ define('FORMTYPE_NOMODAL',0);
/**
* FORMTYPE_MODAL
* Constante d'un type de fiche modal
*/ define('FORMTYPE_MODAL',1);
/**
* FORMTYPE_REFRESH
* Constante d'un type de fiche refresh
*/ define('FORMTYPE_REFRESH',2);
/**
* SELECT_NONE
* Constante d'un mode de selection (grille) : aucune selection
*/ define('SELECT_NONE',1);
/**
* SELECT_SINGLE
* Constante d'un mode de selection (grille) : selection unique
*/ define('SELECT_SINGLE',0);
/**
* SELECT_MULTI
* Constante d'un mode de selection (grille) : selection multiple
*/ define('SELECT_MULTI',2);
/**
 * trace_debug
 *
 * @access private
 * @param string $name
 * @param object $obj
 */
function trace_debug($name,$obj = null) {
	$res = "";
	if($obj != null) {
		require_once'debug_tools.php';
		$res .= " : ". Array_To_String($obj);
	}
	echo"<!--$name$res-->\n";
}
/**
 * Classe d'erreur de transfert
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Error extends PEAR_Error {
	/**
	 * Constructor
	 *
	 * @param string $msgorparser
	 * @param integer $code
	 * @param integer $mode
	 * @param integer $level
	 * @return Xfer_Error
	 */
	function Xfer_Error($msgorparser = 'unknown error',$code = 0,$mode = PEAR_ERROR_RETURN,$level = E_USER_NOTICE) {
		$this->PEAR_Error($msgorparser,$code,$mode,$level);
	}
}
/**
 * Classe de base de transfert
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Object extends PEAR {
	/**
	 * Constructeur

	 *
	 * @return Xfer_Object
	 */
	function Xfer_Object() {
		$this->PEAR('Xfer_Error');
	}
	/**
	 * Retourne la chaine XML à transferer
	 *
	 * @return string
	 */
	function getReponseXML() {
		return "";
	}
	/**
	 * Remonte une erreur PEAR
	 *
	 * @param string $msg
	 * @param integer $ecode
	 * @return PEAR_Error
	 */
	function raiseError($msg = null,$ecode = 0) {
		$err = & new Xfer_Error($msg,$ecode);
		return parent:: raiseError($err);
	}
	/**
	 * Vérifie les droits de l'utilisateur courant pour cette action
	 *
	 * @param Xfer_Action $action
	 * @return boolean
	 */
	function checkActionRigth($action) {
		$ret = false;
		if( strtolower( get_class($action)) == 'xfer_action') {
			if(($action->m_action == "") || ($action->m_extension == ""))$ret = true;
			else {
				global $login;
				$ret = checkRight($login,$action->m_extension,$action->m_action);
			}
		}
		return $ret;
	}
}
/**
 * Classe d'action
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Action extends Xfer_Object {
	/**
	 * Titre de l'action
	 *
	 * @var string
	 */
	var $m_title = "";

	/**
	 * Nom de l'icone
	 *
	 * @var string
	 */
	var $m_icon = "";

	/**
	 * Nom de l'extension
	 *
	 * @var string
	 */
	var $m_extension = "";

	/**
	 * Nom de l'action
	 *
	 * @var string
	 */
	var $m_action = "";

	/**
	 * CLOSE_YES=ferme la fenêtre appelante
	 * CLOSE_NO=fenêtre reste en fond
	 *
	 * @var integer
	 */
	var $m_close = "";

	/**
	 * FORMTYPE_MODAL=appel une fenêtre modal
	 * FORMTYPE_NOMODAL=non modal
	*  FORMTYPE_REFRESH=réutilise la fiche appelante
	 *
	 * @var integer
	 */
	var $m_modal = "";

	/**
	 * SELECT_NONE=action à l'ensemble d'une grille
	 * SELECT_SINGLE=action associée à une sélection dans une grille
	 * SELECT_MULTI=action associée à une ou plusieurs sélections dans une grille
	 *
	 * @var integer
	 */
	var $m_select = "";

	/**
	 * _begin_tag
	 *
	 * @access private
	 * @var string
	 */
	var $_begin_tag = "";

	/**
	 * _end_tag
	 *
	 * @access private
	 * @var string
	 */
	var $_end_tag = "";

	/**
	 * Constructeur
	 *
	 * @param string $title titre du bouton
	 * @param string $icon nom de l'icone
	 * @param string $extension
	 * @param string $action nom de l'action
	 * @param integer $modal FORMTYPE_MODAL=appel une fenêtre modal - FORMTYPE_NOMODAL=non modal - FORMTYPE_REFRESH=réutilise la fiche appelante
	 * @param integer $close CLOSE_YES=ferme la fenêtre appelante - CLOSE_NO=fenêtre reste en fond
	 * @param integer $select SELECT_NONE=action à l'ensemble d'une grille - SELECT_SINGLE=action associée à une sélection dans une grille - SELECT_MULTI=action associée à une ou plusieurs sélections dans une grille
	 * @return Xfer_Action
	 */
	function Xfer_Action($title,$icon = "",$extension = "",$action = "",$modal = "",$close = "",$select = "") {
		$this->Xfer_Object();
		$this->m_title = $title;
		$this->m_icon = "";

		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		if( is_file($rootPath."extensions/$extension/images/$icon"))
			$this->m_icon = $rootPath."extensions/$extension/images/$icon";
		else if( is_file($rootPath."images/$icon"))
			$this->m_icon = $rootPath."images/$icon";
		else if( is_file($rootPath."$icon"))
			$this->m_icon = $rootPath.$icon;
		$this->m_extension = $extension;
		$this->m_action = $action;
		$this->m_close = $close;
		$this->m_modal = $modal;
		$this->m_select = $select;
		$this->_begin_tag = "<ACTION%s>";
		$this->_end_tag = "</ACTION>";
	}
	/**
	 * contenu
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		return "<![CDATA[".$this->m_title."]]>";
	}
	/**
	 * Retourne la chaine XML à transferer
	 *
	 * @return string
	 */
	function getReponseXML() {
		$xml_attrb = "";
		if($this->m_icon != "") {
			$size=filesize($this->m_icon);
			$xml_attrb = sprintf("%s icon='%s' sizeicon='%d'",$xml_attrb,$this->m_icon,$size);
		}
		if($this->m_extension != "")$xml_attrb = sprintf("%s extension='%s'",$xml_attrb,$this->m_extension);
		if($this->m_action != "")$xml_attrb = sprintf("%s action='%s'",$xml_attrb,$this->m_action);
		if( is_int($this->m_close))$xml_attrb = sprintf("%s close='%d'",$xml_attrb,$this->m_close);
		if( is_int($this->m_modal))$xml_attrb = sprintf("%s modal='%d'",$xml_attrb,$this->m_modal);
		if( is_int($this->m_select))$xml_attrb = sprintf("%s unique='%d'",$xml_attrb,$this->m_select);
		$xml_text = sprintf($this->_begin_tag,$xml_attrb);
		$xml_text = $xml_text.$this->_getContent();
		$xml_text = $xml_text.$this->_end_tag;
		return $xml_text;
	}
}
/**
 * Classe abstraite de base au containeur de transfert
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_Abstract extends Xfer_Object {
	/**
	 * Identifiant de l'observeur
	 *
	 * @var string
	 */var$m_observer_name = '';
	/**
	 * Nom de l'extension appelante
	 *
	 * @var string
	 */var$m_extension = "";
	/**
	 * Nom de l'action appelante
	 *
	 * @var string
	 */var$m_action = "";
	/**
	 * Contexte d'appéle
	 *
	 * @var array
	 */var$m_context = array();
	/**
	 * Titre de l'action
	 *
	 * @var string
	 */var$Caption = "";
	/**
	 * action de fermeture
	 *
	 * @var Xfer_Action
	 */var$m_closeaction = null;
	/**
	 * Constructeur
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Abstract
	 */
	function Xfer_Container_Abstract($extension,$action,$context = array()) {
		$this->Xfer_Object();
		$this->m_extension = $extension;
		$this->m_action = $action;
		$this->m_context = $context;
	}
	/**
	 * change l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	function setCloseAction($action) {
		if($this->checkActionRigth($action)) {
			$this->m_closeaction = $action;
		}
	}
	/**
	 * Retourne la chaine XML à transferer
	 *
	 * @return string
	 */
	function getReponseXML() {
		$xml_text = sprintf("\n<REPONSE observer='%s' source_extension='%s' source_action='%s'>\n",$this->m_observer_name,$this->m_extension,$this->m_action);
		$xml_text .= sprintf("<TITLE><![CDATA[%s]]></TITLE>\n",$this->Caption);
		$xml_text .= "<CONTEXT>";
		if( is_array($this->m_context)) {
			foreach($this->m_context as $key => $value) {
				$xml_text = $xml_text. sprintf("<PARAM name='%s'><![CDATA[%s]]></PARAM>",$key,$value);
			}
		}
		$xml_text = $xml_text."</CONTEXT>\n";
		$xml_text = $xml_text.$this->_ReponseXML();
		if($this->m_closeaction != null) {
			$xml_text .= "<CLOSE_ACTION>";
			$close_action = $this->m_closeaction;
			$xml_text .= $close_action->getReponseXML();
			$xml_text .= "</CLOSE_ACTION>";
		}
		$xml_text = $xml_text."</REPONSE>";
		return $xml_text;
	}
	/**
	 * _endXML
	 *
	 * @access private
	 * @return string
	 */
	function _endXML() {
		return "</REPONSE>";
	}
	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	function _ReponseXML() {
		return "";
	}
}
/**
 * Classe d'accusé de reception
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_Acknowledge extends Xfer_Container_Abstract {
	/**
	 * Titre de confirmation
	 *
	 * @var string
	 */
	var $Title = "";

	/**
	 * Message
	 *
	 * @var string
	 */
	var $Msg = "";

	/**
	 * traitment
	 *
	 * @var array
	 */
	var $traitment=null;

	/**
	* Type de message
	*
	* @var integer
	*/
	var $Type = 1;

	/**
	 * Action de redirection
	 *
	 * @var Xfer_Action
	 */
      var $Redirect = null;

	/**
	 * Constructeur
	 *
	 * @param string $extension
	 * @param string $action
	 * @param string $context
	 * @return Xfer_Container_Acknowledge
	 */
	function Xfer_Container_Acknowledge($extension,$action,$context = array()) {
		$this->Xfer_Container_Abstract($extension,$action,$context);
		$this->m_observer_name = "Core.Acknowledge";
	}

	/**
	 * Demande une confirmation avant une action irrémédiable (ex:suppression)
	 *
	 * @param string $title
	 * @return boolean true=confirmé - false=pas confirmé
	 */
	function confirme($title) {
		$this->Title = $title;
		if($title != "") {
			if( array_key_exists("CONFIRME",$this->m_context))
			return ($this->m_context["CONFIRME"] != "");
			else
			return false;
		}
		else
		return true;
	}

	/**
	 * Assigne un message de resultat
	 *
	 * @param string $title
	 */
	function message($title,$type = 1) {
		$this->Msg = $title;
		$this->Type = $type;
	}

	/**
	 * Gestion d'un traitement long
	 *
	 * @param string $icon
	 * @param string $waitingMessage
	 * @param string $finishMessage
	 */
	function traitment($icon,$waitingMessage,$finishMessage) {
		$this->traitment=array($icon,$waitingMessage,$finishMessage);
		if( array_key_exists("RELOAD",$this->m_context))
			return ($this->m_context["RELOAD"] != "");
		else
			return false;
	}

	/**
	 * Demande au client de rediriger cette action.
	 *
	 * @param Xfer_Action $action
	 */
	function redirectAction($action) {
		if($this->checkActionRigth($action))$this->Redirect = $action;
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	function _ReponseXML() {
		if($this->Redirect == null)
		return "";
		else
		return $this->Redirect->getReponseXML();
	}

	/**
	 * Retourne la chaine XML à transferer
	 *
	 * @return string
	 */
	function getReponseXML() {
		if(($this->Title != "") && (! array_key_exists("CONFIRME",$this->m_context) || ($this->m_context["CONFIRME"] != "YES"))) {
			require_once'xfer_dialogBox.inc.php';
			$this->m_context["CONFIRME"] = "YES";
			$dlg = new Xfer_Container_DialogBox($this->m_extension,$this->m_action,$this->m_context);
			$dlg->Caption = "Confirmation";
			$dlg->setTypeAndText($this->Title, XFER_DBOX_CONFIRMATION);
			$dlg->addAction( new Xfer_Action("Oui","ok.png",$this->m_extension,$this->m_action,FORMTYPE_MODAL,CLOSE_YES));
			$dlg->addAction( new Xfer_Action("Non","cancel.png"));
			$dlg->m_closeaction = $this->m_closeaction;
			return $dlg->getReponseXML();
		}
		else if($this->Msg != "") {
			require_once'xfer_dialogBox.inc.php';
			$dlg = new Xfer_Container_DialogBox($this->m_extension,$this->m_action,$this->m_context);
			$dlg->Caption = "Message";
			$dlg->setTypeAndText($this->Msg,$this->Type);
			$dlg->addAction( new Xfer_Action("_Ok","ok.png"));
			$dlg->m_closeaction = $this->m_closeaction;
			return $dlg->getReponseXML();
		}
		else if($this->traitment!=null) {
			require_once'xfer_custom.inc.php';
			$dlg = new Xfer_Container_Custom($this->m_extension,$this->m_action,$this->m_context);
			$dlg->Caption = $this->Caption;
			$dlg->m_context = $this->m_context;
			$img_title = new Xfer_Comp_Image('img_title');
			$img_title->setLocation(0,0,1,2);
			$img_title->setValue($this->traitment[0]);
			$dlg->addComponent($img_title);

			$lbl = new Xfer_Comp_LabelForm("info");
			$lbl->setLocation(1,0);
			$dlg->addComponent($lbl);
			if (array_key_exists('RELOAD',$this->m_context)) {
				$lbl->setValue("{[newline]}".$this->traitment[2]);
				$dlg->addAction( new Xfer_Action('_Fermer','close.png','','', FORMTYPE_MODAL, CLOSE_YES));
			}
			else {
				$lbl->setValue("{[newline]}{[center]}".$this->traitment[1]."{[/center]}");
				$dlg->m_context["RELOAD"] = "YES";
				$btn = new Xfer_Comp_Button("Next");
				$btn->setLocation(1,1);
				$btn->setSize(50,300);
				$btn->setAction( new Xfer_Action('Traitement...','',$this->m_extension,$this->m_action, FORMTYPE_REFRESH, CLOSE_NO));
				$btn->JavaScript = "
					parent.refresh();
				";
				$dlg->addComponent($btn);
				$dlg->addAction( new Xfer_Action('_Annuler','cancel.png','','', FORMTYPE_MODAL, CLOSE_YES));
			}
			return $dlg->getReponseXML();
		}
		else
		return Xfer_Container_Abstract:: getReponseXML();
	}
}
/**
 * Converti une heure DB en chaine
 *
 * @param string $time
 * @return string
 */
function convertTime($time) {
	list($hour,$minute,$sec) = split(':',"$time");
	//$val=mktime($hour,$minute,$sec,0,0,0);
	//return date("H:i:s",$val);;
	return $hour.":".$minute;
}
/**
 * Converti une date DB en chaine
 *
 * @param string $date
 * @param boolean $long
 * @return string
 */
function convertDate($date,$long = false) {
	$year = 2000;
	$mouth = 1;
	$day = 1;
	$d = split('-',$date);
	if( count($d) == 3)list($year,$mouth,$day) = $d;
	elseif ( count($d) == 2)list($year,$mouth) = $d;
	else
	return "";
	if($long) {
		$j = (int)$day;
		$m = (int)$mouth;
		switch((int)$m) {
		case 1:
			$m = "Janvier";
			break;
		case 2:
			$m = "Février";
			break;
		case 3:
			$m = "Mars";
			break;
		case 4:
			$m = "Avril";
			break;
		case 5:
			$m = "Mai";
			break;
		case 6:
			$m = "Juin";
			break;
		case 7:
			$m = "Juillet";
			break;
		case 8:
			$m = "Août";
			break;
		case 9:
			$m = "Septembre";
			break;
		case 10:
			$m = "Octobre";
			break;
		case 11:
			$m = "Novembre";
			break;
		case 12:
			$m = "Décembre";
			break;
		default :
			$m = "Janvier";
			break;
		}
		$Y = (int)$year;
		return $j." ".$m." ".$Y;
	}
	else
	return "$day/$mouth/$year";
}
/**
 * Converti de caractére pour l'impression
 *
 * @param string $text
 * @param boolean $WithCdata
 * @return string
 */
function convertForPrint($text,$WithCdata = false) {
	if($WithCdata)$text = str_replace("{[newline]}","]]><br></br><![CDATA[",$text);
	else $text = str_replace("{[newline]}","<br></br>",$text);
	return $text;
}

function rm_recursive($filepath) {
	if( is_dir($filepath) && ! is_link($filepath)) {
		if($dh = opendir($filepath)) {
			while(($sf = readdir($dh)) !== false) {
				if($sf == '.' || $sf == '..') {
					continue;
				}
				if(! rm_recursive($filepath.'/'.$sf)) {
					throw new Exception($filepath.'/'.$sf.' could not be deleted.');
				}
			} closedir($dh);
		}
		return rmdir($filepath);
	}
	return unlink($filepath);
}
//@END@
?>
