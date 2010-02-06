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
// --- Last modification: Date 05 February 2010 22:25:05 By  ---

//@BEGIN@
/**
 * fichier gérant le DBSearch
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 1.0
 * @package Lucterios
 * @subpackage DBObject
 */

require_once("DBObject.inc.php");

/**
* Classe de recherche au DBObject Luctèrios
*
* Classe d'aide au requete de recherche
*
* @package Lucterios
* @subpackage DBObject
* @author Pierre-Oliver Vershoore/Laurent Gay
*/
class DB_Search {

	private $DBObject;

	private $dataField;

	private $paramSearch=array();
	public $fields=array();
	public $tables=array();
	public $conditions=array();

	private $special_selection=1;
	private $special_nb=0;

	/**
	 * Constructeur DB_Search
	 *
	 * @return DB_Search
	 */
	public function __construct($aDBObj) {
		$this->DBObject = $aDBObj;
		$this->dataField= $aDBObj->getDBMetaDataField();
		$this->tables=array();
		$this->conditions=array();
	}

	private function returnType($fieldName,$obj) {
		$desc=null;
		if( array_key_exists($fieldName,$obj->__DBMetaDataField)) {
			$desc=array();
			$desc['TYPE']=$obj->__DBMetaDataField[$fieldName]['type'];
			$desc['PARAM']=$obj->__DBMetaDataField[$fieldName]['params'];
			$desc['TABLES']=array($obj->__table);
			$desc['FIELD']=$fieldName;
			$desc['SUB']=null;
		}
		else {
			if($obj->Super != null) {
				$desc=$this->returnType($fieldName,$obj->Super);
				if ($desc!=null) {
					$desc['TABLES'][]=$obj->__table;
				}
			}
		}
		return $desc;
	}

	private function addTypeDescription($fieldName,$comp,$value) {
		$desc=null;
		$sep_pos=strpos($fieldName,SEP_SEARCH);
		if ($sep_pos===false) {
			// champ simple
			$field_type=$this->returnType($fieldName,$this->DBObject);
			if ($field_type!=null) {
				$this->paramSearch[$fieldName]=array($comp,$value,$field_type);
			}
		}
		else {
			// champ complex
			$main_field_name=substr($fieldName,0,$sep_pos);
			$sub_field_name=substr($fieldName,$sep_pos+1);
			if (!array_key_exists($main_field_name,$this->paramSearch)) {
				$this->addTypeDescription($main_field_name,null,null);
				if (array_key_exists($main_field_name,$this->paramSearch)) {
					$type=$this->paramSearch[$main_field_name][2]['TYPE'];
					if (($type==9) || ($type==10)) {
						$table_link=$this->paramSearch[$main_field_name][2]['PARAM']['TableName'];
						$file_link= $this->DBObject->getTableName($table_link);
						require_once($file_link);
						$class_link='DBObj_'.$table_link;
						$this->paramSearch[$main_field_name][2]['SUB']=new DB_Search(new $class_link());
					}
					else
						unset($this->paramSearch[$main_field_name]);
				}
			}
			if (array_key_exists($main_field_name,$this->paramSearch)) {
				$sub_search=$this->paramSearch[$main_field_name][2]['SUB'];
				$sub_search->addTypeDescription($sub_field_name,$comp,$value);
			}
		}
	}

	private function initParamSearch($Params) {
		$this->paramSearch=array();
		foreach($Params as $name=>$comp) {
			if ((substr($name,-7)=='_select') && ($comp!=0)) {
				$ident_field=substr($name,0,-7);
				$value=trim($Params[$ident_field.'_value1']);
				$this->addTypeDescription($ident_field,$comp,$value);
			}
		}
		logAutre("PARAM=".print_r($this->paramSearch,true));
	}

	private function getConditionsHeritage($params) {
		$conditions=array();
		$table_list=$params[2]['TABLES'];
		for($idx=1;$idx<count($table_list);$idx++){
			$conditions[]=$table_list[$idx-1].".id=".$table_list[$idx].".superId";
		}
		return $conditions;
	}

	private function getConditions($params) {
		$conditions=array();
		$table_list=$params[2]['TABLES'];
		$current_table=$table_list[0];
		$current_field=$params[2]['FIELD'];
		$select_id=$params[0];
		$value1=$params[1];

		switch($params[2]['TYPE']) {
		case 0:
			//int
		case 1:
			//float
			// "ignorer","=","<",">"
			switch($select_id) {
			case 1:
				$conditions[]="$current_table.$current_field=$value1";
				break;
			case 2:
				$conditions[]="$current_table.$current_field<$value1";
				break;
			case 3:
				$conditions[]="$current_table.$current_field>$value1";
				break;
			}
			break;
		case 4:
			//Date
		case 5:
			//time
		case 6:
			//Date & time

			// "ignorer","=","<",">"
			switch($select_id) {
			case 1:
				$conditions[]="$current_table.$current_field='$value1'";
				break;
			case 2:
				$conditions[]="$current_table.$current_field<'$value1'";
				break;
			case 3:
				$conditions[]="$current_table.$current_field>'$value1'";
				break;
			}
			break;
		case 2:
			//text
		case 7:
			//long text

			// "ignorer","contiens","commence par","fini par","égal"
			if((trim($value1) != "")) {
				switch($select_id) {
				case 1:
					$conditions[]="$current_table.$current_field like '%$value1%'";
					break;
				case 2:
					$conditions[]="$current_table.$current_field like '$value1%'";
					break;
				case 3:
					$conditions[]="$current_table.$current_field like '%$value1'";
					break;
				case 4:
					$conditions[]="$current_table.$current_field like '$value1'";
					break;
				}
			}
			break;
		case 3:
			//bool

			// " ignorer"," = "
			if($select_id== 1) {
				$conditions[]="$current_table.$current_field=$value1";
			}
			break;
		case 8:
			// enum
			// " ignorer"," ou"
			if(( trim($value1) != "") && ($select_id == 1)) {
				$value1=str_replace(array(';'),array(','),$value1);
				$conditions[]="$current_table.$current_field in ($value1)";
			}
			break;
		case 10:
			// ref

			// " ignorer"," ou", "et"
			$sub_search=$params[2]['SUB'];
			if ($sub_search==null) {
				if(( trim($value1) != "") && (($select_id == 1) || ($select_id == 2))) {
					$this->special_selection=max($this->special_selection,$select_id);
					$this->special_nb=max($this->special_nb,count(split(';',$value1)));
					$value1=str_replace(array(';'),array(','),$value1);
					$conditions[]="$current_table.$current_field IN ($value1)";

				}
			}
			else {
				$sub_search->process();
				$sub_Q=$sub_search->queryCreator('id');
				$this->special_selection=$sub_search->special_selection;
				if ($sub_Q!='') {
					$conditions[]="$current_table.$current_field IN ($sub_Q)";
				}
			}
			break;
		case 9:
			// child

			// " ignorer"," ou"," et"
			$sub_search=$params[2]['SUB'];
			if ($sub_search==null) {
				if(trim($value1) != "") {
					$this->special_selection=max($this->special_selection,$select_id);
					$this->special_nb=max($this->special_nb,count(split(';',$value1)));
					$value1=str_replace(array(';'),array(','),$value1);
					$sub_table=$params[2]['PARAM']['TableName'];
					$sub_field=$params[2]['PARAM']['RefField'];
					$sub_Q="SELECT $sub_field FROM $sub_table WHERE id IN ($value1)";
					if($select_id== 1) {
						$conditions[]="$current_table.id IN ($sub_Q)";
					}
					if($select_id== 2) {
						$conditions[]="$current_table.id =ANY ($sub_Q)";
					}
				}
			}
			else {
				$sub_field=$params[2]['PARAM']['RefField'];
				$sub_search->process();
				if($sub_search->special_selection== 1) {
					$sub_Q=$sub_search->queryCreator($sub_field);
					if ($sub_Q!='') {
						$conditions[]="$current_table.id IN ($sub_Q)";
					}
				}
				if($sub_search->special_selection== 2) {
					$sub_search->conditions[]=$sub_search->DBObject->__table.".$sub_field=$current_table.id";
					$sub_Q=$sub_search->queryCreator(null);
					if ($sub_Q!='') {
						$conditions[]=$sub_search->special_nb."=($sub_Q)";
					}
				}
			}
			break;
		}
		return $conditions;
	}

	private function process() {
		foreach($this->paramSearch as $param_name=>$param_item) {
			$this->tables=array_merge($this->tables,$param_item[2]['TABLES']);
			$this->conditions=array_merge($this->conditions,$this->getConditions($param_item));
			$this->conditions=array_merge($this->conditions,$this->getConditionsHeritage($param_item));
		}
		$this->tables=array_unique($this->tables);
		$this->conditions=array_unique($this->conditions);
		$id=array_search('',$this->conditions);
		if ($id!==false) {
			unset($this->conditions[$id]);
		}
		logAutre("tables=".print_r($this->tables,true));
		logAutre("conditions=".print_r($this->conditions,true));
	}

	/**
	 * Generation de la requette
	 *
	 * @param string $select_item
	 * @param string $OrderBy
	 */
	public function queryCreator($select_item,$OrderBy="") {
		if ($select_item==null)
			$ret_col="count(*)";
		else
		if ($select_item=='*') {
			list($fields,$tables,$wheres)=$this->DBObject->prepQuery();
			$this->tables=array_merge($this->tables,$tables);
			$this->conditions=array_merge($this->conditions,$wheres);
			$this->tables=array_unique($this->tables);
			$this->conditions=array_unique($this->conditions);
			$ret_col=implode(',',$fields);
		}
		else
			$ret_col=$this->DBObject->__table.".$select_item";
		if ((count($this->tables)>0) && (count($this->conditions)>=count($this->tables))) {
			$query="SELECT $ret_col FROM ".implode(',',$this->tables)." WHERE ".implode(' AND ',$this->conditions);
			if ($OrderBy!='') {
				$query." ORDER BY ".$OrderBy;
			}
			logAutre("query:$query");
			return $query;
		}
		return "";
	}


	/**
	 * Lance une recherche d'enregistrement
	 *
	 * Permet de rechercher des enregistrements.
	 * Pour chaque champs intervenant dans la requetes, 2 clefs suffixés par _select et _value1 doivent être référencé dans $Params
      * _select: référence l'operateur de comparaison
	 * _value1: valeur à comparer
	 * @param array $Params
	 * @param string $OrderBy
	*/
	public function Execute($Params,$OrderBy = '',$searchQuery = "",$searchTable=array()) {
		$this->tables=$searchTable;
		$searchQuery=trim($searchQuery);
		$searchQuery=str_replace(array('and'),array('AND'),$searchQuery);
		$this->conditions=split('AND',$searchQuery);

		$this->initParamSearch($Params);

		$this->process();
		return $this->queryCreator('*',$OrderBy);
	}
}
//@END@
?>
