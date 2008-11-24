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
// --- Last modification: Date 23 November 2008 15:05:30 By  ---

//@BEGIN@
/**
 * fichier gérant le DBSearch
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage DBObject
 */require_once("DBObject.inc.php");
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
	/**
	 * Constructeur DB_Search
	 *
	 * @return DB_Search
	 */
	public function __construct($aDBObj) {
		$this->DBObject = $aDBObj;
	}
	/**
	 * Retourne un sous champ
	 *
	 * @access private
	 * @param string $field_name
	 * @param string $subname
	 * @return array
	 */
	private function getSubField($field_name,$subname = "") {
		$FieldName = $field_name;
		if(($subname != "") && ( substr($FieldName,0, strlen($subname)+1) == ($subname. SEP_SEARCH)))$FieldName = substr($FieldName, strlen($subname)+1);
		if(($pos_sep = strpos($FieldName, SEP_SEARCH)) !== false)
		return Array( substr($FieldName,$pos_sep+1), substr($FieldName,0,$pos_sep));
		else
		return Array('',$FieldName);
	}
	/**
	* SearchByField
	* @access private
	*/
	private function SearchByField($Params,$field_name,$tbref = '',$subname = "") {
		$search_tbl = array($this->DBObject->__table);
		$search_query = "";
		if($tbref == '')$tbref = $this->DBObject->__table;
		list($sub_fields,$FieldName) = $this->getSubField($field_name,$subname);
		if( array_key_exists($FieldName,$this->DBObject->__DBMetaDataField)) {
			$field_item = $this->DBObject->__DBMetaDataField[$FieldName];
			$type_fld = $field_item['type'];
			$select_id = (int)$Params[$field_name."_select"];
			$value1 = $Params[$field_name."_value1"];
			switch($type_fld) {
			case 0:
				//int
			case 1:
				//float
				// "ignorer","=","<",">"
				switch($select_id) {
				case 1:
					$search_query = "$tbref.$FieldName=$value1";
					break;
				case 2:
					$search_query = "$tbref.$FieldName<$value1";
					break;
				case 3:
					$search_query = "$tbref.$FieldName>$value1";
					break;
				}
				break;
			case 2:
				//text
			case 7:
				//long text
				// "ignorer","contiens"
				if(( trim($value1) != "") && ($select_id == 1))
					$search_query = "$tbref.$FieldName like '%$value1%'";
				break;
			case 3:
				//bool
				// " ignorer"," = "
				if($select_id== 1)
					$search_query="$tbref.$FieldName = $value1";
				break;
			case 4:
				//Date
			case 5:
				//time
			case 6:
				//Date & time
				/// " ignorer"," = ","<",">"
				//
				switch($select_id) {
				case 1:
					$search_query = "$tbref.$FieldName= '$value1'";
					break;
				case 2:
					$search_query = "$tbref.$FieldName<'$value1'";
					break;
				case 3:
					$search_query = "$tbref.$FieldName>'$value1'";
					break;
				}
				break;
			case 8:
				// enum
			case 10:
				// ref
				// " ignorer"," ou"," et"
				//
				if($sub_fields != "") {
					$param_fld = $field_item['params'];
					$TableName = $param_fld[" TableName"];
					$val = new DB_Search($this->DBObject->getField($FieldName));
					if ($subname == "")
						$new_sub_field = $FieldName;
					else
						$new_sub_field = $subname.SEP_SEARCH.$FieldName;
					$search = $val->SearchByField($Params,$field_name,$TableName,$new_sub_field);
					if($search[1] != "") {
						$search_tbl[] = $TableName;
						$search_tbl = array_merge($search_tbl,$search[0]);
						$search_query = $search[1];
						$search_query .= "AND $TableName.id =$tbref.$FieldName";
					}
				}
				else if( trim($value1) != "") {
					$list = split(';',$value1);
					switch($select_id) {
					case 1:
						$or_item = "";
						foreach($list as $item) {
							if($or_item != "")
								$or_item .= " OR ";
							$or_item .= "($tbref.$FieldName=$item)";
						}
						if($or_item != "")
							$search_query = "($or_item)";
						break;
					case 2:
						$search_query = "";
						foreach($list as $item) {
							if($search_query != "")
								$search_query .= " AND ";
							$search_query .= "$tbref.$FieldName=$item";
						}
						break;
					}
				}
				break;
			case 9:
				// child
				$param_fld=$field_item['params'];
				$TableName=$param_fld["TableName"];
				$RefField=$param_fld["RefField"];
				$val= new DB_Search($this->DBObject->getField($FieldName));
				if($subname == "")
					$new_sub_field = $FieldName;
				else
					$new_sub_field = $subname.SEP_SEARCH.$FieldName;
				$search = $val->SearchByField($Params,$field_name,$TableName,$new_sub_field);
				if($search[1] != "") {
					$search_tbl[] = $TableName;
					$search_tbl = array_merge($search_tbl,$search[0]);
					$search_query = $search[1];
					$search_query .= " AND $TableName.$RefField = $tbref.id";
				}
				break;
			}
		}
		else {
			if($this->DBObject->Super != null) {
				$super_setup = new DB_Search($this->DBObject->Super);
				list($super_search_tbl,$super_search_query) = $super_setup->SearchByField($Params,$field_name,'',$subname);
				if($super_search_query != '') {
					$search_tbl = $super_search_tbl;
					if (!in_array($this->DBObject->__table,$search_tbl))
						$search_tbl[]=$this->DBObject->__table;
					$search_query = $super_search_query." AND ".$this->DBObject->__table.".superId=".$this->DBObject->Super->__table.".id";
				}
			}
		}
		return array($search_tbl,$search_query);
	}
	/**
	 * Lance une recherche d'enregistrement
	 *
	 * Permet de rechercher des enregistrements.
	 * Pour chaque champs intervenant dans la requetes, 2 clefs suffixés par _select et _value1 doivent être référencé dans$Params* _select: référence l'operateur de comparaison
	 * _value1: valeur à comparer
	 * @param array$Params* @param string$OrderBy*/
	function Execute($Params,$OrderBy = '') {
		$search_tbl = array($this->DBObject->__table);
		$search_query = "";
		$FieldNames = $this->DBObject->getFieldEditable();
		foreach($Params as $key => $val)
			if(( substr($key,-7) == "_select") && ($Params[$key] != "0")) {
				list($tbls,$q) = $this->SearchByField($Params, substr($key,0,-7));
				if($q != "") {
					$search_tbl = array_merge($search_tbl,$tbls);
					if($search_query != "")
						$search_query .= " AND ";
					$search_query .= $q;
				}
			}
		$order_by = "";
		if($OrderBy != '') {
			list($sub_field_name,$field_name) = $this->getSubField($OrderBy);
			if($sub_field_name != '') {
				$field_item = $this->DBObject->__DBMetaDataField[$field_name];
				if($field_item['type'] == 10) {
					$param_fld = $field_item['params'];
					$TableName = $param_fld["TableName"];
					$order_by = "$TableName.$sub_field_name";
					if( count( array_keys($search_tbl,$TableName)) == 0) {
						$search_tbl[] = $TableName;
						$search_query .= " AND $field_name=$TableName.id";
					}
				}
			}
			else $order_by = "$field_name";
		}
		$table_list = '';
		$search_tbl = array_unique($search_tbl);
		foreach($search_tbl as $tbl)$table_list .= "$tbl,";
		$query = "";
		if($search_query != "") {
			$query = " SELECT DISTINCT ".$this->DBObject->__table.".* FROM ".$table_list;
			$query = substr($query,0,-1);
			$query .= " WHERE ".$search_query;
			if($order_by != '')
				$query.=" ORDER BY ".$order_by;
			logAutre("Recherche:$query");
		}
		else logAutre("Pas de recherche:$search_query-$table_list");
		return $query;
	}
}
//@END@
?>
