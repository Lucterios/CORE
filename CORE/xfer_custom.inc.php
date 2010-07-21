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
// --- Last modification: Date 21 July 2010 9:10:59 By  ---

//@BEGIN@
/**
 * fichier gérant une fenÃªtre personnalisÃ©e
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer

 */

require_once'xfer_component.inc.php';
require_once'xfer_grid.inc.php';

/**
 * Classe containaire d'une fenêtre personnalisée
 *
 * @package Lucterios

 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_Custom extends Xfer_Container_Abstract {
	/**
	 * Liste des actions
	 *
	 * @var array
	 */
	var $m_actions = array();

	/**
	 * Liste des composants
	 *
	 * @var array
	 */
	var $m_components = array();

	/**
	 * tabulation courante
	 *
	 * @access private
	 * @var integer
	 */
	var $m_tab = 0;

	/**
	 * Constructor
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Custom
	 */
	function Xfer_Container_Custom($extension,$action,$context = array()) {
		$this->Xfer_Container_Abstract($extension,$action,$context);
		$this->m_observer_name = "Core.Custom";
	}

	/**
	 * Remplire une fenêtre avec des controles dépandant de la classe
	 *
	 * @param DBObj_Basic $DBObjs
	 * @param null|string|array $FieldNames champ ou liste des champs
	 * @param boolean $ReadOnly
	 * @param integer $posY position en vertical
	 * @param integer $posX position en horizontal
	 * @param integer $colspan encombrement horizontal
	 */
	function setDBObject($DBObjs,$FieldNames = null,$ReadOnly = false,$posY = 0,$posX = 0,$colspan = 1) {
		$field_desc = $DBObjs->getDBMetaDataField();
		if(is_int($FieldNames))
			$FieldNames = $DBObjs->getFieldEditable($RefTableName,(int)$FieldNames);
		else if($FieldNames == null)
			$FieldNames = $DBObjs->getFieldEditable($RefTableName);
		elseif (!is_array($FieldNames)) {
			$FieldNames = array($FieldNames);
		}
		foreach($FieldNames as $FieldName) {
			if (array_key_exists($FieldName,$field_desc)) {
				$field_item = $field_desc[$FieldName];
				$type_fld = $field_item['type'];
				$field_val = $DBObjs->getField($FieldName);
			}
			else {
				$first_letter=substr($FieldName,0,1);
				if (in_array($first_letter,array('0','1','2','3','4','5','6','7','8'))) {
					$FieldName=substr($FieldName,1);
					$type_fld = (int)$first_letter;
				}
				else
					$type_fld = 2;
				$pos = strpos($FieldName, SEP_SHOW);
				if($pos === false) {
					$field_item['description']=$FieldName;
					$formula=$FieldName;
				}
				else {
					$field_item['description']=substr($FieldName,0,$pos);
					$formula=substr($FieldName,$pos+strlen(SEP_SHOW));
				}
				$field_val = $DBObjs->evalByText($formula);
			}
			$comp = null;
			if($ReadOnly) {
				switch($type_fld) {
				case 3:
					//bool
					$comp = new Xfer_Comp_Label($FieldName);
					$comp->setValue($field_val!=0?'Oui':'Non');
					break;
				case 4:
					//Date
					$comp = new Xfer_Comp_Label($FieldName);
					$comp->setValue( convertDate($field_val, true));
					break;
				case 5:
					//time
					$comp = new Xfer_Comp_Label($FieldName);
					$comp->setValue( convertTime($field_val));
					break;
				case 6:
					//Date & time
					$comp = new Xfer_Comp_Label($FieldName);
					List($date_val,$time_val)=split(' ',$field_val);
					$comp->setValue(convertDate($date_val, true)." ".convertTime($time_val));
					break;
				case 9:
					//Child
					$comp = new Xfer_Comp_Grid($FieldName);
					$comp->setDBObject($field_val, null,$DBObjs->__table,$this->m_context);
					break;
				case 12:
				case 13:
					//Method
					$params=$DBObjs->__DBMetaDataField[$FieldName]['params'];
					$comp = new Xfer_Comp_Label($FieldName);
					$comp->setValue($DBObjs->Call($params['MethodGet']));
					break;
				default :
					$comp = new Xfer_Comp_Label($FieldName);
					if(is_object($field_val))
						$comp->setValue($field_val->toText());
					else
						$comp->setValue($field_val);
					break;
				}
			}
			else {
				$param_fld = $field_item['params'];
				switch($type_fld) {
				case 0:
					//int
					$comp = new Xfer_Comp_Float($FieldName,$param_fld['Min'],$param_fld['Max'],0);
					$comp->setValue($field_val);
					break;
				case 1:
					//float
					$comp = new Xfer_Comp_Float($FieldName,$param_fld['Min'],$param_fld['Max'],$param_fld['Prec']);
					$comp->setValue($field_val);
					break;
				case 2:
					//text
					if(array_key_exists('Multi',$param_fld) && $param_fld['Multi'])
						$comp = new Xfer_Comp_Memo($FieldName);
					else
						$comp = new Xfer_Comp_Edit($FieldName);
					$comp->setValue($field_val);
					break;
				case 3:
					//bool
					$comp = new Xfer_Comp_Check($FieldName);
					$comp->setValue($field_val);
					break;
				case 4:
					//Date
					$comp = new Xfer_Comp_Date($FieldName);
					$comp->setValue($field_val);
					break;
				case 5:
					//time
					$comp = new Xfer_Comp_Time($FieldName);
					$comp->setValue($field_val);
					break;
				case 6:
					//Date & time
					$comp = new Xfer_Comp_DateTime($FieldName);
					$comp->setValue($field_val);
					break;
				case 7:
					// long text
					$comp = new Xfer_Comp_Memo($FieldName);
					$comp->setValue($field_val);
					break;
				case 8:
					// enum
					$comp = new Xfer_Comp_Select($FieldName);
					$comp->setSelect($param_fld['Enum']);
					$comp->setValue($DBObjs->$FieldName);
					break;
				case 10:
					// ref
					$select_list = array();
					$comp = new Xfer_Comp_Select($FieldName);
					$tbl_name = $param_fld['TableName'];
					$table_file_name = $DBObjs->getTableName($tbl_name);
					if( is_file($table_file_name)) {
						require_once($table_file_name);
						$class_name = "DBObj_".$tbl_name;
						$sub_object = new $class_name;
						$sub_object->find();
						while($sub_object->fetch())
							$select_list[$sub_object->id] = $sub_object->toText();
					}
					$comp->setSelect($select_list);
					$comp->setValue($DBObjs->$FieldName);
					break;
				case 12:
					// method chaine
					$comp = new Xfer_Comp_Edit($FieldName);
					$comp->setValue($field_val);
					break;
				case 13:
					// method réel
					$comp = new Xfer_Comp_Float($FieldName,$param_fld['Min'],$param_fld['Max'],$param_fld['Prec']);
					$comp->setValue($field_val);
					break;
				}
			}
			if($comp != null) {
				$desc_fld = $field_item['description'];
				$label = new Xfer_Comp_LabelForm('label'.$FieldName);
				$label->setValue("{[bold]}".$desc_fld."{[/bold]}");
				$label->setLocation($posX,$posY);
				$this->addComponent($label);
				$comp->setLocation($posX+1,$posY,$colspan);
				$comp->setNeeded($field_item['notnull']);
				$comp->m_description = $desc_fld;
				$this->addComponent($comp);
				$posY++;
			}
		}
	}

	var 	$FULL_TEXT_SELECTOR = array("contient","commence par","fini par","égal");
	var $testSelector=0;

	/**
	 * Remplire une fenêtre avec des controle de selection de recherche
	 *
	 * @param DBObj_Basic $DBObjs
	 * @param null|string|array $FieldNames champ ou liste des champs
	 * @param integer $posY position initial
	 * @param string $SubSearch
	 * @param string $SubTitle
	 * @return integer position final
	 */
	function setDBSearch($DBObjs,$FieldNames = null,$posY = 0,$SubSearch = "",$SubTitle = "",$SubFieldOfChield = false) {
		$field_desc = $DBObjs->getDBMetaDataField();
		if(is_int($FieldNames))
			$FieldNames = $DBObjs->getFieldEditable($RefTableName,(int)$FieldNames);
		else if($FieldNames == null)
			$FieldNames = $DBObjs->getFieldEditable($RefTableName);
		elseif (!is_array($FieldNames)) {
			$FieldNames = array($FieldNames);
		}
		foreach($FieldNames as $FieldName) {
			$SubField = null;
			if(($pos = strpos($FieldName,"[")) !== false) {
				$tmp_names = substr($FieldName,$pos+1,-1);
				$SubField = array();
				while(($comma_pos = strpos($tmp_names,",")) !== false) {
					$new_field = substr($tmp_names,0,$comma_pos);
					$tmp_names = substr($tmp_names,$comma_pos+1);
					if(( strpos($new_field,"[")>0) && ( strpos($new_field,"]") === false) && (($sep_pos = strpos($tmp_names,"]"))>0)) {
						$new_field .= ",". substr($tmp_names,0,$sep_pos+1);
						$tmp_names = substr($tmp_names,$sep_pos+1);
					}
					if($new_field != "")
						$SubField[] = $new_field;
				}
				if($tmp_names != "")
					$SubField[] = $tmp_names;
				$FieldName = substr($FieldName,0,$pos);
			}
			$field_name = $FieldName;
			if($SubSearch != "")
				$field_name = $SubSearch. SEP_SEARCH.$field_name;
			$field_item = $field_desc[$FieldName];
			$type_fld = $field_item['type'];
			$select_list = null;
			$line = array();
			$param_fld = $field_item['params'];
			switch($type_fld) {
			case 0:
				//int
				$comp = new Xfer_Comp_Float($field_name."_value1",$param_fld['Min'],$param_fld['Max'],0);
				$comp->setValue($DBObjs->getField($FieldName));
				$select_list = array("égal","inférieur","suppérieur");
				$line[] = $comp;
				break;
			case 1:
				//float
				$comp = new Xfer_Comp_Float($field_name."_value1",$param_fld['Min'],$param_fld['Max'],$param_fld['Prec']);
				$comp->setValue($DBObjs->getField($FieldName));
				$select_list = array("égal","inférieur","suppérieur");
				$line[] = $comp;
				break;
			case 2:
				//text
				if(array_key_exists('Multi',$param_fld) && $param_fld['Multi'])
					$comp = new Xfer_Comp_Memo($field_name."_value1");
				else
					$comp = new Xfer_Comp_Edit($field_name."_value1");
				$comp->setValue($DBObjs->getField($FieldName));
				if ($this->testSelector!=-1)
					$select_list = array($this->testSelector => $this->FULL_TEXT_SELECTOR[$this->testSelector]);
				else
					$select_list = $this->FULL_TEXT_SELECTOR;
				$line[] = $comp;
				break;
			case 3:
				//bool
				$comp = new Xfer_Comp_Check($field_name."_value1");
				$comp->setValue($DBObjs->getField($FieldName));
				$select_list = array("égal");
				$line[] = $comp;
				break;
			case 4:
				//Date
				$comp = new Xfer_Comp_Date($field_name."_value1");
				$comp->setValue($DBObjs->getField($FieldName));
				$select_list = array("égal","inférieur","suppérieur");
				$line[] = $comp;
				break;
			case 5:
				//time
				$comp = new Xfer_Comp_Time($field_name."_value1");
				$comp->setValue($DBObjs->getField($FieldName));
				$select_list = array("égal","inférieur","suppérieur");
				$line[] = $comp;
				break;
			case 6:
				//Date & time
				$comp = new Xfer_Comp_DateTime($field_name."_value1");
				$comp->setValue($DBObjs->getField($FieldName));
				$select_list = array("égal","inférieur","suppérieur");
				$line[] = $comp;
				break;
			case 7:
				// long text
				$comp = new Xfer_Comp_Memo($field_name."_value1");
				$comp->setValue($DBObjs->getField($FieldName));
				if ($this->testSelector!=-1)
					$select_list = array($this->testSelector => $this->FULL_TEXT_SELECTOR[$this->testSelector]);
				else
					$select_list = $this->FULL_TEXT_SELECTOR;
				$line[] = $comp;
				break;
			case 8:
				// enum
				$comp = new Xfer_Comp_CheckList($field_name."_value1");
				$comp->setSelect($param_fld['Enum']);
				$comp->setValue($DBObjs->$FieldName);
				$comp->setSize( min(120,20* count($comp->m_select)),200);
				if($SubFieldOfChield)
					$select_list = array("ou","et");
				else
					$select_list = array("ou");
				$line[] = $comp;
				break;
			case 9:
				// child
				$param_fld = $field_item['params'];
				$desc_fld = $field_item['description'];
				$value = $DBObjs->getField($FieldName);
				if($SubField == null) {
					$RefField = $param_fld["RefField"];
					$SubField = $value->getFieldEditable($DBObjs->__table);
				}
				if($SubTitle == "")
					$new_title = $desc_fld;
				else
					$new_title = "$SubTitle-$desc_fld";
				if($SubSearch == "")
					$new_subsearch = $FieldName;
				else
					$new_subsearch = $SubSearch. SEP_SEARCH.$FieldName;
				$posY = $this->setDBSearch($value,$SubField,$posY,$new_subsearch,$new_title, true);
				break;
			case 10:
				// ref
				if($SubField == null) {
					$selectlist = array();
					$comp = new Xfer_Comp_CheckList($field_name."_value1");
					$tbl_name = $param_fld['TableName'];
					$table_file_name = $DBObjs->getTableName($tbl_name);
					if( is_file($table_file_name)) {
						require_once($table_file_name);
						$class_name = "DBObj_".$tbl_name;
						$sub_object = new $class_name;
						$sub_object->find();
						while($sub_object->fetch())
							$selectlist[$sub_object->id] = $sub_object->toText();
					}
					$comp->setSelect($selectlist);
					$comp->setValue($DBObjs->$FieldName);
					$comp->setSize( min(120,20* count($comp->m_select)),200);
					if($SubSearch)
						$select_list = array("ou","et");
					else
						$select_list = array("ou");
					$line[] = $comp;
				}
				else {
					$param_fld = $field_item['params'];
					$desc_fld = $field_item['description'];
					$value = $DBObjs->getField($FieldName);
					if($SubTitle == "")
						$new_title = $desc_fld;
					else
						$new_title = "$SubTitle-$desc_fld";
					if($SubSearch == "")
						$new_subsearch = $FieldName;
					else
						$new_subsearch = $SubSearch. SEP_SEARCH.$FieldName;
					$posY = $this->setDBSearch($value,$SubField,$posY,$new_subsearch,$new_title);
				}
				break;
			}
			if($select_list != null) {
				$desc_fld = $field_item['description'];
				$label = new Xfer_Comp_LabelForm('label'.$field_name);
				if($SubTitle == "")
					$label->setValue("{[bold]}$desc_fld{[/bold]}");
				else
					$label->setValue("{[bold]}$SubTitle-$desc_fld{[/bold]}");
				$label->setLocation(0,$posY);
				$this->addComponent($label);
				if( count($select_list) == 1) {
					$keys=array_keys($select_list);
					$first_key=(int)$keys[0];
					$lbl_select = new Xfer_Comp_Label($field_name."_lbl_select");
					$lbl_select->setLocation(1,$posY);
					$lbl_select->setValue($select_list[$first_key]);
					$this->addComponent($lbl_select);
					$this->m_context[$field_name."_select"] = ($first_key+1);
				}
				else {
					$select_field_name = $field_name."_select";
					$value_field_name = $field_name."_value1";
					$select = new Xfer_Comp_Select($field_name."_select");
					$select->setSelect( array_merge(array("ignorer"),$select_list));
					$select->setLocation(1,$posY);
					$select->setNeeded( false);
					$select->setValue(0);
					$select->JavaScript = "var value=current.getRequete('').get('$select_field_name').toString();
parent.get('$value_field_name').setEnabled(value!='0');";
					$this->addComponent($select);
				}
				$posX = 2;
				foreach($line as $subcomp) {
					$subcomp->setLocation($posX,$posY);
					$subcomp->setNeeded( false);
					$this->addComponent($subcomp);
					$posX++;
				}
				$posY++;
			}
		}
		return $posY;
	}

	/**
	 * Ajoute un composant
	 *
	 * @param Xfer_Component $component
	 */
	function addComponent($component) {
		$component->tab = $this->m_tab;
		$id = $component->getId();
		$this->m_components[$id] = $component;
	}

	/**
	 * Modifie la taille par defaut des composants d'une même position
	 *
	 * @param int $col
	 * @param int $hmin
	 * @param int $vmin
	 */
	function resize($col,$hmin,$vmin) {
		foreach($this->m_components as $comp)
			if(($comp->x == $col) && ($comp->colspan == 1))
				$comp->setSize($vmin,$hmin);
	}

	/**
	 * Cherche une tabulation
	 *
	 * @param string $tabName
	 */
	function findTab($tabName) {
		$num = -1;
		$index = 0;
		foreach($this->m_components as $comp) {
			if('Xfer_Comp_Tab' == get_class($comp)) {
				$index = max($index,$comp->tab);
				if($comp->m_value == $tabName)
					$num = $comp->tab;
			}
		}
		if( is_string($tabName))
			return $num;
		else
			return $index;
	}

	/**
	 * Ajoute une tabulation
	 *
	 * @param string $tabName
	 */
	function newTab($tabName,$num = -1) {
		$old_num = $this->findTab($tabName);
		if($old_num == -1) {
			if($num == -1)
				$this->m_tab = $this->findTab( null)+1;
			else {
				foreach($this->m_components as $comp)if($comp->tab >= $num)$comp->tab = $comp->tab+1;
				$this->m_tab = $num;
			}
			$new_tab = new Xfer_Comp_Tab();
			$new_tab->setValue($tabName);
			$new_tab->setLocation(-1,-1);
			$this->addComponent($new_tab);
		}
		else
			$this->m_tab = $old_num;
	}
	/**
	 * Nombre de composants
	 *
	 * @return integer
	 */
	function getComponentCount() {
		if($this->m_components == null)
			return "NULL";
		else
			return count($this->m_components);
	}

	/**
	 * Retourne un composant identifier par son numéro ou son nom
	 *
	 * @param integer|string $cmp_idx
	 * @return Xfer_Component
	 */
	function getComponents($cmp_idx) {
		if( is_int($cmp_idx)) {
			$nb = count($this->m_components);
			if($cmp_idx<0)
				$cmp_idx = $nb+$cmp_idx;
			if(($cmp_idx >= 0) && ($cmp_idx<$nb)) {
				$list_ids = array_keys($this->m_components);
				$comp_id = $list_ids[$cmp_idx];
				return $this->m_components[$comp_id];
			}
			else
				return "$cmp_idx-$nb";
		}
		else if( is_string($cmp_idx)) {
			$comp_id = '';
			foreach($this->m_components as $key => $comp)
				if($comp->m_name == $cmp_idx)
					$comp_id = $key;
			if($comp_id != '')
				return $this->m_components[$comp_id];
			else
				return null;
		}
		else
		return null;
	}

	/**
	 * Supprime un composant identifier par son numéro ou son nom
	 *
	 * @param integer|string $cmp_idx
	 */
	function removeComponents($cmp_idx) {
		if( is_int($cmp_idx)) {
			$nb = count($this->m_components);
			if($cmp_idx<0)
				$cmp_idx = $nb+$cmp_idx;
			if(($cmp_idx >= 0) && ($cmp_idx<$nb)) {
				$list_ids = array_keys($this->m_components);
				$comp_id = $list_ids[$cmp_idx];
				unset($this->m_components[$comp_id]);
			}
		}
		else if( is_string($cmp_idx)) {
			$comp_id = '';
			foreach($this->m_components as $key => $comp)
				if($comp->m_name == $cmp_idx)
					$comp_id = $key;
			if($comp_id != '')
				unset($this->m_components[$comp_id]);
		}
	}

	/**

	 * Ajoute une action à la fenêtre personnalisée
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
	 * getSortComponents
	 *
	 * @return array
	 */
	function getSortComponents() {
		$final_components = array();
		foreach($this->m_components as $component) {
			$id = $component->getId();
			$final_components[$id] = $component;
		}
		ksort($final_components);
		return $final_components;
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	function _ReponseXML() {
		$xml_text = "";
		if( count($this->m_components) != 0) {
			$final_components = $this->getSortComponents();
			$xml_text = $xml_text."<COMPONENTS>";
			foreach($final_components as $component)
				$xml_text = $xml_text.$component->getReponseXML();
			$xml_text = $xml_text."</COMPONENTS>\n";
		}
		if( count($this->m_actions) != 0) {
			$xml_text = $xml_text."<ACTIONS>";
			foreach($this->m_actions as $action)
				$xml_text = $xml_text.$action->getReponseXML();
			$xml_text = $xml_text."</ACTIONS>\n";
		}
		return $xml_text;
	}
}
//@END@
?>
