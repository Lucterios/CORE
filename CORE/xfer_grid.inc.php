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
// --- Last modification: Date 03 February 2010 9:28:01 By  ---

//@BEGIN@
/**
 * fichier gérant des composants pour une fenêtre personnalisée
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer

 */

require_once'xfer.inc.php';
require_once'xfer_component.inc.php';

define('MAX_GRID_RECORD',25);
define('GRID_PAGE','GRID_PAGE%');

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
	 */
	var $m_name = "";

	/**
	 * Description de la colonne
	 *
	 * @var string
	 */
	var $m_descript = "";

	/**
	 * type de la colonne
	 *
	 * @var string
	 */
	var $m_type = "";

	/**
	 * chaine à évaluer pour l'enregistrement
	 *
	 * @var string
	 */
	var $m_formula = "";

	/**
	 * Nom de la fonction
	 *
	 * @var string
	 */
	var $m_functionName = "";

	/**
	 * Constructeur
	 *
	 * @param string $name
	 * @param string $descript
	 * @param string $type
	 * @param string $formula formula
	 * @param string $functionName nom de la fonction
	 * @return Xfer_Comp_Header
	 */
	function Xfer_Comp_Header($name,$descript,$type = "",$formula="",$functionName="") {
		$this->PEAR('Observer_Error');
		$this->m_name = $name;
		$this->m_descript = $descript;
		$this->m_type = $type;
		$this->m_formula=$formula;
		$this->m_functionName=$functionName;
	}

	/**
	 * Retourne le contenu XML de l'entête
	 *
	 * @return string
	 */
	function getReponseXML() {
		$xml_text = sprintf("<HEADER name='%s'",$this->m_name);
		if($this->m_type != "")
			$xml_text = $xml_text.sprintf(" type='%s'",$this->m_type);
		$xml_text = $xml_text.sprintf("><![CDATA[%s]]></HEADER>",$this->m_descript);
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
	 */
	var $m_headers = array();

	/**
	 * Liste de cellules
	 *
	 * @var array
	 */
	var $m_records = array();

	/**
	 * Liste d'actions
	 *
	 * @var array
	 */
	var $m_actions = array();

	/**
	 * Nombre total de pages
	 *
	 * @var int
	 */
	var $mPageMax = 0;

	/**
	 * Numéro page courant
	 *
	 * @var int
	 */
	var $mPageMum = 0;

	/**
	 * Nombre de lignes totales
	 *
	 * @var int
	 */
	var $mNbLines = 0;

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
	 * @param string $formula formula
	 * @param string $functionName nom de la fonction
	 */
	function addHeader($name,$descript,$type = "",$formula = "",$functionName="") {
		$new_obs = & new Xfer_Comp_Header($name,$descript,$type,$formula,$functionName);
		$this->m_headers[$name] = $new_obs;
	}

	/**
	 * Ajoute l'action associé au bouton
	 *
	 * @param Xfer_Action $action
	 */
	function addAction($action,$posAct=-1) {
		if($this->checkActionRigth($action)) {
			if ($posAct!=-1) {
				$old_actions=$this->m_actions;
				$this->m_actions=array();
				$index=0;
				foreach($old_actions as $action_item) {
					if ($index==posAct) {
						$this->m_actions[]=$action;
						$index++;
						$action=null;
					}
					$this->m_actions[]=$action_item;
					$index++;
				}
				if ($action!=null)
					$this->m_actions[]=$action;
			}
			else
				$this->m_actions[]=$action;
		}
	}

	/**
	 * Ajoute un entête de colonne dans une grille
	 *
	 * @param string $FieldName
	 * @param array $desc_fld
	 * @param integer $type_fld 0:entier - 1:réel - 3:booléen - autre:chaine
	 * @param string $formula formula
	 * @param string $functionName nom de la fonction
	 */
	function newHeader($FieldName,$desc_fld,$type_fld,$formula="",$functionName="") {
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
		$this->addHeader($FieldName,$desc_fld,$type_col,$formula,$functionName);
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
					if ($pos === false) {
						$desc_fld = "";
						$formula=$FieldName;
					}
					else {
						$desc_fld = substr($FieldName,0,$pos);
						$formula = substr($FieldName,$pos+strlen(SEP_SHOW));
					}
					if($formula[0] == '#') {
						$fct_name = substr($formula,1);
						list($fct_file_name,$fct_name)=$DBObjs->getMethodFileName($fct_name);
						if (is_file($fct_file_name))
							require_once($fct_file_name);
						else
							$fct_name='';
					}
					else
						$fct_name='';
					$this->newHeader($FieldName,$desc_fld,$type_fld,$formula,$fct_name);
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
		if( array_key_exists($FieldName,$field_desc)) {
			$type_fld = $field_desc[$FieldName]['type'];
			$data = $DBObjs->getField($FieldName);
		}
		else {
			$type_fld = 2;
			$header=$this->m_headers[$FieldName];
			$fct_name=$header->m_functionName;
			if (($fct_name!='') && function_exists($fct_name))
				$data = $fct_name($DBObjs);
			else
				$data = $DBObjs->evalByText($header->m_formula);
		}
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
		case 6:
			//Date & time
			List($date_val,$time_val)=split(' ',$data);
			$val = convertTime($time_val)." ".convertDate($date_val);
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

	function definePage($ContextParams) {
		if (is_array($ContextParams)) {
			$page_num=$ContextParams[GRID_PAGE.$this->m_name];
			$this->mPageMax = (int)ceil($this->mNbLines/MAX_GRID_RECORD);
			if ($this->mPageMax<$page_num) $page_num=0;
			$this->mPageMum = $page_num;
			$record_min=$this->mPageMum*MAX_GRID_RECORD;
			$record_max=($this->mPageMum+1)*MAX_GRID_RECORD;
		}
		else {
			$record_min=0;
			$record_max=$this->mNbLines;
		}
		return array($record_min,$record_max);
	}

	/**
	 * Remplis une grille avec la déscription de l'objet $DBObjs et les champs décrits.
	 *
	 * @param DBObj_Basic $DBObjs
	 * @param null|string|array $FieldNames
	 * @param string $RefTableName
	 * @param array $ContextParams Context des parametres => Numero de la page [0,N-1]
	 */
	function setDBObject($DBObjs,$FieldNames = null,$RefTableName = "",$ContextParams=null) {
		$this->mNbLines=$DBObjs->N;
		list($record_min,$record_max)=$this->definePage($ContextParams);

		if( is_int($FieldNames))
			$FieldNames = $DBObjs->getFieldEditable($RefTableName,(int)$FieldNames);
		else if($FieldNames == null)
			$FieldNames = $DBObjs->getFieldEditable($RefTableName);
		$this->setDBObjectHeader($DBObjs,$FieldNames,$RefTableName);
		$field_desc = $DBObjs->getDBMetaDataField();

		$record_current=0;
		while($DBObjs->fetch() && ($record_current<$record_max)) {
			if ($record_current>=$record_min)
				foreach($FieldNames as $FieldName) {
					$pos = strpos($FieldName,"[");
					if($pos === false)
						$this->setDBObjectData($DBObjs->id,$DBObjs,$FieldName,$field_desc);
					else {
						$new_field_name = substr($FieldName,0,$pos);
						if( array_key_exists($new_field_name,$field_desc)) {
							$new_obj = $DBObjs->getField($new_field_name);
							$new_FieldNames = split(',', substr($FieldName,$pos+1,-1));
							$new_field_desc = $new_obj->__DBMetaDataField;
							foreach($new_FieldNames as $new_FieldName)
								$this->setDBObjectData($DBObjs->id,$new_obj,$new_FieldName,$new_field_desc);
						}
					}
				}
			$record_current++;
		}
	}

	/**
	 * Remplis une grille avec le contenu d'une connexion
	 *
	 * @param int $queryId
	 * @param string $FieldKey
	 * @param array $ContextParams Context des parametres => Numero de la page [0,N-1]
	 */
	function setDBRows($queryId,$FieldKey,$ContextParams=null) {
		global $connect;
		$this->mNbLines=$connect->getNumRows($queryId);
		list($record_min,$record_max)=$this->definePage($ContextParams);

		$record_current=0;
		while(($row=$connect->getRowByName($queryId)) && ($record_current<$record_max)) {
			if ($record_current>=$record_min) {
				$id=$row[$FieldKey];
				foreach($this->m_headers as $header) {
					$field_name=$header->m_name;
					$val=$row[$field_name];
					$this->setValue($id,$field_name,$val);
				}
			}
			$record_current++;
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
	 * Retourne l'ensemble des attributs du composant
	 *
	 * @access public
	 * @return string
	 */
	function _attributs() {
		$xml_attr = parent:: _attributs();
		if( is_int($this->mPageMax) && ($this->mPageMax > 1))
			$xml_attr .= " PageMax='".$this->mPageMax."' PageNum='".$this->mPageMum."'";
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
		foreach($this->m_headers as $header)$xml_text = $xml_text.$header->getReponseXML();
		foreach($this->m_records as $key => $record) {
			$xml_text = $xml_text. sprintf("<RECORD id='%s'>",$key);
			foreach($record as $name => $value)
				$xml_text = $xml_text.sprintf("<VALUE name='%s'><![CDATA[%s]]></VALUE>",$name,$value);
			$xml_text = $xml_text."</RECORD>";
		}
		if( count($this->m_actions) != 0) {
			$xml_text = $xml_text."<ACTIONS>";
			foreach($this->m_actions as $action)
				$xml_text = $xml_text.$action->getReponseXML();
			$xml_text = $xml_text."</ACTIONS>";
		}
		return $xml_text;
	}
}
//@END@
?>
