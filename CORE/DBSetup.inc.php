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
// --- Last modification: Date 21 November 2008 14:33:57 By  ---

//@BEGIN@
/**
 * fichier gérant la creation et la migration des tables DB
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage DBObject
 */require_once("DBObject.inc.php");
/**
* Classe de gestion de setup au DBObject Luctèrios
*
* Classe principale de manipulation de creation et modification dess tables utilisèes par Luctérios.
*
* @package Lucterios
* @subpackage DBObject
* @author Pierre-Oliver Vershoore/Laurent Gay
*/
class DBObj_Setup {

	private $DBObject;

	private $Dbh;
	/**
	 * ID_KEY_TYPE
	 *
	 * @var string
	 * @access private
	 */const ID_KEY_TYPE = "int(10) unsigned NOT NULL";
	/**
	 * LOCK_TYPE
	 *
	 * @var string
	 * @access private
	 */const LOCK_TYPE = "varchar(100) NOT NULL";
	/**
	 * Description (console) de l'action réaliser
	 *
	 * @var string
	 */
	public $Return = "";
	/**
	 * Constructeur DBObj_Setup
	 *
	 * @return DBObj_Setup
	 */
	public function __construct($aDBObj) {
		$this->DBObject = $aDBObj;
		require_once'DB.php';
		$options = array('debug' => 2,'portability' => DB_PORTABILITY_ALL);
		$this->Dbh = & DB:: connect($aDBObj->_database_dsn,$options);
	}
	/**
	 * rafraichi les valeurs par defaut de la classe/table
	 *
	 * @return string retour console
	 */
	public function refreshDefaultValues() {
		$this->Return .= "{[italic]}Rafraichissement par defaut de ".$this->DBObject->tblname."{[/italic]}{[newline]}";
		foreach($this->DBObject->DefaultFields as $field_values) {
			$refresh_data = false;
			if( array_key_exists('@refresh@',$field_values))$refresh_data = $field_values['@refresh@'];
			$field_id = -1;
			if( array_key_exists('id',$field_values) && ($field_values['id']!=''))$field_id = (int)$field_values['id'];
			$this->refreshDefaultValue($field_values,$field_id,$refresh_data);
		}
		$this->ReaffectAutoinc();
		return $this->Return;
	}
	/**
	 * rafraichi une valeur par defaut de la classe/table
	 *
	 * @param array $fieldValues dictionnaire nom de champ/valeur
	 * @param integer $fieldId force une clef primaire. Les valeurs inferieurs à 100 sont reservées
	 * @param boolean $refreshData force le rafraichissement des enregistrement existants
	 * @return string retour console
	 */
	public function refreshDefaultValue($fieldValues,$fieldId = -1,$refreshData = false) {
		$fields = array_keys($this->DBObject->__DBMetaDataField);
		$DBObjClass = $this->DBObject->GetClassName();
		$DBObj = new $DBObjClass;
		$nb_find = 0;
		if($fieldId != -1) {

			try {
				$nb_find = $DBObj->get($fieldId);
			}
			 catch( Exception$e) {
				$nb_find = 0;
			}
		}
		else {
			$nb_field = min($this->DBObject->NbFieldsCheck, count($this->DBObject->__DBMetaDataField));
			if($nb_field>0) {
				for($fieldidx = 0;
				$fieldidx<$nb_field;
				$fieldidx++) {
					$field_name = $fields[$fieldidx];
					if( array_key_exists($field_name,$fieldValues)) {
						$DBObj->$field_name = $fieldValues[$field_name];
					}
				}
				$nb_find = $DBObj->find();
				$DBObj->fetch();
			}
		}
		$DBObj->setFrom($fieldValues);
		if($nb_find>0) {
			if($refreshData) {
				$DBObj->update();
				$this->Return .= "Refresh enregistrement - update #";
				$this->Return .= $DBObj->id;
				$this->Return .= ".{[newline]}";
			}
			else {
				$this->Return .= "Refresh enregistrement - OK #";
				$this->Return .= $DBObj->id;
				$this->Return .= ".{[newline]}";
			}
		}
		else {
			if($fieldId == -1) {
				$DBObj->insert();
				$this->Return .= "Refresh enregistrement - insert #";
				$this->Return .= $DBObj->id;
				$this->Return .= "{[newline]}";
			}
			else {
				$f = "id ";
				$v = "'$fieldId'";
				foreach($fieldValues as $fld => $vl)if( in_array($fld,$fields)) {
					$f .= ",$fld";
					$v .= ", '". str_replace("'","'",$vl)."' ";
				}
				if($DBObj->Heritage != '') {
					$new_obj = new $DBObjClass;
					$super_id = $new_obj->Super->insert();
					$f .= ",superId";
					$v .= ", '".$super_id."' ";
				}
				$q = "INSERT INTO ".$this->DBObject->__table."($f) VALUES ($v)";
				$rep = &$DBObj->query($q);
				$this->Return .= "Refresh enregistrement [$q] insert #$fieldId";
				if( DB:: isError($rep))$this->Return .= "{".$rep->getMessage()."}";
				$this->Return .= "{[newline]}";
			}
		}
		return $this->Return;
	}

	/**
	* GetValueFromStr
	* @access private
	*/
	private function GetValueFromStr($params,$fieldname,$defaultval) {
		if( is_array($params) && array_key_exists($fieldname,$params))
		return $params[$fieldname];
		else
		return $defaultval;
	}
	/**
	* __createTableQuery
	* @access private
	*/
	private function __createTableQuery() {
		return "create table ".$this->DBObject->__table."(\n\tid ". DBObj_Setup:: ID_KEY_TYPE." auto_increment, PRIMARY KEY  (id),\n\tlockRecord ". DBObj_Setup:: LOCK_TYPE.",\n\tsuperId ". DBObj_Setup:: ID_KEY_TYPE."\n) TYPE=InnoDB AUTO_INCREMENT=100 ;\n";
	}
	/**
	* ControleAndCreateTable
	* @access private
	*/
	private function ControleAndCreateTable() {
		$rep = $this->Dbh->query("SHOW TABLE STATUS LIKE '".$this->DBObject->__table."';");
		$no_found = ( DB:: isError($rep) || ($rep->numRows() != 1));
		if($no_found) {
			$q = $this->__createTableQuery();
			$rep = &$this->Dbh->query($q);
			if( DB:: isError($rep)) {
				$this->Return .= "DB::query - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
				return false;
			}
			$this->Return .= "Table '".$this->DBObject->__table."' crèe dans la base de donnèe.{[newline]}";
		}
		else {
			$field_info = $this->Dbh->tableInfo($rep);
			$engine_id = 0;
			$idx = 0;
			foreach($field_info as $fi) {
				if( strtolower($fi['name']) == 'engine')$engine_id = $idx;
				$idx++;
			}
			$row = $rep->fetchRow();
			if($row[$engine_id] != 'InnoDB') {
				$q = "alter table ".$this->DBObject->__table." TYPE=InnoDB;";
				$rep = &$this->Dbh->query($q);
				if( DB:: isError($rep)) {
					$this->Return .= "DB::alter - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
					return false;
				}
				$this->Return .= "Table '".$this->DBObject->__table."' modifié (InnoDB) dans la base de donnèe.{[newline]}";
			}
			else $this->Return .= "Controle de la table '".$this->DBObject->__table."'.{[newline]}";
		}
		return true;
	}

	/**
	* ReaffectAutoinc
	* @access private
	*/
	private function ReaffectAutoinc() {
		$q = "ALTER TABLE ".$this->DBObject->__table." AUTO_INCREMENT =100";
		$rep = $this->Dbh->query($q);
		$this->Return .= "Refresh AutoInc";
		if( DB:: isError($rep))
			$this->Return .= "{".$rep->getMessage()."}";
		$this->Return .= "{[newline]}";
	}
	/**
	* getCurrentFieldDescription
	* @access private
	*/
	private function getCurrentFieldDescription() {
		$q = "SHOW FULL FIELDS FROM ".$this->DBObject->__table.";";
		$rep = $this->Dbh->query($q);
		if( DB:: isError($rep)) {
			$this->Return .= "DB::query - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
			return null;
		}
		$field_info = $this->Dbh->tableInfo($rep);
		$name_id = 0;
		$type_id = 1;
		$null_id = 3;
		$idx = 0;
		foreach($field_info as $fi) {
			if($fi['name'] == 'field')$name_id = $idx;
			elseif ($fi['name'] == 'type')$type_id = $idx;
			elseif ($fi['name'] == 'null')$null_id = $idx;
			$idx++;
		}
		$current_fields = array();
		while($row = $rep->fetchRow()) {
			$col_name = $row[$name_id];
			$current_fields[$col_name] = $col_name." ".$row[$type_id];
			if( trim($row[$null_id]) == "YES")$current_fields[$col_name] .= " NULL";
			else $current_fields[$col_name] .= " NOT NULL";
		}
		$rep->free();
		return $current_fields;
	}
	/**
	* __alterTableQuery
	* @access private
	*/
	private function __alterTableQueryByField($currentFields,$columnName,$fieldDescription) {
		$q = "";
		if( array_key_exists($columnName,$currentFields)) {
			if($currentFields[$columnName] != $fieldDescription) {
				$q = "ALTER TABLE ".$this->DBObject->__table." MODIFY ".$fieldDescription.";\n";
				$this->Return .= " !! ".$currentFields[$columnName]."=>$fieldDescription{[newline]}";
			}
		}
		else $q = "ALTER TABLE ".$this->DBObject->__table." ADD ".$fieldDescription.";\n";
		return $q;
	}
	/**
	* checkFields
	* @access private
	*/
	private function alterTableByField($currentFields,$columnName,$fieldDescription) {
		$success = true;
		$q = $this->__alterTableQueryByField($currentFields,$columnName,$fieldDescription);
		if($q != "") {
			$rep = $this->Dbh->query($q);
			if( DB:: isError($rep)) {
				$this->Return .= "DB::query - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->Return .= "Champ '$columnName' ajoutè ou modifier. ($q){[newline]}";
		}
		return $success;
	}
	/**
	* checkFields
	* @access private
	*/
	private function __getFieldSQL($col_name,$item) {
		global $field_dico;
		$type = $item['type'];
		$col_type = $field_dico[$type];
		$param = $item['params'];
		if($col_type[2] != "") {
			switch($type) {
			case 0:
				// integer
				$val_min = $this->GetValueFromStr($param,"Min",0);
				$val_max = $this->GetValueFromStr($param,"Max",1000);
				$size = max( strlen("$val_min"), strlen("$val_max"));
				$type_txt = sprintf($col_type[2],$size);
				break;
			case 1:
				// real
				$val_min = $this->GetValueFromStr($param,"Min",0);
				$val_max = $this->GetValueFromStr($param,"Max",1000);
				$prec = $this->GetValueFromStr($param,"prec",2);
				$val_min_int = (int)$val_min;
				$val_max_int = (int)$val_max;
				$size = max( strlen("$val_min_int"), strlen("$val_max_int"))+$prec;
				$type_txt = sprintf($col_type[2],$size,$prec);
				break;
			case 2:
				// string
				$type_txt = sprintf($col_type[2],$this->GetValueFromStr($param,"Size",50));
				break;
			default :
				$type_txt = $col_type[2];
			}
			$field_str = $col_name." ".$type_txt;
			if( array_key_exists('notnull',$item) && ($item['notnull']))$field_str .= " NOT NULL";
			else $field_str .= " NULL";
			return $field_str;
		}
		return '';
	}
	/**
	* checkFields
	* @access private
	*/
	private function checkFields($currentFields) {
		$success = true;
		if( array_key_exists('id',$currentFields) && ($currentFields['id'] != "id ". DBObj_Setup:: ID_KEY_TYPE)) {
			$q = "ALTER TABLE ".$this->DBObject->__table." CHANGE `id` ". DBObj_Setup:: ID_KEY_TYPE." AUTO_INCREMENT";
			$rep = $this->Dbh->query($q);
			if( DB:: isError($rep)) {
				$this->Return .= "DB::query - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->Return .= "Champ id ajoutè ou modifier ('".$currentFields['id']."'->'id ". DBObj_Setup:: ID_KEY_TYPE."'). {[newline]}";
		}
		$success = $this->alterTableByField($currentFields,'lockRecord','lockRecord '. DBObj_Setup:: LOCK_TYPE);
		$success = $this->alterTableByField($currentFields,'superId','superId '. DBObj_Setup:: ID_KEY_TYPE);
		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$field_str = $this->__getFieldSQL($col_name,$item);
			if($field_str != '')$success = $this->alterTableByField($currentFields,$col_name,$field_str);
		}
		foreach($currentFields as $fieldname => $val)if(($fieldname != 'id') && ($fieldname != 'lockRecord') && ($fieldname != 'superId') && ! array_key_exists($fieldname,$this->DBObject->__DBMetaDataField)) {
			$q = "ALTER TABLE ".$this->DBObject->__table." DROP COLUMN ".$fieldname.";";
			$rep = $this->Dbh->query($q);
			if( DB:: isError($rep)) {
				$this->Return .= "DB::query - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->Return .= "Champ '$fieldname' supprimé.{[newline]}";
		}
		return $success;
	}
	/**
	* CurrentIndexes
	* @access private
	*/
	private function CurrentIndexes() {
		$q = "SHOW INDEX FROM ".$this->DBObject->__table.";";
		$rep = $this->Dbh->query($q);
		if( DB:: isError($rep)) {
			$this->Return .= "DB::query - creation DB : '".$rep->getMessage()."' dans '$q'{[newline]}";
			return false;
		}
		$field_info = $this->Dbh->tableInfo($rep);
		$Key_name_id = 0;
		$Column_name_id = 1;
		$idx = 0;
		foreach($field_info as $fi) {
			if($fi['name'] == 'key_name')$Key_name_id = $idx;
			elseif ($fi['name'] == 'column_name')$Column_name_id = $idx;
			$idx++;
		}
		$current_indexes = array();
		while($row = $rep->fetchRow()) {
			$Key_name = $row[$Key_name_id];
			if($Key_name != "PRIMARY") {
				if( array_key_exists($Key_name,$current_indexes))$index_desc = $current_indexes[$Key_name];
				else $index_desc = "CREATE INDEX ".$Key_name." ON ".$this->DBObject->__table." (";
				if($index_desc[ strlen($index_desc)-1] != '(')$index_desc .= ", ";
				$index_desc .= $row[$Column_name_id];
				$current_indexes[$Key_name] = $index_desc;
			}
		}
		$rep->free();
		foreach($current_indexes as $Key_name => $index_desc)$current_indexes[$Key_name] = $index_desc.")";
		return $current_indexes;
	}
	/**
	* modifIndexQuery
	* @access private
	*/
	private function __createIndexQuery($index_name,$Indexfields = array()) {
		$create_index = "CREATE INDEX ".$index_name." ON ".$this->DBObject->__table." (";
		$field_index = "";
		foreach($Indexfields as $Indexfield) {
			$field_index .= $Indexfield.", ";
		}
		$field_index = substr($field_index,0,-2);
		if($field_index == "")
		return "";
		$create_index .= $field_index.");\n";
		return $create_index;
	}
	/**
	* modifIndexQuery
	* @access private
	*/
	private function modifIndexQuery($index_name,$current_indexes,$Indexfields = array()) {
		$create_index = $this->__createIndexQuery($index_name,$Indexfields);
		if( array_key_exists($index_name,$current_indexes)) {
			$old_index = $current_indexes[$index_name];
			if($old_index != $create_index)
			return "DROP INDEX ".$index_name." ON ".$this->DBObject->__table.";".$create_index;
			else
			return "";
		}
		else
		return $create_index;
	}
	/**
	* RunIndexQuery
	* @access private
	*/
	private function RunIndexQuery($index_name,$q,&$current_indexes) {
		if($index_name != "") {
			if( array_key_exists($index_name,$current_indexes)) {
				$current_indexes[$index_name] = "";
				//$this->Return.="### Index '$index_name' remove ".$current_indexes[$index_name]." ###{[newline]}";
			}
			$q_list = split(';',$q);
			foreach($q_list as $item)if( trim($item) != "") {
				$rep = $this->Dbh->query($item);
				if( DB:: isError($rep)) {
					$this->Return .= "DB::query - modification index : '".$rep->getMessage()."' dans '$item'{[newline]}";
					return false;
				}
				else $this->Return .= "Index '$index_name' modifiè.{[newline]}";
			}
		}
		return true;
	}
	/**
	* checkIndexes
	* @access private
	*/
	private function checkIndexes() {
		$current_indexes = $this->CurrentIndexes();
		if(! is_array($current_indexes))
		return false;
		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$type = $item['type'];
			$index_name = "";
			$q = "";
			switch($type) {
			case 8:
				// enumeration
				$index_name = "E_".$col_name;
				$q = $this->modifIndexQuery($index_name,$current_indexes,array($col_name));
				break;
			case 10:
				// reference
				$index_name = "R_".$col_name;
				$q = $this->modifIndexQuery($index_name,$current_indexes,array($col_name));
				break;
			}
			if(!$this->RunIndexQuery($index_name,$q,$current_indexes))
			return false;
		}
		foreach($this->DBObject->__DBCustomIndexes as $index_name => $item) {
			$index_name = "C_".$index_name;
			$q = $this->modifIndexQuery($index_name,$current_indexes,$item);
			if(!$this->RunIndexQuery($index_name,$q,$current_indexes))
			return false;
		}
		foreach($current_indexes as $Key_name => $index_desc)if($index_desc != "") {
			$q = "DROP INDEX ".$Key_name." ON ".$this->DBObject->__table;
			$rep = &$this->Dbh->query($q);
			if( DB:: isError($rep)) {
				$this->Return .= "DB::query - delete index : '".$rep->getMessage()."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->Return .= "Index '$Key_name' supprimè.{[newline]}";
		}
		return true;
	}
	/**
	 * Controle et modifie la structure d'une table DB
	 *
	 * @return array(boolean,string) tableau:succes + retour console
	 */
	public function execute() {
		$this->Return = "";
		if(!$this->ControleAndCreateTable())
		return false;
		$old_field = $this->GetCurrentFieldDescription();
		if($old_field == null)
		return false;
		if(!$this->CheckFields($old_field))
			return false;
		$this->ReaffectAutoinc();
		return $this->CheckIndexes();
	}
	/**
	 * Retour le script SQL de creation de cette table
	 *
	 * @return string
	 */
	public function describeSQLTable($addDrop = false) {
		$currentFields = array();
		$q = "";
		if($addDrop)$q .= "DROP TABLE IF EXISTS ".$this->DBObject->__table.";\n";
		$q .= $this->__createTableQuery();
		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$type_txt = $this->__getFieldSQL($col_name,$item);
			if($type_txt != '')$q .= $this->__alterTableQueryByField($currentFields,$col_name,$type_txt);
		}
		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$type = $item['type'];
			$index_name = "";
			switch($type) {
			case 8:
				// enumeration
				$index_name = "E_".$col_name;
				$q .= $this->__createIndexQuery($index_name,array($col_name));
				break;
			case 10:
				// reference
				$index_name = "R_".$col_name;
				$q .= $this->__createIndexQuery($index_name,array($col_name));
				break;
			}
		}
		foreach($this->DBObject->__DBCustomIndexes as $index_name => $item) {
			$index_name = "C_".$index_name;
			$q .= $this->__createIndexQuery($index_name,$item);
		}
		return $q;
	}
	/**
	 * Retour le contenu SQL de cette table
	 *
	 * @return string
	 */
	public function extractSQLData() {
		$query = "SELECT * FROM ".$this->DBObject->__table;
		$rep = $this->Dbh->query($query);
		if( DB:: isError($rep))
		return "-- ".$rep->getMessage()."--$query--\n";
		$q = "";
		$names = "";
		$field_info = $this->Dbh->tableInfo($rep);
		foreach($field_info as $fi) {
			$names .= $fi['name'].",";
		}
		$names = substr($names,0,-1);
		while($row = $rep->fetchRow()) {
			$values = "";
			foreach($row as $val) {
				$values .= "'". str_replace("'","''",$val)."',";
			}
			$values = substr($values,0,-1);
			if($values != '')$q .= "INSERT INTO ".$this->DBObject->__table.' ('.$names.') VALUES ('.$values.");\n";
		}
		return $q;
	}
}
//@END@
?>
