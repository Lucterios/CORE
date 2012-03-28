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
// --- Last modification: Date 13 November 2011 19:09:27 By  ---

//@BEGIN@
/**
 * fichier gérant une fenêtre personnalisée
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
	public $m_actions = array();

	/**
	 * Liste des composants
	 *
	 * @var array
	 */
	public $m_components = array();

	/**
	 * tabulation courante
	 *
	 * @access private
	 * @var integer
	 */
	public $m_tab = 0;

	/**
	 * Constructor
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Custom
	 */
	public function __construct($extension,$action,$context = array()) {
		parent::__construct($extension,$action,$context);
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
	public function setDBObject($DBObjs,$FieldNames = null,$ReadOnly = false,$posY = 0,$posX = 0,$colspan = 1) {
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
					List($date_val,$time_val)=explode(' ',$field_val);
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
					if ($field_item['notnull']==false)
						$select_list[0]='';
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

	public $testSelector=0;

	private function buildSelectAndScript($FieldDescList) {
		include_once("CORE/DBFind.inc.php");

		$selector=array();
		$script_ref="findFields=new Array();\n";
		$script_ref.="findLists=new Array();\n";
		foreach($FieldDescList as $FieldDescItem) {
			if (isset($FieldDescItem['fieldname'])) {
				$selector[$FieldDescItem['fieldname']]=$FieldDescItem['description'];
				$script_ref.="findFields['".$FieldDescItem['fieldname']."']='".$FieldDescItem['type']."';\n";
				if (($FieldDescItem['type']=='list') || ($FieldDescItem['type']=='listmult') || ($FieldDescItem['type']=='float'))
					$script_ref.="findLists['".$FieldDescItem['fieldname']."']='".$FieldDescItem['list']."';\n";
			}
		}
		$script_ref.="
var name=current.getValue();
var type=findFields[name];
parent.get('searchValueFloat').setVisible(type=='float');
parent.get('searchValueStr').setVisible(type=='str');
parent.get('searchValueBool').setVisible(type=='bool');
parent.get('searchValueDate').setVisible(type=='date' || type=='datetime');
parent.get('searchValueTime').setVisible(type=='time' || type=='datetime');
parent.get('searchValueList').setVisible(type=='list' || type=='listmult');
var new_operator='';
".DBFind::getScriptForOperator('type','new_operator')."
parent.get('searchOperator').setValue('<SELECT>'+new_operator+'</SELECT>');
if (type=='float') {
    var prec=findLists[name].split(';');
    parent.get('searchValueFloat').setValue('<FLOAT min=\"'+prec[0]+'\" max=\"'+prec[1]+'\" prec=\"'+prec[2]+'\"></FLOAT>');
}
if (type=='str') {
    parent.get('searchValueStr').setValue('<STR></STR>');
}
if (type=='bool') {
    parent.get('searchValueBool').setValue('<BOOL>n</BOOL>');
}
if (type=='date' || type=='datetime') {
    parent.get('searchValueDate').setValue('<DATE>1900/01/01</DATE>');
}
if (type=='time' || type=='datetime') {
    parent.get('searchValueTime').setValue('<DATE>00:00:00</DATE>');
}
if ((type=='list') || (type=='listmult')) {
    var list=findLists[name].split(';');
    var list_txt='';
    for(i=0;i<list.length;i++) {
	var val=list[i].split('||');
	if (val.length>1)
		list_txt+='<CASE id=\"'+val[0]+'\">'+val[1]+'</CASE>';
    }
    parent.get('searchValueList').setValue('<SELECT>'+list_txt+'</SELECT>');
}
";
		return array($selector,$script_ref);
	}


	private function manageFindAction(&$currentCriteria,$FieldDescList) {
		if ($this->m_context['ACT']=='ADD') {
			$new_name=$this->m_context['searchSelector'];
			$new_type=$FieldDescList[$new_name]['type'];
			if ($new_type!='') {
				$new_op=$this->m_context['searchOperator'];
				$new_val='';
				if ($new_type=='float')
					$new_val=$this->m_context['searchValueFloat'];
				if ($new_type=='str')
					$new_val=$this->m_context['searchValueStr'];
				if ($new_type=='bool')
					$new_val=$this->m_context['searchValueBool'];
				if ($new_type=='date')
					$new_val=$this->m_context['searchValueDate'];
				if ($new_type=='time')
					$new_val=$this->m_context['searchValueTime'];
				if ($new_type=='datetime')
					$new_val=$this->m_context['searchValueDate'].' '.$this->m_context['searchValueTime'];
				if (($new_type=='list') || ($new_type=='listmult'))
					$new_val=$this->m_context['searchValueList'];
				if ($new_val!='')
					$currentCriteria[]=array($new_name,$new_op,$new_val,$FieldDescList[$new_name]);
			}
		}
		else if (isset($this->m_context['ACT'])) {
			unset($currentCriteria[$this->m_context['ACT']]);
		}
		unset($this->m_context['ACT']);
	}

	/**
	 * Remplire une fenêtre avec des controle de selection de recherche
	 *
	 * @param DBObj_Basic $DBObjs
	 * @param null|string|array $SearchFieldDescList champ ou liste des champs ou liste de descriptifs de recherche
	 * @param integer $posY position en vertical
	 * @param integer $posX position en horizontal
	 */
	public function setSearchGUI($DBObjs,$SearchFieldDescList = null,$posY = 0,$posX = 0) {
		// $FieldDescItem : array('fieldname'=>'','description'=>'','type'=>'xxx','list'=>'xxx||yyyy;xxx||yyyy;xxx||yyyy')
		// type:float,str,bool,date,time,datetime,list,listmult
		include_once("CORE/DBFind.inc.php");
		$newFind= new DBFind($DBObjs);
		$FieldDescList=$newFind->convertFieldDesc($SearchFieldDescList);
		list($selector,$script_ref)=$this->buildSelectAndScript($FieldDescList);

		$label = new Xfer_Comp_LabelForm('labelsearchSelector');
		$label->setValue("{[bold]}Nouveau critère{[/bold]}");
		$label->setLocation($posX,$posY,1,7);
		$this->addComponent($label);
		$comp = new Xfer_Comp_Select("searchSelector");
		$comp->setSelect($selector);
		$comp->setValue("");
		$comp->setLocation($posX+1,$posY,1,7);
		$comp->setSize(20,200);
		$comp->JavaScript=$script_ref;
		$this->addComponent($comp);

		$comp = new Xfer_Comp_Select("searchOperator");
		$comp->setSelect(array());
		$comp->setValue("");
		$comp->setSize(20,200);
		$comp->setLocation($posX+2,$posY,1,7);
		$this->addComponent($comp);

		$comp = new Xfer_Comp_Button("searchButtonAdd");
		$comp->setIsMini(true);
		$comp->setClickInfo('ACT','ADD');
		$comp->setLocation($posX+4,$posY,1,7);
		$comp->setAction($this->getRefreshAction("","add.png"));
		$this->addComponent($comp);

		$comp = new Xfer_Comp_Float("searchValueFloat");
		$comp->setLocation($posX+3,$posY++);
		$comp->setSize(20,200);
		$this->addComponent($comp);
		$comp = new Xfer_Comp_Edit("searchValueStr");
		$comp->setLocation($posX+3,$posY++);
		$comp->setSize(20,200);
		$this->addComponent($comp);
		$comp = new Xfer_Comp_Check("searchValueBool");
		$comp->setLocation($posX+3,$posY++);
		$comp->setSize(20,200);
		$this->addComponent($comp);
		$comp = new Xfer_Comp_Date("searchValueDate");
		$comp->setLocation($posX+3,$posY++);
		$comp->setSize(20,200);
		$this->addComponent($comp);
		$comp = new Xfer_Comp_Time("searchValueTime");
		$comp->setLocation($posX+3,$posY++);
		$comp->setSize(20,200);
		$this->addComponent($comp);
		$comp = new Xfer_Comp_CheckList("searchValueList");
		$comp->setLocation($posX+3,$posY++);
		$comp->setSize(80,200);
		$this->addComponent($comp);
		$label = new Xfer_Comp_LabelForm('labelsearchSep');
		$label->setValue("");
		$label->setSize(1,200);
		$label->setLocation($posX+3,$posY++);
		$this->addComponent($label);

		$current_criteria=$newFind->extractCriteria($this->m_context);
		$this->manageFindAction($current_criteria,$FieldDescList);
		$newFind->reinjectCriteria($this->m_context,$current_criteria,$FieldDescList);
		$criteriaDesc=$newFind->getCriteriaDescription($current_criteria,$FieldDescList);

		$label = new Xfer_Comp_LabelForm('labelsearchDescTitle');
		if (count($criteriaDesc)>0) {
			$label->setValue("{[bold]}{[underline]}Vos critères de recherche:{[/underline]}{[/bold]}");
			$label->setLocation($posX,$posY,2,4);
		}
		else {
			$label->setValue("{[center]}{[bold]}{[underline]}Aucun critère de recherche défini{[/underline]}{[/bold]}{[/center]}");
			$label->setLocation($posX,$posY,4);
		}
		$this->addComponent($label);
		$posY++;
		foreach($criteriaDesc as $id=>$criteriaText) {
			$label = new Xfer_Comp_LabelForm('labelSearchText_'.$id);
			$label->setValue($criteriaText);
			$label->setLocation($posX+2,$posY,2);
			$this->addComponent($label);
			$comp = new Xfer_Comp_Button("searchButtonDel_".$id);
			$comp->setIsMini(true);
			$comp->setClickInfo('ACT',$id);
			$comp->setLocation($posX+4,$posY++);
			$comp->setAction($this->getRefreshAction("","suppr.png"));
			$this->addComponent($comp);
		}
		return $posY;
	}

	/**
	 * Ajoute un composant
	 *
	 * @param Xfer_Component $component
	 */
	public function addComponent($component) {
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
	public function resize($col,$hmin,$vmin) {
		foreach($this->m_components as $comp)
			if(($comp->x == $col) && ($comp->colspan == 1))
				$comp->setSize($vmin,$hmin);
	}

	/**
	 * Cherche une tabulation
	 *
	 * @param string $tabName
	 */
	public function findTab($tabName) {
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
	public function newTab($tabName,$num = -1) {
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
	public function getComponentCount() {
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
	public function getComponents($cmp_idx) {
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
			$comp_res = null;
			foreach($this->m_components as $key => $comp) {
				if($comp->m_name == $cmp_idx) {
					$comp_res = $comp;
				}
			}
			return $comp_res;
		}
		else
		return null;
	}

	/**
	 * Supprime un composant identifier par son numéro ou son nom
	 *
	 * @param integer|string $cmp_idx
	 */
	public function removeComponents($cmp_idx) {
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
	public function addAction($action,$posAct=-1) {
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
	public function getSortComponents() {
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
	protected function _ReponseXML() {
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
