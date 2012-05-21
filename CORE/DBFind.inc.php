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
 * Classe gÃ©rant la recherche
 *
 * @package Lucterios

 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class DBFind {

	 /**
	 * Objet de recherche
	 *
	 * @access private
	 * @var DBObject
	 */
	private $m_object = null;

	/**
	 * Constructor
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Custom
	 */
	public function __construct($DBObject) {
		$this->m_object = $DBObject;
	}



	public function convertFieldDesc($SearchFieldDescList) {
		$resFieldDesc=array();
		if (is_string($SearchFieldDescList)) {
			$item=$this->convertFieldItem($SearchFieldDescList);
			if (count($item)>0)
				$resFieldDesc[$item['fieldname']]=$item;
		}
		else if (is_array($SearchFieldDescList))
		    foreach($SearchFieldDescList as $SearchFieldDescItem) {
			if (is_string($SearchFieldDescItem)){
				$item=$this->convertFieldItem($SearchFieldDescItem);
				if (count($item)>0)
					$resFieldDesc[$item['fieldname']]=$item;
			}elseif (count($SearchFieldDescItem)>0)
				$resFieldDesc[$SearchFieldDescItem['fieldname']]=$SearchFieldDescItem;
		    }
		return $resFieldDesc;
	}


	private function convertFieldItem($FieldName) {
		$resFieldItem=array();
		if(($pos = strpos($FieldName,"[")) !== false) {
			$sufix_name = substr($FieldName,$pos+1,-1);
			$prefix_Name = substr($FieldName,0,$pos);
			$field_desc = $this->m_object->getDBMetaDataField();
			$field_item = $field_desc[$prefix_Name];
			if (isset($field_item['params']['TableName'])) {
				$file_class_name=DBObj_Abstract::getTableName($field_item['params']['TableName']);
				$class_name = 'DBObj_'.$field_item['params']['TableName'];
				if ($file_class_name!='') {
					include_once($file_class_name);
					$OtherFind=new DBFind(new $class_name);
					$resFieldItem=$OtherFind->convertFieldItem($sufix_name);
					$resFieldItem['description']=$field_item['description'].'/'.$resFieldItem['description'];
					$resFieldItem['fieldname']=$prefix_Name.'['.$resFieldItem['fieldname'].']';

					if ($field_item['type']==9)
						$resFieldItem['wheres'][]=$this->m_object->__table.'.id='.$OtherFind->m_object->__table.'.'.$field_item['params']['RefField'];
					else
						$resFieldItem['wheres'][]=$this->m_object->__table.'.'.$prefix_Name.'='.$OtherFind->m_object->__table.'.id';

					$resFieldItem['tables'][]=$this->m_object->__table;
				}
			}
		}
		else if (array_key_exists($FieldName,$this->m_object->__DBMetaDataField)) {
			$resFieldItem=$this->getFieldItemFromDataField($FieldName,$this->m_object->__DBMetaDataField[$FieldName]);
		}
		else if ($this->m_object->Heritage!='') {
			list($file_class_name,$class_name)=DBObj_Abstract::getTableAndClass($this->m_object->Heritage);
			if ($file_class_name!='') {
				include_once($file_class_name);
				$OtherFind=new DBFind(new $class_name);
				$resFieldItem=$OtherFind->convertFieldItem($FieldName);
				$resFieldItem['wheres'][]=$this->m_object->__table.'.superId='.$OtherFind->m_object->__table.'.id';
				$resFieldItem['tables'][]=$this->m_object->__table;
			}
		}
		return $resFieldItem;
	}


	private function getFieldItemFromDataField($FieldName,$field_item) {
		$resFieldItem=array();
		// minimum
		$resFieldItem['fieldname']=$FieldName;
		$resFieldItem['description']=$field_item['description'];
		$resFieldItem['list']='';
		$resFieldItem['type']='';
		// spécial base
		$resFieldItem['table.name']=$this->m_object->__table.'.'.$FieldName;
		$resFieldItem['tables']=array($this->m_object->__table);
		$resFieldItem['wheres']=array();

		$type_fld = $field_item['type'];
		$param_fld = $field_item['params'];
		switch($type_fld) {
		case 0:
			//int
			$resFieldItem['type']='float';
			$resFieldItem['list']=$param_fld['Min'].';'.$param_fld['Max'].';0';
			break;
		case 1:
			//float
			$resFieldItem['type']='float';
			$resFieldItem['list']=$param_fld['Min'].';'.$param_fld['Max'].';'.$param_fld['Prec'];
			break;
		case 2:
		case 7:
			//text
			$resFieldItem['type']='str';
			break;
		case 3:
			//bool
			$resFieldItem['type']='bool';
			break;
		case 4:
			//Date
			$resFieldItem['type']='date';
			break;
		case 5:
			//time
			$resFieldItem['type']='time';
			break;
		case 6:
			//Date & time
			$resFieldItem['type']='datetime';
			break;
		case 8:
			// enum
			$resFieldItem['type']='list';
			$list="";
			foreach($param_fld['Enum'] as $id=>$val)
				$list.="$id||$val;";				
			$resFieldItem['list']=$list;
			break;
		case 9:
			// child
			$resFieldItem['type']='listmult';
		case 10:
			// ref
			if ($resFieldItem['type']=='')
				$resFieldItem['type']='list';

			$file_class_name=DBObj_Abstract::getTableName($param_fld['TableName']);
			$class_name = 'DBObj_'.$param_fld['TableName'];
			if ($file_class_name!='') {
				$list="";
				include_once($file_class_name);
				$OtherDBObj=new $class_name;
				$OtherDBObj->find();
				while($OtherDBObj->fetch()) {
					$list.=$OtherDBObj->id."||".$OtherDBObj->toText().";";				
				}
				$resFieldItem['list']=$list;
			}
			break;
		}
		return $resFieldItem;
	}


	public static function getOperatorListResult() {
		$operatorList=array();
		$operatorList[1]="égal à";
		$operatorList[2]="différent à";
		$operatorList[3]="inférieur à";
		$operatorList[4]="supérieur à";
		$operatorList[5]="contient";
		$operatorList[6]="commence par";
		$operatorList[7]="fini par";
		$operatorList[8]="correspond à";
		$operatorList[9]="correspond à";
		return $operatorList;
	}

	public static function getScriptForOperator($TypeVar,$ResVar) {
		$script="
if (($TypeVar=='str'))
    $ResVar+='<CASE id=\"5\">contient</CASE>';
if (($TypeVar=='str'))
    $ResVar+='<CASE id=\"6\">commence par</CASE>';
if (($TypeVar=='str'))
    $ResVar+='<CASE id=\"6\">fini par</CASE>';

if (($TypeVar=='float') || ($TypeVar=='str') || ($TypeVar=='bool') || ($TypeVar=='date') || ($TypeVar=='time') || ($TypeVar=='datetime'))
    $ResVar+='<CASE id=\"1\">egal</CASE>';

if (($TypeVar=='float') || ($TypeVar=='str') || ($TypeVar=='date') || ($TypeVar=='time') || ($TypeVar=='datetime'))
    $ResVar+='<CASE id=\"2\">different</CASE>';

if (($TypeVar=='float') || ($TypeVar=='date') || ($TypeVar=='time') || ($TypeVar=='datetime'))
    $ResVar+='<CASE id=\"3\">inferieur</CASE>';

if (($TypeVar=='float') || ($TypeVar=='date') || ($TypeVar=='time') || ($TypeVar=='datetime'))
    $ResVar+='<CASE id=\"4\">superieur</CASE>';

if (($TypeVar=='list') || ($TypeVar=='listmult'))
    $ResVar+='<CASE id=\"8\">ou</CASE>';

if (($TypeVar=='listmult'))
    $ResVar+='<CASE id=\"9\">et</CASE>';

if ($ResVar=='')
    $ResVar+='<CASE id=\"0\"></CASE>';
";
		return $script;
	}

	public static function getOperatorListQuery() {
		$operatorList=array();
		$operatorList[1]="=@@";
		$operatorList[2]="<>@@";
		$operatorList[3]="<@@";
		$operatorList[4]=">@@";
		$operatorList[5]=" like '%@@%'";
		$operatorList[6]=" like '@@%'";
		$operatorList[7]=" like '%@@'";
		$operatorList[8]=" in (@@)";
		$operatorList[9]=" in (@@)";
		return $operatorList;
	}

	public function generateQuery($Params,$oldtables=array(),$oldwheres=array()) {
		$CriteriaList=$this->extractCriteria($Params);
		$operatorList=DBFind::getOperatorListQuery();

		$notManage=array();
		$tables=$oldtables;
		if (!in_array($this->m_object->__table,$tables))
			$tables=array($this->m_object->__table);
		$wheres=$oldwheres;
		foreach($CriteriaList as $id=>$criteriaItem) {
			$new_name=$criteriaItem[0];
			$new_op=(int)$criteriaItem[1];
			$new_val=$criteriaItem[2];
			if (is_array($criteriaItem[3]))
				$FieldDescItem=$criteriaItem[3];
			else
				$FieldDescItem=$this->convertFieldItem($new_name);

			if (isset($FieldDescItem['table.name'])) {
				$new_type=$FieldDescItem['type'];
				if (($new_type=='date') || ($new_type=='time') || ($new_type=='datetime'))
					$new_val_txt="'".$new_val."'";
				else if ($new_type=='bool') {
					$new_val_txt=($new_val=='o')?"'o'":"'n'";
				}
				else if ($new_type=='str') {
					$new_val_txt=str_replace("'","''",$new_val);
					if (($new_op==1) || ($new_op==2))
						$new_val_txt="'".$new_val."'";
				}
				else if ($new_type=='list') {
					$new_val_txt=str_replace(';',',',$new_val);
				}
				else
					$new_val_txt=$new_val;
				$new_wheres=$FieldDescItem['wheres'];
				if (isset($FieldDescItem['operator'])) {
					$operator_text=$FieldDescItem['operator'];
					$new_val_txt=$new_val;
				}
				else
					$operator_text=$operatorList[$new_op];
				$new_wheres[]=$FieldDescItem['table.name'].str_replace('@@',$new_val_txt,$operator_text);
				foreach($new_wheres as $new_where)
					if (!in_array($new_where,$wheres))
						$wheres[]=$new_where;
				$new_tables=$FieldDescItem['tables'];
				foreach($new_tables as $new_table)
					if (!in_array($new_table,$tables))
						$tables[]=$new_table;
			}
			else if (isset($FieldDescItem['tables']) && isset($FieldDescItem['wheres'])) {
				$new_wheres=$FieldDescItem['wheres'];
				foreach($new_wheres as $new_where)
					if (!in_array($new_where,$wheres))
						$wheres[]=$new_where;
				$new_tables=$FieldDescItem['tables'];
				foreach($new_tables as $new_table)
					if (!in_array($new_table,$tables))
						$tables[]=$new_table;
			}
			else {
			      $FieldDescItem['op']=$new_op;
			      $FieldDescItem['val']=$new_val;
			      $notManage[]=$FieldDescItem;
			}
		}
		return array($tables,$wheres,$notManage);
	}


	public function getCriteriaDescription($CriteriaList) {
		$operatorList=DBFind::getOperatorListResult();
		$criteriaDesc=array();
		foreach($CriteriaList as $id=>$criteriaItem) {
			$new_name=$criteriaItem[0];
			if ($new_name!='') {
				$new_val=$criteriaItem[2];
				$new_op=(int)$criteriaItem[1];
				if (is_array($criteriaItem[3]))
					$FieldDescItem=$criteriaItem[3];
				else
					$FieldDescItem=$this->convertFieldItem($new_name);
				$new_type=$FieldDescItem['type'];
				$new_desc=$FieldDescItem['description'];
				if ($new_type=='str')
					$new_val_txt='"'.$new_val.'"';
				else if ($new_type=='bool') {
					$new_val_txt=($new_val=='o')?"oui":"non";
				}
				else if ($new_type=='date') {
					$new_val_txt=convertDate($new_val);
				}
				else if ($new_type=='datetime') {
					$datetime=explode(' ',$new_val);  
					$new_val_txt=convertDate($datetime[0]).' '.$datetime[1];
				}
				else if ($new_type=='list') {
					$ids=explode(';',$new_val);
					$new_list=explode(';',$FieldDescItem['list']);
					$new_val_txt='';
					foreach($new_list as $new_list_item) {
						$val_list=explode('||',$new_list_item);
						if (in_array($val_list[0],$ids)) {
							if ($new_val_txt!='')
								$new_val_txt.=($new_op==7)?' ou ':' et ';
							$new_val_txt.='"'.$val_list[1].'"';
						}
					}
				}
				else
					$new_val_txt=$new_val;
				$criteriaDesc[]="{[bold]}$new_desc{[/bold]} ".$operatorList[$new_op]." {[italic]}$new_val_txt{[/italic]}";
			}
		}
		return $criteriaDesc;
	}

	public static function getCriteriaText($DBObject,$Params,$extraAddon='') {
		$newFind= new DBFind($DBObject);
		$criteriaDesc=$newFind->getCriteriaDescription($newFind->extractCriteria($Params));
		if (count($criteriaDesc)>0) {
			if ($extraAddon!='')
			      $criteriaDesc=array_merge((array)$extraAddon,$criteriaDesc);
			$searchText="{[underline]}Vos critères de recherche:{[/underline]} ".implode(' et ',$criteriaDesc);
		}
		else
			$searchText="{[underline]}Aucun critère de recherche défini{[/underline]}";
		return $searchText;
	}


	public function extractCriteria($Params) {
		$CriteriaList=array();
		if (isset($Params['CRITERIA'])) {
			$current_criteria=explode('//',$Params['CRITERIA']);
			foreach($current_criteria as $criteriaItem) {
				$criteriaval=explode('||',$criteriaItem);
				if (count($criteriaval)>=3) {
					for($i=3;$i<count($criteriaval);$i++) {
						$cmd='$tmpVal='.urldecode($criteriaval[$i]).';';
						eval($cmd);
						$criteriaval[$i]=$tmpVal;
					}
					$CriteriaList[]=$criteriaval;
				}
			}
		}
		return $CriteriaList;
	}

	public function reinjectCriteria(&$Params,$CriteriaList,$FieldDescList) {
		include_once("CORE/setup_param.inc.php");
		foreach($FieldDescList as $FieldDescItem) {
			if (!isset($FieldDescItem['fieldname']) && isset($FieldDescItem['tables']) && isset($FieldDescItem['wheres'])) {
				$CriteriaList[]=array("","","",$FieldDescItem);
			}
		}
		$tempList=array();
		foreach($CriteriaList as $CriteriaItem) {
			if (count($CriteriaItem)>=3) {
			      for($i=3;$i<count($CriteriaItem);$i++) {
				      $CriteriaItem[$i]=urlencode(Param_Parameters::ArrayToString($CriteriaItem[$i]));
			      }
			      $tempList[]=implode('||',$CriteriaItem);
			}
		}
		$Params['CRITERIA']=implode('//',$tempList);
	}

	
	public function checkInCriteriaFieldExisting($CriteriaList,$Fields) {
		$NewCriteriaList=array();
		foreach($CriteriaList as $CriteriaItem) {
			$exist=false;
			foreach($Fields as $Field)
				if (($CriteriaItem[0]==$Field['fieldname']) || ($CriteriaItem[0]==$Field))
					$exist=true;
			if ($exist)
				$NewCriteriaList[]=$CriteriaItem;
		}
		return $NewCriteriaList;
	}

	public function Execute($Params,$OrderBy = '',$searchQuery = "",$searchTable=array(),$extraFields=array()) {
		list($fields,$oldtables,$oldwheres)=$this->m_object->prepQuery(true,true);
		foreach($extraFields as $extraField)
			$fields[]=$extraField;
		if ($searchQuery!='')
			$oldwheres[]=$searchQuery;
		foreach($searchTable as $new_table)
			if (!in_array($new_table,$oldtables))
				$oldtables[]=$new_table;	
		list($tables,$wheres,$notManage)=$this->generateQuery($Params,$oldtables,$oldwheres);
		if (count($wheres)>0)
			$query="SELECT ".implode(',',$fields)." FROM ".implode(',',$tables)." WHERE ".implode(' AND ',$wheres);
		if ($OrderBy!='')
			$query.=" ORDER BY ".$OrderBy;
		__log($query,"DBFind.execute");
		return $query;
	}
}

//@END@
?>
 
