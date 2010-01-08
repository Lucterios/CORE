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
// --- Last modification: Date 08 January 2010 23:21:43 By  ---

//@BEGIN@
/**
 * fichier gérant la creation et la migration des tables DB
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage DBObject
 */

require_once("DBObject.inc.php");

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

	/**
	 * ID_KEY_TYPE
	 *
	 * @var string
	 * @access private
	 */
	const ID_KEY_TYPE = "int(10) unsigned NOT NULL";

	/**
	 * SUPERID_KEY_TYPE
	 *
	 * @var string
	 * @access private
	 */
	const SUPERID_KEY_TYPE = "int(10) unsigned NULL";

	/**
	 * LOCK_TYPE
	 *
	 * @var string
	 * @access private
	 */
	const LOCK_TYPE = "varchar(100) NOT NULL";

	/**
	 * Description (console) de l'action réaliser
	 *
	 * @var string
	 */
	public $RetMsg = "";

	/**
	 * remonte une exception en cas d'erreur
	 *
	 * @var boolean
	 */
	public $throwExcept = false;

	/**
	 * Constructeur DBObj_Setup
	 *
	 * @return DBObj_Setup
	 */
	public function __construct($aDBObj) {
		$this->DBObject = $aDBObj;
	}

	/**
	 * rafraichi les valeurs par defaut de la classe/table
	 *
	 * @return string retour console
	 */
	public function refreshDefaultValues() {
		$this->RetMsg .= "{[italic]}Rafraichissement par defaut de ".$this->DBObject->tblname."{[/italic]}{[newline]}";
		foreach($this->DBObject->DefaultFields as $field_values) {
			$refresh_data = false;
			if( array_key_exists('@refresh@',$field_values))
				$refresh_data = $field_values['@refresh@'];
			$field_id = -1;
			if( array_key_exists('id',$field_values) && ($field_values['id']!=''))
				$field_id = (int)$field_values['id'];
			$this->refreshDefaultValue($field_values,$field_id,$refresh_data);
		}
		$this->ReaffectAutoinc();
		return $this->RetMsg;
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

		$field_Values=$fieldValues;
		foreach($field_Values as $field_name=>$field_value) {
			$type=$this->DBObject->__DBMetaDataField[$field_name]['type'];
			if (($type==10) && (empty($field_value)))
				unset($fieldValues[$field_name]);
		}

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
		if ($fieldValues['id']=='')
			unset($fieldValues['id']);
		$DBObj->setFrom($fieldValues);
		if($nb_find>0) {
			if($refreshData) {
				$DBObj->update();
				$this->RetMsg .= "Refresh enregistrement - update #";
				$this->RetMsg .= $DBObj->id;
				$this->RetMsg .= ".{[newline]}";
			}
			else {
				$this->RetMsg .= "Refresh enregistrement - OK #";
				$this->RetMsg .= $DBObj->id;
				$this->RetMsg .= ".{[newline]}";
			}
		}
		else {
			if($fieldId == -1) {
				$DBObj->insert();
				$this->RetMsg .= "Refresh enregistrement - insert #";
				$this->RetMsg .= $DBObj->id;
				$this->RetMsg .= "{[newline]}";
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
				$DBObj->query($q);
				$this->RetMsg .= "Refresh enregistrement [$q] insert #$fieldId";
				$this->RetMsg .= "{[newline]}";
			}
		}
		return $this->RetMsg;
	}

	/**
	* GetValueFromStr
	* @access private
	*/
	private function GetValueFromStr($params,$fieldname,$defaultval) {
		if(is_array($params) && array_key_exists($fieldname,$params))
			return $params[$fieldname];
		else
			return $defaultval;
	}

	/**
	* __createTableQuery
	* @access private
	*/
	private function __createTableQuery() {
		return "create table ".$this->DBObject->__table."(\n\tid ". DBObj_Setup:: ID_KEY_TYPE." auto_increment, PRIMARY KEY  (id),\n\tlockRecord ". DBObj_Setup:: LOCK_TYPE.",\n\tsuperId ". DBObj_Setup:: SUPERID_KEY_TYPE."\n) TYPE=InnoDB AUTO_INCREMENT=100 ;\n";
	}

	/**
	* __showCreateTable
	* @access private
	*/
	private function __showCreateTable() {
		$resValue="";
		global $connect;
		if (method_exists($connect,'getRowByName')) {
			$rep = $connect->execute("SHOW CREATE TABLE ".$this->DBObject->__table.";",$this->throwExcept);
			if (($rep!=false) && ($connect->getNumRows($rep)==1)) {
				$row = $connect->getRowByName($rep);
				$resValue=$row['Create Table'];
			}
			else
				$this->RetMsg .= "Erreur SHOW CREATE TABLE : ".$connect->errorMsg;
		}
		return $resValue;
	}

	/**
	* ControleAndCreateTable
	* @access private
	*/
	private function ControleAndCreateTable() {
		global $connect;
		$rep = $connect->execute("SHOW TABLE STATUS LIKE '".$this->DBObject->__table."';",$this->throwExcept);
		$no_found = ( ($rep==false) || ($connect->getNumRows($rep) != 1));
		if($no_found) {
			$q = $this->__createTableQuery();
			$rep = $connect->execute($q,$this->throwExcept);
			if (!$rep) {
				$this->RetMsg .= "DB::query - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
				return false;
			}
			$this->RetMsg .= "Table '".$this->DBObject->__table."' crèe dans la base de donnèe.{[newline]}";
		}
		else {
			if (method_exists($connect,'getRowByName')) {
				$row = $connect->getRowByName($rep);
				$q = "";
				if ($row['engine'] != 'InnoDB')
					$q = "ALTER TABLE ".$this->DBObject->__table." TYPE=InnoDB;";
			}
			else
				$q = $this->__old_control_table($rep);

			if ($q != "") {
					$rep = $connect->execute($q,$this->throwExcept);
					if (!$rep) {
						$this->RetMsg .= "DB::alter - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
						return false;
					}
					$this->RetMsg .= "Table '".$this->DBObject->__table."' modifié (InnoDB) dans la base de donnèe.{[newline]}";
				}
				else
					$this->RetMsg .= "Controle de la table '".$this->DBObject->__table."'.{[newline]}";

		}
		return true;
	}

	/**
	* __old_control_table
	* @access private
	*/
	private function __old_control_table($qId){
		global $connect;
		if (empty($connect->dbh))
			return "";

		$rep=$connect->res[$qId];
		$field_info = $connect->dbh->tableInfo($rep);
		$engine_id = 0;
		$idx = 0;
		foreach($field_info as $fi) {
			if( strtolower($fi['name']) == 'engine')$engine_id = $idx;
			$idx++;
		}
		$row = $connect->getRow($qId);
		if($row[$engine_id] != 'InnoDB') {
			return "alter table ".$this->DBObject->__table." TYPE=InnoDB;";
		}
		else
			Return "";
	}


	/**
	* ReaffectAutoinc
	* @access private
	*/
	private function ReaffectAutoinc() {
		$q = "ALTER TABLE ".$this->DBObject->__table." AUTO_INCREMENT=100";
		global $connect;
		$rep = $connect->execute($q,$this->throwExcept);
		$this->RetMsg .= "Refresh AutoInc";
		if (!$rep)
			$this->RetMsg .= "{".$connect->errorMsg."}";
		$this->RetMsg .= "{[newline]}";
	}

	/**
	* getCurrentFieldDescription
	* @access private
	*/
	private function getCurrentFieldDescription() {
		$q = "SHOW FULL FIELDS FROM ".$this->DBObject->__table.";";
		global $connect;
		$rep = $connect->execute($q,$this->throwExcept);
		if (!$rep) {
			$this->RetMsg .= "DB::query - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
			return null;
		}
		if (method_exists($connect,'getRowByName')) {
			$current_fields = array();
			while($row = $connect->getRowByName($rep)) {
				$col_name = $row['COLUMNS.Field'];
				$current_fields[$col_name] = $col_name." ".$row['COLUMNS.Type'];
				if( trim($row['COLUMNS.Null']) == "YES")
					$current_fields[$col_name] .= " NULL";
				else
					$current_fields[$col_name] .= " NOT NULL";
			}
		}
		else
			$current_fields = $this->__old_field_description($rep);
		return $current_fields;
	}

	/**
	* __old_field_description
	* @access private
	*/
	private function __old_field_description($qId){
		global $connect;
		if (empty($connect->dbh))
			return array();

		global $connect;
		$rep=$connect->res[$qId];
		$field_info = $connect->dbh->tableInfo($rep);
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
		while($row = $connect->getRow($qId)) {
			$col_name = $row[$name_id];
			$current_fields[$col_name] = $col_name." ".$row[$type_id];
			if( trim($row[$null_id]) == "YES")
				$current_fields[$col_name] .= " NULL";
			else
				$current_fields[$col_name] .= " NOT NULL";
 		}
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
				$this->RetMsg .= " !! ".$currentFields[$columnName]."=>$fieldDescription{[newline]}";
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
			global $connect;
			$rep = $connect->execute($q,$this->throwExcept);
			if (!$rep) {
				$this->RetMsg .= "DB::query - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->RetMsg .= "Champ '$columnName' ajoutè ou modifier. ($q){[newline]}";
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
				$default="0";
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
				$default="0";
				break;
			case 2:
				// string
				$type_txt = sprintf($col_type[2],$this->GetValueFromStr($param,"Size",50));
				$default="''";
				break;
			case 3:
				// boolean
				$type_txt = $col_type[2];
				$default="'n'";
				break;
			case 4:
				// date
				$type_txt = $col_type[2];
				$default="'1900-01-01'";
				break;
			case 5:
				// heure
				$type_txt = $col_type[2];
				$default="'00:00:00'";
				break;
			case 6:
				// date heure
				$type_txt = $col_type[2];
				$default="'1900-01-01 00:00:00'";
				break;
			case 7:
				//long text
				$type_txt = $col_type[2];
				$default="''";
				break;
			case 8:
				//enum
				$type_txt = $col_type[2];
				$default="0";
				break;
			case 10:
				//reference
				$type_txt = $col_type[2];
				$default="";
				break;
			}
			$field_str = $col_name." ".$type_txt;
			if( array_key_exists('notnull',$item) && ($item['notnull']))
				$field_str .= " NOT NULL";
			else
				$field_str .= " NULL";
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
		global $connect;
		if( array_key_exists('id',$currentFields) && ($currentFields['id'] != "id ". DBObj_Setup:: ID_KEY_TYPE)) {
			$q = "ALTER TABLE ".$this->DBObject->__table." CHANGE `id` ". DBObj_Setup:: ID_KEY_TYPE." AUTO_INCREMENT";
			$rep=$connect->execute($q,$this->throwExcept);
			if (!$rep) {
				$this->RetMsg .= "DB::query - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->RetMsg .= "Champ id ajoutè ou modifier ('".$currentFields['id']."'->'id ". DBObj_Setup:: ID_KEY_TYPE."'). {[newline]}";
		}
		$success = $this->alterTableByField($currentFields,'lockRecord','lockRecord '. DBObj_Setup:: LOCK_TYPE);
		$success = $this->alterTableByField($currentFields,'superId','superId '. DBObj_Setup:: SUPERID_KEY_TYPE);
		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$field_str = $this->__getFieldSQL($col_name,$item);
			if($field_str != '')$success = $this->alterTableByField($currentFields,$col_name,$field_str);
		}
		foreach($currentFields as $fieldname => $val)if(($fieldname != 'id') && ($fieldname != 'lockRecord') && ($fieldname != 'superId') && ! array_key_exists($fieldname,$this->DBObject->__DBMetaDataField)) {
			$q = "ALTER TABLE ".$this->DBObject->__table." DROP COLUMN ".$fieldname.";";
			$rep=$connect->execute($q,$this->throwExcept);
			if (!$rep) {
				$this->RetMsg .= "DB::query - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
				$success = false;
			}
			else $this->RetMsg .= "Champ '$fieldname' supprimé.{[newline]}";
		}
		return $success;
	}

	/**
	* CurrentIndexes
	* @access private
	*/
	private function CurrentIndexes() {
		$q = "SHOW INDEX FROM ".$this->DBObject->__table.";";
		global $connect;
		$rep=$connect->execute($q,$this->throwExcept);
		if (!$rep) {
			$this->RetMsg .= "DB::query - creation DB : '".$connect->errorMsg."' dans '$q'{[newline]}";
			return false;
		}
		if (method_exists($connect,'getRowByName')) {
			$current_indexes = array();
			while($row = $connect->getRowByName($rep)) {
				$Key_name = $row['STATISTICS.Key_name'];
				if($Key_name != "PRIMARY") {
					if (array_key_exists($Key_name,$current_indexes))
						$index_desc = $current_indexes[$Key_name];
					else
						$index_desc = "CREATE INDEX ".$Key_name." ON ".$this->DBObject->__table." (";
					if($index_desc[strlen($index_desc)-1]!= '(')
						$index_desc .= ", ";
					$index_desc.= $row['STATISTICS.Column_name'];
					$current_indexes[$Key_name] = $index_desc;
				}
			}
		}
		else
			$current_indexes = $this->__old_current_indexes($rep);
		foreach($current_indexes as $Key_name => $index_desc)
			$current_indexes[$Key_name] = $index_desc.")";
		return $current_indexes;
	}

	/**
	* __old_current_indexes
	* @access private
	*/
	private function __old_current_indexes($qId){
		global $connect;
		if (empty($connect->dbh))
			return array();

		global $connect;
		$rep=$connect->res[$qId];

		$field_info = $connect->dbh->tableInfo($rep);
		$Key_name_id = 0;
		$Column_name_id = 1;
		$idx = 0;
		foreach($field_info as $fi) {
			if($fi['name'] == 'key_name')
				$Key_name_id = $idx;
			elseif ($fi['name'] == 'column_name')
				$Column_name_id = $idx;
			$idx++;
		}
 		$current_indexes = array();
		while($row = $connect->getRow($qId)) {
			$Key_name = $row[$Key_name_id];
 			if($Key_name != "PRIMARY") {
				if( array_key_exists($Key_name,$current_indexes))
					$index_desc = $current_indexes[$Key_name];
				else
					$index_desc = "CREATE INDEX ".$Key_name." ON ".$this->DBObject->__table." (";
				if($index_desc[ strlen($index_desc)-1] != '(')
					$index_desc .= ", ";
				$index_desc .= $row[$Column_name_id];
 				$current_indexes[$Key_name] = $index_desc;
 			}
 		}
		return $current_indexes;
	}

	/**
	* __createIndexQuery
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
	* ModifIndexQuery
	* @access private
	*/
	private function ModifIndexQuery($index_name,$current_indexes,$Indexfields = array()) {
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
				//$this->RetMsg.="### Index '$index_name' remove ".$current_indexes[$index_name]." ###{[newline]}";
			}
			global $connect;
			$q_list = split(';',$q);
			foreach($q_list as $item)
				if( trim($item) != "") {
					$rep=$connect->execute($item,$this->throwExcept);
					if (!$rep) {
						$this->RetMsg .= "DB::query - modification index : '".$connect->errorMsg."' dans '$item'{[newline]}";
						return false;
					}
					else
						$this->RetMsg .= "Index '$index_name' modifiè.{[newline]}";
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

		if($this->DBObject->Heritage != '') {
			$col_name="superId";
			$index_name = "R_".$col_name;
			$q = $this->ModifIndexQuery($index_name,$current_indexes,array($col_name));
			if(!$this->RunIndexQuery($index_name,$q,$current_indexes))
				return false;
		}

		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$type = $item['type'];
			$index_name = "";
			$q = "";
			switch($type) {
			case 8:
				// enumeration
				$index_name = "E_".$col_name;
				$q = $this->ModifIndexQuery($index_name,$current_indexes,array($col_name));
				break;
			case 10:
				// reference
				$index_name = "R_".$col_name;
				$q = $this->ModifIndexQuery($index_name,$current_indexes,array($col_name));
				break;
			}
			if(!$this->RunIndexQuery($index_name,$q,$current_indexes))
				return false;
		}
		foreach($this->DBObject->__DBCustomIndexes as $index_name => $item) {
			$index_name = "C_".$index_name;
			$q = $this->ModifIndexQuery($index_name,$current_indexes,$item);
			if(!$this->RunIndexQuery($index_name,$q,$current_indexes))
				return false;
		}
		foreach($current_indexes as $Key_name => $index_desc)if($index_desc != "") {
			$q = "DROP INDEX ".$Key_name." ON ".$this->DBObject->__table;
			global $connect;
			$rep=$connect->execute($q,$this->throwExcept);
			if (!$rep) {
				$this->RetMsg .= "DB::query - delete index : '".$connect->errorMsg."' dans '$q'{[newline]}";
				$success = false;
			}
			else
				$this->RetMsg .= "Index '$Key_name' supprimè.{[newline]}";
		}
		return true;
	}


	/**
	* CurrentContraintes
	* @access private
	*/
	public function CurrentContraints() {
		$current_contraints = array();
		$lines=split("\n",$this->__showCreateTable());
		foreach($lines as $line) {
			$line=trim($line);
			if (substr($line,0,11)=="CONSTRAINT ") {
				if (substr($line,-1)==',')
					$current_contraints[]=substr($line,0,-1);
				else
					$current_contraints[]=$line;
			}
		}
		return $current_contraints;
	}

	/**
	* __createContraintQuery
	* @access private
	*/
	private function __createContraintQuery($contraintName,$fieldName,$referenceParams) {
		$ref_table=$referenceParams['params']['TableName'];
		$table_file_name=$this->DBObject->getTableName($ref_table);

		// Vérification d'un dépendance optionnel non assurée
		$table_file_splited=split('/',$table_file_name);
		if ((count($table_file_splited)!=3) || is_dir("extensions/".$table_file_splited[1])) {
			$create_contraint = "CONSTRAINT `$contraintName` FOREIGN KEY (`".$fieldName."`) REFERENCES `".$ref_table."` (`id`) ON DELETE ";
			if ($referenceParams['notnull']) {
				require_once($table_file_name);
				$ref_class="DBObj_".$ref_table;
				$ref_obj=new $ref_class;
				$has_child_in_ref=false;
				foreach($ref_obj->__DBMetaDataField as $col_name => $item) {
					$type = $item['type'];
					if (($type==9) && ($item['params']['TableName']==$this->DBObject->__table) && ($item['params']['RefField']==$fieldName))
						$has_child_in_ref=true;
				}
				if (($has_child_in_ref) || ($fieldName=='superId'))
					$create_contraint.= "CASCADE";
				else
					$create_contraint.= "NO ACTION";
				$correct="DELETE FROM ".$this->DBObject->__table." WHERE NOT $fieldName IN (SELECT id FROM $ref_table);";
			}
			else {
				$create_contraint.= "SET NULL";
				$correct="UPDATE ".$this->DBObject->__table." SET $fieldName=NULL WHERE NOT $fieldName IN (SELECT id FROM $ref_table);";
			}
			return array($create_contraint,$correct);
		}
		return array("","");
	}

	/**
	* ModifContraintQuery
	* @access private
	*/
	private function ModifContraintQuery($contraintName,$currentcontraints,$fieldName,$referenceParams) {
		list($create_contraint,$correct) = $this->__createContraintQuery($contraintName,$fieldName,$referenceParams);
		if($contraintName != "") {
			if( array_key_exists($contraintName,$currentcontraints)) {
				$old_contraint = $currentcontraints[$contraintName];
				if($old_contraint != $create_contraint) {
					$q="ALTER TABLE `".$this->DBObject->__table."` DROP FOREIGN KEY `$contraintName`;";
					$q.=$correct;
					$q.="ALTER TABLE `".$this->DBObject->__table."` ADD ".$create_contraint.';';
					return $q;
				}
				else
					return "";
			}
			else {
				$q =$correct;
				$q.="ALTER TABLE `".$this->DBObject->__table."` ADD ".$create_contraint.';';
				return $q;
			}
		}
		return "";
	}

	/**
	* RunContraintQuery
	* @access private
	*/
	private function RunContraintQuery($contraintName,$q,&$currentcontraints) {
		if($contraintName != "") {
			if( array_key_exists($contraintName,$currentcontraints)) {
				$currentcontraints[$contraintName] = "";
			}
			global $connect;
			$q_list = split(';',$q);
			foreach($q_list as $item)
				if( trim($item) != "") {
					$rep=$connect->execute($item,$this->throwExcept);
					if (!$rep) {
						$this->RetMsg .= "DB::query - modification contrainte : '".$connect->errorMsg."' dans '$item'{[newline]}";
						return false;
					}
					else
						$this->RetMsg .= "Contrainte '$contraintName' modifiè.{[newline]}";
				}
		}
		return true;
	}

	/**
	* checkIndexes
	* @access private
	*/
	public function CheckContraints() {
		$current_contraints = $this->CurrentContraints();
		if(! is_array($current_contraints))
			return "Contrainte non controlable!!{[newline]}";

		if($this->DBObject->Heritage != '') {
			$col_name="superId";
			$contraint_name = $this->DBObject->__table."_".$col_name;
			$q = $this->ModifContraintQuery($contraint_name,$current_contraints,$col_name,array('description'=>'superId', 'type'=>10, 'notnull'=>true, 'params'=>array('TableName'=>$this->DBObject->Super->__table)));
			$this->RunContraintQuery($contraint_name,$q,$current_contraints);
		}

		foreach($this->DBObject->__DBMetaDataField as $col_name => $item) {
			$type = $item['type'];
			if($type==10) {
				// reference
				$contraint_name = $this->DBObject->__table."_".$col_name;
				$q = $this->ModifContraintQuery($contraint_name,$current_contraints,$col_name,$item);
				if(!$this->RunContraintQuery($contraint_name,$q,$current_contraints))
					break;
			}
		}
		global $connect;
		$connect->printDebug("Contrainte Msg:".$this->RetMsg);
		return $this->RetMsg;
	}

	/**
	 * Controle et modifie la structure d'une table DB
	 *
	 * @return array(boolean,string) tableau:succes + retour console
	 */
	public function execute() {
		$this->RetMsg = "";
		if(!$this->ControleAndCreateTable())
			return false;
		$old_field = $this->GetCurrentFieldDescription();
		if($old_field == null)
			return false;
		if(!$this->CheckFields($old_field))
			return false;
		$this->ReaffectAutoinc();
		$ret=$this->CheckIndexes();
		global $connect;
		$connect->printDebug("Setup Msg:".$this->RetMsg);
		return $ret;
	}

	/**
	 * Retour le script SQL de creation de cette table
	 *
	 * @return string
	 */
	public function describeSQLTable($addDrop = false) {
		$currentFields = array();
		$q = "";
		if($addDrop)
			$q .= "DROP TABLE IF EXISTS ".$this->DBObject->__table.";\n";
		$q .= $this->__showCreateTable();
		return $q;
	}

	/**
	 * Retour le contenu SQL de cette table
	 *
	 * @return string
	 */
	public function extractSQLData() {
		$query = "SELECT * FROM ".$this->DBObject->__table;
		global $connect;
		$rep=$connect->execute($query,$this->throwExcept);
		if (!$rep)
			return "-- ".$connect->errorMsg."--$query--\n";
		$q = "";
		$MetaDataField=$this->DBObject->__DBMetaDataField;
		$MetaDataField['superId']['type']=10;
		global $field_dico;
		$fetched=$connect->getRecord($rep);
		while($row = $fetched->fetch_assoc()) {
			$names = "";
			$values = "";
			foreach($row as $nom=>$val) {
				if (!empty($val)) {
					$names .= $nom.",";
					$type=$MetaDataField[$name]['type'];
					if ($field_dico[$type][0]==DBOBJ_INT)
						$values .= $val.",";
					else
						$values .= "'". str_replace("'","''",$val)."',";
				}
			}
			$names = substr($names,0,-1);
			$values = substr($values,0,-1);
			if($values != '')
				$q .= "INSERT INTO ".$this->DBObject->__table.' ('.$names.') VALUES ('.$values.");\n";
		}
		return $q;
	}
}
//@END@
?>
