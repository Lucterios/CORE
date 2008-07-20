<?php
// library file write by SDK tool
// --- Last modification: Date 20 June 2008 13:26:55 By  ---

//@BEGIN@
/**
 * fichier gérant des composants pour une fenêtre personnalisée
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer

 */require_once'xfer.inc.php';
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
	 */var$_component;
	/**
	 * Identifiant du composant
	 *
	 * @var string
	 */var$m_name;
	/**
	 * Valeur véhiculée par le composant
	 *
	 * @var unknown_type
	 */var$m_value;
	/**
	 * Identifiant de l'onglet associé. 0=Hors onglet
	 *
	 * @var integer
	 */var$tab;
	/**
	 * position horizontal
	 *
	 * @var integer
	 */var$x;
	/**
	 * position vertical
	 *
	 * @var integer
	 */var$y;
	/**
	 * Encombrement horizontal. 1 par defaut
	 *
	 * @var integer
	 */var$colspan;
	/**
	 * Encombrement vertical. 1 par defaut.
	 *
	 * @var integer
	 */var$rowspan;
	/**
	 * Description du composant
	 *
	 * @var string
	 */var$m_description;
	/**
	 * Précise si le champ est obligatoire. False par defaut.
	 *
	 * @var boolean
	 */var$needed;
	/**
	 * Taille vertical minimum
	 *
	 * @var integer
	 */var$VMin;
	/**
	 * Taille horizontal minimum
	 *
	 * @var integer
	 */var$HMin;
	/**
	 * Taille vertical maximum
	 *
	 * @var integer
	 */var$VMax;
	/**
	 * Taille horizontal maximum
	 *
	 * @var integer
	 */var$HMax;
	/**
	 * Constructeur
	 *
	 * @param string $name identifiant du composant
	 * @return Xfer_Component
	 */
	function Xfer_Component($name) {
		$this->Xfer_Object();
		$this->m_name = $name;
		$this->_component = "";
		//$component;
		$this->m_value = "";
		$this->tab = 0;
		$this->x = "";
		$this->y = "";
		$this->VMin = "";
		$this->HMin = "";
		$this->VMax = "";
		$this->HMax = "";
		$this->colspan = "";
		$this->rowspan = "";
		$this->m_description = "";
		$this->needed = false;
	}
	/**
	 * Change la position en l'encombrement du composant
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $colspan
	 * @param integer $row
	 */
	function setLocation($x,$y,$colspan = 1,$rowspan = 1) {
		$this->x = $x;
		$this->y = $y;
		$this->colspan = $colspan;
		$this->rowspan = $rowspan;
	}
	/**
	*  Change la taille min/max du composant
	*
	* @param integer $VMin
	* @param integer $HMin
	* @param integer $VMax
	* @param integer $HMax
	*/
	function setSize($VMin,$HMin,$VMax = "",$HMax = "") {
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
	function setValue($value) {
		$this->m_value = $value;
	}
	/**
	 * Change l'état obligatoire du composant
	 *
	 * @param boolean $needed
	 */
	function setNeeded($needed) {
		$this->needed = $needed;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		return $this->m_value;
	}
	/**
	 * Retourne l'identification du composant pour ordonnancer un ensemble de composants
	 *
	 * @return string
	 */
	function getId() {
		$text = "";
		if( is_int($this->tab))$text .= sprintf("tab=%4d",(int)$this->tab);
		else $text .= sprintf("tab=%4d",0);
		if( is_int($this->y) && ($this->y >= 0))$text .= sprintf("_y=%4d",(int)$this->y);
		else $text .= "_y=    ";
		if( is_int($this->x) && ($this->x >= 0))$text .= sprintf("_x=%4d",(int)$this->x);
		else $text .= "_x=    ";
		return $text;
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	function _attributs() {
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
		if($this->needed != false)$xml_attr .= " needed='1'";
		return $xml_attr;
	}
	/**
	 * Retourne le contenu XML du composant
	 *
	 * @return string
	 */
	function getReponseXML() {
		$xml_text = sprintf("\t<%s %s>",$this->_component,$this->_attributs());
		$xml_text = $xml_text.$this->_getContent();
		$xml_text = $xml_text. sprintf("</%s>\n",$this->_component);
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
	function Xfer_Comp_Tab() {
		$this->Xfer_Component("");
		$this->_component = "TAB";
	}
	/**
	* Nom/description de l'onglet
	*
	* @param string $value
	*/
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Retourne le contenu XML du composant
	 *
	 * @return string
	 */
	/*	function getReponseXML()
	{
		$xml_text=sprintf("\t<%s>",$this->_component);
		$xml_text=$xml_text.$this->_getContent();
		$xml_text=$xml_text.sprintf("</%s>\ n",$this->_component);
		return $xml_text;
	}*/
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_Label($name) {
		$this->Xfer_Component($name);
		$this->_component = "LABEL";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	 */var$m_type = "";
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Label
	 */
	function Xfer_Comp_Image($name) {
		$this->Xfer_Component($name);
		$this->_component = "IMAGE";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($icon,$type = "") {
		$this->m_type = $type;
		if($type == "") {
			global $extension;
			if( is_file("extensions/$extension/images/$icon"))$this->m_value = "extensions/$extension/images/$icon";
			else if( is_file("images/$icon"))$this->m_value = "images/$icon";
			else if( is_file($icon))$this->m_value = $icon;
			else $this->m_value = "";
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
	function _attributs() {
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
	function _getContent() {
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
	 */var$Link = "";
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Label
	 */
	function Xfer_Comp_LinkLabel($name) {
		$this->Xfer_Component($name);
		$this->_component = "LINK";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setEmailFromGrid($grid,$FieldName) {
		$mails = "";
		foreach($grid->m_records as $rec) {
			$rec[$FieldName] = trim($rec[$FieldName]);
			if($rec[$FieldName] != "" && $rec[$FieldName] == htmlentities($rec[$FieldName])) {
				if($mails == "")$mails .= $rec[$FieldName];
				else $mails .= ','.$rec[$FieldName];
			}
		}
		$this->setLink('mailto:'.$mails);
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setFileToLoad($filename) {
		$server_name = $_SERVER["SERVER_NAME"];
		$server_port = $_SERVER["SERVER_PORT"];
		$server_dir = $_SERVER["PHP_SELF"];
		$pos = strpos($server_dir,'coreIndex.php');
		$server_dir = substr($server_dir,0,$pos);
		$this->setLink( sprintf('http://%s:%d%s%s',$server_name,$server_port,$server_dir,$filename));
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setLink($value) {
		$this->m_Link = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		return sprintf("<LINK><![CDATA[%s]]></LINK>\n<![CDATA[%s]]>",$this->m_Link,$this->m_value);
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
	function Xfer_Comp_LabelForm($name) {
		$this->Xfer_Component($name);
		$this->_component = "LABELFORM";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		return sprintf("<![CDATA[%s]]>",$this->m_value);
	}
}
/**
 * Classe gérant un entête d'un composant Xfer_Comp_Grid
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Header extends Xfer_Object {
	/**
	 * nom identifiant de la colonne
	 *
	 * @var string
	 */var$m_name = "";
	/**
	 * Description de la colonne
	 *
	 * @var string
	 */var$m_descript = "";
	/**
	 * type de la colonne
	 *
	 * @var string
	 */var$m_type = "";
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @param string $descript
	 * @param string $type
	 * @return Xfer_Comp_Header
	 */
	function Xfer_Comp_Header($name,$descript,$type = "") {
		$this->PEAR('Observer_Error');
		$this->m_name = $name;
		$this->m_descript = $descript;
		$this->m_type = $type;
	}
	/**
	 * Retourne le contenu XML de l'entête
	 *
	 * @return string
	 */
	function getReponseXML() {
		$xml_text = sprintf("<HEADER name='%s'",$this->m_name);
		if($this->m_type != "")$xml_text = $xml_text. sprintf(" type='%s'",$this->m_type);
		$xml_text = $xml_text. sprintf("><![CDATA[%s]]></HEADER>",$this->m_descript);
		return $xml_text;
	}
}
/**
 * Composant gérant une grille/un tableau.
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Comp_Grid extends Xfer_Component {
	/**
	 * Liste d'entêtes
	 *
	 * @var array
	 */var$m_headers = array();
	/**
	 * Liste de cellules
	 *
	 * @var array
	 */var$m_records = array();
	/**
	 * Liste d'actions
	 *
	 * @var array
	 */var$m_actions = array();
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Grid
	 */
	function Xfer_Comp_Grid($name) {
		$this->Xfer_Component($name);
		$this->_component = "GRID";
	}
	/**
	 * Ajoute un entête de colonne dans une grille
	 *
	 * @param string $name
	 * @param array $descript
	 * @param string $type int,float,bool,str
	 */
	function addHeader($name,$descript,$type = "") {
		$new_obs = & new Xfer_Comp_Header($name,$descript,$type);
		$this->m_headers[$name] = $new_obs;
	}
	/**
	 * Ajoute l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	function addAction($action) {
		if($this->checkActionRigth($action)) { array_push($this->m_actions,$action);
		}
	}
	/**
	 * Ajoute un entête de colonne dans une grille
	 *
	 * @param string $FieldName
	 * @param array $desc_fld
	 * @param integer $type_fld 0:entier - 1:réel - 3:booléen - autre:chaine
	 */
	function newHeader($FieldName,$desc_fld,$type_fld) {
		switch($type_fld) {
		case 0:
			$type_col = "int";
			break;
		case 1:
			$type_col = "float";
			break;
		case 3:
			$type_col = "bool";
			break;
		case 100:
			$type_col = "icon";
			break;
		default :
			$type_col = "str";
			break;
		}
		$this->addHeader($FieldName,$desc_fld,$type_col);
	}
	/**
	 * Rempli l'entête d'une grille avec la déscription de l'objet $DBObjs et les champs décrits.
	 *
	 * @param DBObj_Basic $DBObjs
	 * @param null|string|array $FieldNames
	 * @param string $RefTableName
	 */
	function setDBObjectHeader($DBObjs,$FieldNames = null,$RefTableName = "") {
		$field_desc = $DBObjs->getDBMetaDataField();
		foreach($FieldNames as $FieldName) {
			if( array_key_exists($FieldName,$field_desc)) {
				$field_item = $field_desc[$FieldName];
				$type_fld = $field_item['type'];
				$desc_fld = $field_item['description'];
				$this->newHeader($FieldName,$desc_fld,$type_fld);
			}
			else {
				$pos = strpos($FieldName,"[");
				if($pos === false) {
					$type_fld = 2;
					$pos = strpos($FieldName, SEP_SHOW);
					if($pos === false)$desc_fld = "";
					else $desc_fld = substr($FieldName,0,$pos);
					$this->newHeader($FieldName,$desc_fld,$type_fld);
				}
				else {
					$new_field_name = substr($FieldName,0,$pos);
					if( array_key_exists($new_field_name,$field_desc)) {
						$new_FieldNames = split(',', substr($FieldName,$pos+1,-1));
						$this->setDBObjectHeader($DBObjs->getField($new_field_name),$new_FieldNames);
					}
				}
			}
		}
	}
	/**
	 * Rempli une cellule dans une grille avec la déscription de l'objet $DBObjs et le champ décrit.
	 *
	 * @param integer $NewId
	 * @param DBObj_Basic $DBObjs
	 * @param string $FieldName
	 * @param array $field_desc
	 */
	function setDBObjectData($NewId,$DBObjs,$FieldName,$field_desc) {
		$data = $DBObjs->getField($FieldName);
		if( array_key_exists($FieldName,$field_desc))$type_fld = $field_desc[$FieldName]['type'];
		else $type_fld = 2;
		switch($type_fld) {
		case 3:
			//Bool
			if($data != false)$val = "Oui";
			else $val = "Non";
			break;
		case 4:
			//Date
			$val = convertDate($data);
			break;
		case 5:
			//time
			$val = convertTime($data);
			break;
		case 9:
			//Childs
			$val = "";
			while($data->fetch())$val .= $data->toText()."{[newline]}";
			$val = trim($val);
			break;
		case 10:
			//Ref
			$val = $data->toText();
			break;
		default :
			$val = $data;
			break;
		}
		$this->setValue($NewId,$FieldName,$val);
	}
	/**
	 * Remplis une grille avec la déscription de l'objet $DBObjs et les champs décrits.
	 *
	 * @param DBObj_Basic $DBObjs
	 * @param null|string|array $FieldNames
	 * @param string $RefTableName
	 */
	function setDBObject($DBObjs,$FieldNames = null,$RefTableName = "") {
		if( is_int($FieldNames))$FieldNames = $DBObjs->getFieldEditable($RefTableName,(int)$FieldNames);
		else if($FieldNames == null)$FieldNames = $DBObjs->getFieldEditable($RefTableName);
		$this->setDBObjectHeader($DBObjs,$FieldNames,$RefTableName);
		$field_desc = $DBObjs->getDBMetaDataField();
		while($DBObjs->fetch()) {
			foreach($FieldNames as $FieldName) {
				$pos = strpos($FieldName,"[");
				if($pos === false)$this->setDBObjectData($DBObjs->id,$DBObjs,$FieldName,$field_desc);
				else {
					$new_field_name = substr($FieldName,0,$pos);
					if( array_key_exists($new_field_name,$field_desc)) {
						$new_obj = $DBObjs->getField($new_field_name);
						$new_FieldNames = split(',', substr($FieldName,$pos+1,-1));
						$new_field_desc = $new_obj->__DBMetaDataField;
						foreach($new_FieldNames as $new_FieldName)$this->setDBObjectData($DBObjs->id,$new_obj,$new_FieldName,$new_field_desc);
					}
				}
			}
		}
	}
	/**
	 * Crée une nouvelle ligne dans la grille
	 *
	 * @access private
	 * @param integer $id
	 */
	function _newRecord($id) {
		if(! array_key_exists($id,$this->m_records)) {
			$new_record = array();
			foreach($this->m_headers as $header) {
				switch($header->m_type) {
				case 'int':
					$new_record[$header->m_name] = 0;
					break;
				case 'float':
					$new_record[$header->m_name] = 0.0;
					break;
				case 'bool':
					$new_record[$header->m_name] = (bool)0;
					break;
				default :
					$new_record[$header->m_name] = "";
					break;
				}
			}
			$this->m_records[$id] = $new_record;
		}
	}
	/**
	 * Change la valeur de la colonne $name de ma ligne identifiée par $id
	 *
	 * @param integer $id
	 * @param string $name
	 * @param string $value
	 */
	function setValue($id,$name,$value) {
		$this->_newRecord($id);
		$this->m_records[$id][$name] = $value;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		$xml_text = "";
		foreach($this->m_headers as $header)$xml_text = $xml_text.$header->getReponseXML();
		foreach($this->m_records as $key => $record) {
			$xml_text = $xml_text. sprintf("<RECORD id='%s'>",$key);
			foreach($record as $name => $value)$xml_text = $xml_text. sprintf("<VALUE name='%s'><![CDATA[%s]]></VALUE>",$name,$value);
			$xml_text = $xml_text."</RECORD>";
		}
		if( count($this->m_actions) != 0) {
			$xml_text = $xml_text."<ACTIONS>";
			foreach($this->m_actions as $action)$xml_text = $xml_text.$action->getReponseXML();
			$xml_text = $xml_text."</ACTIONS>";
		}
		return $xml_text;
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
	function Xfer_Comp_Button($name) {
		$this->Xfer_Component($name);
		$this->_component = "BUTTON";
	}
	/**
	 * change l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	function setAction($action) {
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
	function _getActionContent() {
		$xml_text = "<ACTIONS>";
		if($this->m_action != null) {
			$action = $this->m_action;
			$xml_text = $xml_text.$action->getReponseXML();
		}
		$xml_text = $xml_text."</ACTIONS>";
		return $xml_text;
	}
	/**
	 * Retourne le contenu XML du composant
	 *
	 * @return string
	 */
	function getReponseXML() {
		$xml_text = sprintf("\t<%s %s>",$this->_component,$this->_attributs());
		$xml_text = $xml_text.$this->_getContent();
		$xml_text = $xml_text.$this->_getActionContent();
		if( strlen($this->JavaScript)>0)$xml_text = $xml_text. sprintf("<JavaScript><![CDATA[%s]]></JavaScript>\n", urlencode($this->JavaScript));
		$xml_text = $xml_text. sprintf("</%s>\n",$this->_component);
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
	function Xfer_Comp_Edit($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "EDIT";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	function _attributs() {
		$xml_attr = parent:: _attributs();
		if( is_int($this->StringSize) && ($this->StringSize != 0))$xml_attr .= " stringSize='".$this->StringSize."'";
		return $xml_attr;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		$content = sprintf("<![CDATA[%s]]>",$this->m_value);
		if( is_string($this->ExprReg) && ($this->ExprReg != ""))$content .= sprintf("<REG_EXPR><![CDATA[%s]]></REG_EXPR>",$this->ExprReg);
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
	function Xfer_Comp_Date($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "DATE";
	}
	/**
	 * Change la date sous la forme "YYYY-MM-DD"
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_Time($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "TIME";
	}
	/**
	 * Change la heure sous la forme "HH:mm:ss"
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_DateTime($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "DATETIME";
	}
	/**
	 * Change la date+heure sous la forme "YYYY-MM-DD HH:mm:ss"
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_Passwd($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "PASSWD";
	}
	/**
	 * Change la valeur par défaut.
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_Memo($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "MEMO";
		$this->HMin = 200;
		$this->VMin = 50;
		$this->FirstLine = -1;
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Ajoute un sous menu
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $value
	 */
	function addSubMenu($type,$name,$value) {
		$this->SubMenu[] = array($type,$name,$value);
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	function _attributs() {
		$xml_attr = parent:: _attributs();
		$xml_attr .= " FirstLine='".$this->FirstLine."'";
		if($this->Encode != false)$xml_attr .= " Encode='1'";
		if( is_int($this->StringSize) && ($this->StringSize != 0))$xml_attr .= " stringSize='".$this->StringSize."'";
		return $xml_attr;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_MemoForm($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "MEMOFORM";
		$this->HMin = 250;
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	function Xfer_Comp_Float($name,$min = 0.00,$max = 10000.00,$prec = 2) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "FLOAT";
		$this->m_min = (double)$min;
		$this->m_max = (double)$max;
		$this->m_prec = (int)$prec;
	}
	/**
	 * Change la valeur réel
	 *
	 * @param real $value
	 */
	function setValue($value) {
		$value = (double)$value;
		$value = Max($this->m_min,$value);
		$value = Min($this->m_max,$value);
		$this->m_value = $value;
	}
	/**
	 * Retourne le format de précision
	 *
	 * @access private
	 * @return string
	 */
	function _getFormat() {
		return "%.".$this->m_prec."f";
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		$xml_text = "";
		$xml_text = $xml_text. sprintf("<![CDATA[".$this->_getFormat()."]]>",$this->m_value);
		$xml_text = $xml_text;
		return $xml_text;
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	function _attributs() {
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
	function Xfer_Comp_Check($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "CHECK";
	}
	/**
	 * Change la valeur
	 *
	 * @param boolean $value
	 */
	function setValue($value) {
		$this->m_value = $value;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
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
	 */var$m_select = array();
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_Select
	 */
	function Xfer_Comp_Select($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "SELECT";
	}
	/**
	 * Change la sélection par défaut
	 *
	 * @param integer $value
	 */
	function setValue($value) {
		$this->m_value = $value;
	}
	/**
	 * Change la liste de selection
	 *
	 * @param array $select
	 */
	function setSelect($select = array()) {
		$this->m_select = $select;
	}
	/**
	 * Remplir par le liste des classes fille
	 *
	 * @param string $MotherClassName
	 * @param string $ClassName
	 * @param bool $IncludeMother
	 */
	function fillByDaughterList($MotherClassName,$ClassName,$IncludeMother) {
		require_once('CORE/extensionManager.inc.php');
		$select = getDaughterClassesList($MotherClassName,'', true,$IncludeMother);
		if(! array_key_exists($ClassName,$select)) {
			$ClassName = '';
			$keys = array_keys($select);
			if( count($keys)>0)$ClassName = $keys[0];
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
	function _getContent() {
		$xml_text = sprintf("%s",$this->m_value);
		foreach($this->m_select as $key => $value)$xml_text = $xml_text. sprintf("<CASE id='%s'><![CDATA[%s]]></CASE>",$key,$value);
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
	 */var$m_select = array();
	/**
	 * Liste de selection
	 *
	 * @var array
	 */var$simple = false;
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_CheckList
	 */
	function Xfer_Comp_CheckList($name) {
		$this->Xfer_Comp_Button($name);
		$this->_component = "CHECKLIST";
	}
	/**
	 * Change la liste de selection
	 *
	 * @param array $select
	 */
	function setSelect($select = array()) {
		$this->m_select = $select;
	}
	/**
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	function _attributs() {
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
	function _getContent() {
		$xml_text = "";
		foreach($this->m_select as $key => $value) {
			$att_checked = " checked='0'";
			if( is_array($this->m_value))foreach($this->m_value as $select)if("$select" == "$key")$att_checked = " checked='1'";
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
	 */var$m_fitre = array();
	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @return Xfer_Comp_UpLoad
	 */
	function Xfer_Comp_UpLoad($name) {
		$this->Xfer_Component($name);
		$this->_component = "UPLOAD";
	}
	/**
	 * Change le text contenu
	 *
	 * @param string $value
	 */
	function setValue($value) {
		$this->m_value = trim($value);
	}
	
	function addFilter($newfiltre) {
		$this->m_fitre[] = $newfiltre;
	}
	/**
	 * Contenu du composant
	 *
	 * @access private
	 * @return string
	 */
	function _getContent() {
		$content = sprintf("<![CDATA[%s]]>",$this->m_value);
		foreach($this->m_fitre as $current_fitre)$content .= sprintf("<FILTER><![CDATA[%s]]></FILTER>",$current_fitre);
		return $content;
	}
}

//@END@
?>
