<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// library file write by SDK tool
// --- Last modification: Date 28 April 2011 22:04:25 By  ---

//@BEGIN@
/**
 * fichier gérant des composants pour une fenêtre personnalisée
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer

 */

require_once ('CORE/xfer.inc.php');

/**
* Configuration du lien mail
*
* 1=cc
* 2=bcc
* autre=to
**/
$MAILTO_TYPE=0;
/**
* Gestionnaire de Link label
*
**/
$LINK_LABEL_MANAGER="";


/**
 * Classe abtraite de composant
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Component extends Xfer_Object {
	/**
	 * Nom du type de composant
	 *
	 * @access public
	 * @var string
	 */
	protected $_componentIdent;

	/**
	 * Identifiant du composant
	 *
	 * @var string
	 */
	public $m_name;

	/**
	 * Valeur véhiculée par le composant
	 *
	 * @var unknown_type
	 */
	public $m_value;

	/**
	 * Identifiant de l'onglet associé. 0=Hors onglet
	 *
	 * @var integer
	 */
	public $tab;

	/**
	 * position horizontal
	 *
	 * @var integer
	 */
	public $x;

	/**
	 * position vertical
	 *
	 * @var integer
	 */
	public $y;
	/**
	 * Encombrement horizontal. 1 par defaut
	 *
	 * @var integer
	 */
	public $colspan;

	/**
	 * Encombrement vertical. 1 par defaut.
	 *
	 * @var integer
	 */
	public $rowspan;

	/**
	 * Description du composant
	 *
	 * @var string
	 */
	public $m_description;

	/**
	 * Précise si le champ est obligatoire. False par defaut.
	 *
	 * @var boolean
	 */
	public $needed;

	/**
	 * Taille vertical minimum
	 *
	 * @var integer
	 */
	public $VMin;

	/**
	 * Taille horizontal minimum
	 *
	 * @var integer
	 */
	public $HMin;

	/**
	 * Taille vertical maximum
	 *
	 * @var integer
	 */
	public $VMax;

	/**
	 * Taille horizontal maximum
	 *
	 * @var integer
	 */
	public $HMax;

	/**
	 * Constructeur
	 *
	 * @param string $name identifiant du composant
	 * @return Xfer_Component
	 */
	public function __construct($name) {
		parent::__construct();
		$this->m_name = $name;
		$this->_componentIdent = "";
		//$component;
		$this->m_value = "";
		$this->tab = 0;
		$this->x = 0;
		$this->y = 0;
		$this->VMin = "";
		$this->HMin = "";
		$this->VMax = "";
		$this->HMax = "";
		$this->colspan = 1;
		$this->rowspan = 1;
		$this->m_description = "";
		$this->needed = false;
	}

	public function getIdent(){
		return $this->_componentIdent;
	}

	/**
	 * Change la position en l'encombrement du composant
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $colspan
	 * @param integer $row
	 */
	public function setLocation($x,$y,$colspan = 1,$rowspan = 1) {
		$this->x = (int)$x;
		$this->y = (int)$y;
		$this->colspan = (int)$colspan;
		$this->rowspan = (int)$rowspan;
	}

	/**
	*  Change la taille min/max du composant
	*
	* @param integer $VMin
	* @param integer $HMin
	* @param integer $VMax
	* @param integer $HMax
	*/
	public function setSize($VMin,$HMin,$VMax = "",$HMax = "") {
		$this->VMin = $VMin;
		$this->HMin = $HMin;
		$this->VMax = $VMax;
		$this->HMax = $HMax;
	}

	/**
	 * Change la valeur véhiculée par le composant
	 *
	 * @param unknown_type $value
	 */
	public function setValue($value) {
		$this->m_value = $value;
	}

	/**
	 * Change l'état obligatoire du composant
	 *
	 * @param boolean $needed
	 */
	public function setNeeded($needed) {
		$this->needed = $needed;
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return $this->m_value;
	}

	/**
	 * Retourne l'identification du composant pour ordonnancer un ensemble de composants
	 *
	 * @return string
	 */
	public function getId() {
		$text = "";
		if( is_int($this->tab))
			$text.= sprintf("tab=%4d",(int)$this->tab);
		else
			$text.= sprintf("tab=%4d",0);
		if( is_int($this->y) && ($this->y >= 0))
			$text.= sprintf("_y=%4d",(int)$this->y);
		else
			$text.= "_y=    ";
		if( is_int($this->x) && ($this->x >= 0))
			$text.= sprintf("_x=%4d",(int)$this->x);
		else
			$text.= "_x=    ";
		return $text;
	}

	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$description = urlencode($this->m_description);
		$xml_attr = sprintf("name='%s' description='%s' ",$this->m_name,$description);
		if( is_int($this->tab))$xml_attr .= sprintf(" tab='%s'",$this->tab);
		if( is_int($this->x))$xml_attr .= sprintf(" x='%s'",$this->x);
		if( is_int($this->y))$xml_attr .= sprintf(" y='%s'",$this->y);
		if( is_int($this->colspan))$xml_attr .= sprintf(" colspan='%s'",$this->colspan);
		if( is_int($this->rowspan))$xml_attr .= sprintf(" rowspan='%s'",$this->rowspan);
		if( is_int($this->VMin))$xml_attr .= sprintf(" VMin='%s'",$this->VMin);
		if( is_int($this->HMin))$xml_attr .= sprintf(" HMin='%s'",$this->HMin);
		if( is_int($this->VMax))$xml_attr .= sprintf(" VMax='%s'",$this->VMax);
		if( is_int($this->HMax))$xml_attr .= sprintf(" HMax='%s'",$this->HMax);
		if($this->needed != false)
			$xml_attr .= " needed='1'";
		return $xml_attr;
	}

	/**
	 * Retourne le contenu XML du composant
	 *
	 * @return string
	 */
	public function getReponseXML() {
		$xml_text = sprintf("\t<%s %s>",$this->_componentIdent,$this->_attributs());
		$xml_text = $xml_text.$this->_getContent();
		$xml_text = $xml_text. sprintf("</%s>\n",$this->_componentIdent);
		return $xml_text;
	}
}

/**
 * Composant Tab permettant de changer d'onglet.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Tab extends Xfer_Component {
	/**
	 * Constructeur
	 *
	 * @return Xfer_Comp_Tab
	 */
	public function __construct() {
		parent::__construct("");
		$this->_componentIdent = "TAB";
	}

	/**
	* Nom/description de l'onglet
	*
	* @param string $value
	*/
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	/**
	 * Retourne le contenu XML du composant
	 *
	 * @return string
	 */
	/*	public function getReponseXML()
	{
		$xml_text=sprintf("\t<%s>",$this->_componentIdent);
		$xml_text=$xml_text.$this->_getContent();
		$xml_text=$xml_text.sprintf("</%s>\ n",$this->_componentIdent);
		return $xml_text;
	}*/

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant gérant l'affichage d'un texte brute.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Label extends Xfer_Component {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Label
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "LABEL";
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant gérant l'affichage d'une image
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Image extends Xfer_Component {
	/**
	 * Type du format (liens fichier si vide)
	 *
	 * @var string
	 */
	public $m_type = "";

	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Label
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "IMAGE";
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($icon,$type = "") {
		$this->m_type = $type;
		if($type == "") {
			global $extension;
			global $rootPath;
			if(!isset($rootPath))$rootPath = "";
			if( is_file($rootPath."extensions/$extension/images/$icon"))
				$this->m_value = $rootPath."extensions/$extension/images/$icon";
			else if( is_file($rootPath."images/$icon"))
				$this->m_value = $rootPath."images/$icon";
			else if( is_file($rootPath.$icon))
				$this->m_value = $rootPath.$icon;
			else
				$this->m_value = "";
		}
		else
		// envoie en flux text
		$this->m_value = $icon;
	}

	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		$size = filesize($this->m_value);
		$image_info = getImageSize($this->m_value);
		list($width,$height) = $image_info;
		$xml_attr = sprintf("%s size='%d' height='%d' width='%d' ",$xml_attr,$size,$height,$width);
		return $xml_attr;
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<TYPE><![CDATA[%s]]></TYPE><![CDATA[%s]]>",$this->m_type,$this->m_value);
	}
}
/**
 * Composant gérant l'affichage d'un label avec hyper-liens
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_LinkLabel extends Xfer_Component {
	/**
	 * Valeur de l'hyper-liens
	 *
	 * @var string
	 */
	public $m_Link = "";

	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Label
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "LINK";
		global $LINK_LABEL_MANAGER;
		if ($LINK_LABEL_MANAGER!="")
			require_once($LINK_LABEL_MANAGER);
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setEmailFromGrid($grid,$FieldName) {
		$mails = "";
		foreach($grid->m_records as $rec) {
			$rec[$FieldName] = trim($rec[$FieldName]);
			if($rec[$FieldName] != "" && $rec[$FieldName] == htmlentities($rec[$FieldName])) {
				if($mails == "")
					$mails .= $rec[$FieldName];
				else $mails .= ','.$rec[$FieldName];
			}
		}
		global $MAILTO_TYPE;
		switch ($MAILTO_TYPE) {
			case 1: // CC
				$this->setLink('mailto:?cc='.$mails);
				break;
			case 2: //BCC
				$this->setLink('mailto:?bcc='.$mails);
				break;
			default: //TO
				$this->setLink('mailto:'.$mails);
		}
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setFileToLoad($filename) {
		$http_referer=$_SERVER["HTTP_REFERER"];
		$protocol=substr($http_referer,0,strpos($http_referer,'://'));
		$protocol=($protocol=='')?'http':$protocol;
		$server_name = $_SERVER["SERVER_NAME"];
		$server_port = $_SERVER["SERVER_PORT"];
		$server_dir = $_SERVER["PHP_SELF"];
		$pos = strrpos($server_dir,'/')+1;
		$server_dir = substr($server_dir,0,$pos);
		$this->setLink( sprintf('%s://%s:%d%s%s',$protocol,$server_name,$server_port,$server_dir,$filename));
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setLink($value) {
		$this->m_Link = trim($value);
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>\n<LINK><![CDATA[%s]]></LINK>\n",$this->m_value,$this->m_Link);
	}
}
/**
 * Composant gérant l'affichage d'un texte formaté.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_LabelForm extends Xfer_Component {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_LabelForm
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "LABELFORM";
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}

/**
 * Abstration d'un composant associé à une action déclanché par un événement.
 * Utiliser concrètement, il se comporte comme un bouton.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Button extends Xfer_Component {
	/**
	 * Action associé au bouton
	 *
	 * @var Xfer_Action
	 */var$m_action = null;
	/**
	 * JavaScript
	 *
	 * @var String
	 */var$JavaScript = "";
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Button
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "BUTTON";
	}
	/**
	 * change l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	public function setAction($action) {
		if($this->checkActionRigth($action)) {
			$this->m_action = $action;
		}
	}
	/**
	 * Contenu de l'action du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getActionContent() {
		$xml_text = "<ACTIONS>";
		if($this->m_action != null) {
			$action = $this->m_action;
			$xml_text = $xml_text.$action->getReponseXML();
		}
		$xml_text = $xml_text."</ACTIONS>";
		return $xml_text;
	}


	/**
	 * Valeur retourné par un clique button
	 *
	 * @var unknown_type
	 */
	public $m_clickvalue="";

	/**
	 * Nom retourné par un clique button
	 *
	 * @var unknown_type
	 */
	public $m_clickname="";

	/**
	 * change l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	public function setClickInfo($clickname,$clickvalue) {
		$this->m_clickname = "$clickname";
		$this->m_clickvalue = "$clickvalue";
	}

	/**
	 * Présise si le bouton doit être "mini"
	 *
	 * @var unknown_type
	 */
	public $m_isMini=false;

	/**
	 * change l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	public function setIsMini($isMini) {
		if ($isMini)
			$this->m_isMini = true;
		else
			$this->m_isMini = false;
	}

	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent::_attributs();
		if ($this->m_clickname!='')
			$xml_attr = sprintf("%s clickname='%s' clickvalue='%s' ",$xml_attr,$this->m_clickname,$this->m_clickvalue);
		if ($this->m_isMini)
			$xml_attr.= " isMini='1' ";
		return $xml_attr;
	}

	/**
	 * Retourne le contenu XML du composant
	 *
	 * @return string
	 */
	public function getReponseXML() {
		$xml_text = sprintf("\t<%s %s>",$this->_componentIdent,$this->_attributs());
		$xml_text = $xml_text.$this->_getContent();
		$xml_text = $xml_text.$this->_getActionContent();
		if(strlen($this->JavaScript)>0)
			$xml_text = $xml_text. sprintf("<JavaScript><![CDATA[%s]]></JavaScript>\n", urlencode($this->JavaScript));
		$xml_text = $xml_text. sprintf("</%s>\n",$this->_componentIdent);
		return $xml_text;
	}
}
/**
 * Composant gérant une zone d'édition mono-ligne brute.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Edit extends Xfer_Comp_Button {
	var$ExprReg = "";
	var$StringSize = 0;
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Edit
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "EDIT";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		if( is_int($this->StringSize) && ($this->StringSize != 0))
			$xml_attr .= " stringSize='".$this->StringSize."'";
		return $xml_attr;
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$content = sprintf("<![CDATA[%s]]>",$this->m_value);
		if( is_string($this->ExprReg) && ($this->ExprReg != ""))
			$content .= sprintf("<REG_EXPR><![CDATA[%s]]></REG_EXPR>",$this->ExprReg);
		return $content;
	}
}
/**
 * Composant géarnt la saisie d'une date
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Date extends Xfer_Comp_Button {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Date
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "DATE";
	}
	/**
	 * Change la date sous la forme "YYYY-MM-DD"
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant géarnt la saisie d'une heure.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Time extends Xfer_Comp_Button {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Time
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "TIME";
	}
	/**
	 * Change la heure sous la forme "HH:mm:ss"
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant géarnt la saisie d'une date et d'une heure.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_DateTime extends Xfer_Comp_Button {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_DateTime
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "DATETIME";
	}
	/**
	 * Change la date+heure sous la forme "YYYY-MM-DD HH:mm:ss"
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant gérant la saisie d'un mot de passe.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Passwd extends Xfer_Comp_Button {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Passwd
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "PASSWD";
	}
	/**
	 * Change la valeur par défaut.
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant gérant une zone d'édition multiligne brute.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Memo extends Xfer_Comp_Button {
	var$FirstLine = -1;
	var$Encode = false;
	var$StringSize = 0;
	var$SubMenu = array();
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Memo
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "MEMO";
		$this->HMin = 200;
		$this->VMin = 50;
		$this->FirstLine = -1;
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Ajoute un sous menu
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $value
	 */
	public function addSubMenu($type,$name,$value) {
		$this->SubMenu[] = array($type,$name,$value);
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		$xml_attr .= " FirstLine='".$this->FirstLine."'";
		if($this->Encode != false)$xml_attr .= " Encode='1'";
		if( is_int($this->StringSize) && ($this->StringSize != 0))
			$xml_attr .= " stringSize='".$this->StringSize."'";
		return $xml_attr;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$content = sprintf("<![CDATA[%s]]>\n",$this->m_value);
		foreach($this->SubMenu as $menu) {
			$content .= "<SUBMENU>
";
			$content .= sprintf("<TYPE><![CDATA[%s]]></TYPE>", urlencode($menu[0]));
			$content .= sprintf("<NAME><![CDATA[%s]]></NAME>", urlencode($menu[1]));
			$content .= sprintf("<VALUE><![CDATA[%s]]></VALUE>", urlencode($menu[2]));
			$content .= "</SUBMENU>\n";
		}
		return $content;
	}
}
/**
 * Composant gérant une zone d'édition multiligne formaté.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_MemoForm extends Xfer_Comp_Button {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_MemoForm
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "MEMOFORM";
		$this->HMin = 250;
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant gérant un zone de saisie d'un réel.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Float extends Xfer_Comp_Button {
	var$m_min = 0.0;
	var$m_max = 10000.0;
	var$m_prec = 2;
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Float
	 */
	public function __construct($name,$min = 0.00,$max = 10000.00,$prec = 2) {
		parent::__construct($name);
		$this->_componentIdent = "FLOAT";
		$this->m_min = (double)$min;
		$this->m_max = (double)$max;
		$this->m_prec = (int)$prec;
	}
	/**
	 * Change la valeur réel
	 *
	 * @param real $value
	 */
	public function setValue($value) {
		$value = (double)$value;
		$this->m_value = $value;
	}
	/**
	 * Retourne le format de précision
	 *
	 * @access private
	 * @return string
	 */
	protected function _getFormat() {
		return "%.".$this->m_prec."f";
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$value = Max($this->m_min,$this->m_value);
		$value = Min($this->m_max,$value);
		$xml_text = "";
		$xml_text = $xml_text. sprintf("<![CDATA[".$this->_getFormat()."]]>",$value);
		$xml_text = $xml_text;
		return $xml_text;
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		$xml_attr = sprintf("%s min='".$this->_getFormat()."' max='".$this->_getFormat()."' prec='%d'",$xml_attr,$this->m_min,$this->m_max,$this->m_prec);
		return $xml_attr;
	}
}
/**
 * Composant gérant une case à cocher.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Check extends Xfer_Comp_Button {
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Float
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "CHECK";
	}
	/**
	 * Change la valeur
	 *
	 * @param boolean $value
	 */
	public function setValue($value) {
		$this->m_value = $value;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Composant gérant une boite de selection simple.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Select extends Xfer_Comp_Button {
	/**
	 * Liste de la selection
	 *
	 * @var array
	 */
	public $m_select = array();
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Select
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "SELECT";
	}
	/**
	 * Change la sélection par défaut
	 *
	 * @param integer $value
	 */
	public function setValue($value) {
		$this->m_value = $value;
	}
	/**
	 * Change la liste de selection
	 *
	 * @param array $select
	 */
	public function setSelect($select = array()) {
		$this->m_select = $select;
	}
	/**
	 * Remplir par le liste des classes fille
	 *
	 * @param string $MotherClassName
	 * @param string $ClassName
	 * @param bool $IncludeMother
	 */
	public function fillByDaughterList($MotherClassName,$ClassName,$IncludeMother) {
		require_once('CORE/extensionManager.inc.php');
		$select = getDaughterClassesList($MotherClassName,'', true,$IncludeMother);
		if(! array_key_exists($ClassName,$select)) {
			$ClassName = '';
			$keys = array_keys($select);
			if(count($keys)>0)
				$ClassName = $keys[0];
		}
		$this->setSelect($select);
		$this->setValue($ClassName);
		return $ClassName;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$xml_text = sprintf("%s",$this->m_value);
		foreach($this->m_select as $key => $value)
			$xml_text = $xml_text.sprintf("<CASE id='%s'><![CDATA[%s]]></CASE>",$key,$value);
		return $xml_text;
	}
}
/**
 * Composant gérant une boite de selection multiple.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_CheckList extends Xfer_Comp_Button {
	/**
	 * Liste de selection
	 *
	 * @var array
	 */
	public $m_select = array();

	/**
	 * La selection est unique
	 *
	 * @var boolean
	 */
	public $simple = false;

	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_CheckList
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "CHECKLIST";
	}
	/**
	 * Change la liste de selection
	 *
	 * @param array $select
	 */
	public function setSelect($select = array()) {
		$this->m_select = $select;
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		$xml_attr = sprintf("%s simple='%d'",$xml_attr,$this->simple);
		return $xml_attr;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$xml_text = "";
		foreach($this->m_select as $key => $value) {
			$att_checked = " checked='0'";
			if( is_array($this->m_value))
				foreach($this->m_value as $select)
					if("$select" == "$key")
						$att_checked = " checked='1'";
			$xml_text = $xml_text. sprintf("<CASE id='%s'%s><![CDATA[%s]]></CASE>",$key,$att_checked,$value);
		}
		return $xml_text;
	}
}

/**
 * Composant gérant le téléchargement d'un fichier
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_UpLoad extends Xfer_Component {
	/**
	 * Liste des filtre
	 *
	 * @var array
	 */
	public $m_fitre = array();

	/**
	 * Le fichier est transmis compressé
	 *
	 * @var string
	 */
	public $compress=false;

	/**
	 * Le fichier n'est pas transmis en Base64
	 *
	 * @var boolean
	 */
	public $HttpFile=false;

	/**
	 * Taille maximal à télécharger
	 *
	 * @var int
	 */
	public $maxsize=1048576;

	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_UpLoad
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "UPLOAD";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	public function addFilter($newfiltre) {
		$this->m_fitre[] = $newfiltre;
	}

	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		if($this->compress==true)
			$xml_attr .= " Compress='".$this->compress."'";
		if ($this->HttpFile==true)
			$xml_attr .= " HttpFile='1'";
		$xml_attr .= " maxsize='$this->maxsize'";
		return $xml_attr;
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$content = sprintf("<![CDATA[%s]]>",$this->m_value);
		foreach($this->m_fitre as $current_fitre)
			$content .= sprintf("<FILTER><![CDATA[%s]]></FILTER>",$current_fitre);
		return $content;
	}
}

/**
 * Composant gérant l'extraction d'un fichier
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_DownLoad extends Xfer_Comp_Button {

	/**
	 * Nom du fichier à extraire
	 *
	 * @var string
	 */
	public $m_FileName="";

	/**
	 * Le fichier est transmis compressé
	 *
	 * @var boolean
	 */
	public $compress=false;

	/**
	 * Le fichier n'est pas transmis en Base64
	 *
	 * @var boolean
	 */
	public $HttpFile=false;

	/**
	 * Taille maximal à télécharger
	 *
	 * @var int
	 */
	public $maxsize=1048576;

	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_DownLoad
	 */
	public function __construct($name) {
		parent::__construct($name);
		$this->_componentIdent = "DOWNLOAD";
	}

	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->m_value = trim($value);
	}

	/**
	 * Change le fichier à télécharger
	 *
	 * @param string $filename
	 */
	public function setFileName($filename) {
		$this->m_FileName = trim($filename);
	}

	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	protected function _attributs() {
		$xml_attr = parent:: _attributs();
		if($this->compress==true)
			$xml_attr .= " Compress='".$this->compress."'";
		if ($this->HttpFile==true)
			$xml_attr .= " HttpFile='1'";
		$xml_attr .= " maxsize='$this->maxsize'";
		return $xml_attr;
	}

	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent() {
		$content = sprintf("<![CDATA[%s]]>",$this->m_value);
		$content.= sprintf("<FILENAME><![CDATA[%s]]></FILENAME>",$this->m_FileName);
		return $content;
	}
}
//@END@
?>
