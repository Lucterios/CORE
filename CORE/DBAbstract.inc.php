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
// --- Last modification: Date 18 January 2010 20:14:33 By  ---

//@BEGIN@
/**
 * fichier gérant le DBObject
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage DBObject
 */

/**
* DBOBJ_INT
* @access private
*/
define('DBOBJ_INT',1);

/**
* DBOBJ_STR
* @access private
*/
define('DBOBJ_STR',2);

/**
* DBOBJ_CHILD
* @access private
*/
define('DBOBJ_CHILD',3);

/**
* DBOBJ_STORAGE
* @access private
*/
define('DBOBJ_STORAGE',4);

/**
* DBOBJ_METHOD
* @access private
*/
define('DBOBJ_METHOD',5);


/**
* field_dico
* @access private
*/
global $field_dico;
$field_dico = array();
$field_dico[0] = array( DBOBJ_INT,"Entier","int(%d)");
$field_dico[1] = array( DBOBJ_INT,"Réel","decimal(%d,%d)");
$field_dico[2] = array( DBOBJ_STR,"Chaîne","varchar(%d)");
$field_dico[3] = array( DBOBJ_STR,"Booléen","enum('n','o')");
$field_dico[4] = array( DBOBJ_STR,"Date","date");
$field_dico[5] = array( DBOBJ_STR,"Heure","time");
$field_dico[6] = array( DBOBJ_STR,"Date/Heure","datetime");
$field_dico[7] = array( DBOBJ_STR,"Text long","longtext");
$field_dico[8] = array( DBOBJ_INT,"Enumèration","tinyint(3)");
$field_dico[9] = array( DBOBJ_CHILD,"Enfants","");
$field_dico[10] = array( DBOBJ_INT,"Rèfèrent","int(10) unsigned");
$field_dico[11] = array( DBOBJ_STORAGE,"Fonction","");
$field_dico[12] = array( DBOBJ_METHOD,"Methode (chaine)","");
$field_dico[13] = array( DBOBJ_METHOD,"Methode (réel)","");

require_once"dbcnx.inc.php";
global $connect;

/**
* Classe abstraite au DBObject Luctèrios
*
* Classe principale de manipulation des tables utilisèes par Luctérios. Repose sur PEAR/DB_DataObject
*
* Cette classe permet diffèrentes manipulations des tables:
* 1. Déscription, crèation et mise à jour des tables de l'application
* 2. Abstraction d'un enregistrement sous forme d'un objet : insertion, selection, modification, suppression.
* 3. Recherche simple et complexe d'enregistrement.
* 4. Association de traitements (actions, methdes) à une table.
* @package Lucterios
* @subpackage DBObject
* @author Pierre-Oliver Vershoore/Laurent Gay
*/
class DBObj_Abstract {
	/**
	 * Nom de la table MySQL
	 *
	 * @var string
	 */
	public $tblname = '';

	/**
	 * Titre de la table MySQL
	 *
	 * @var string
	 */
	public $Title = '';

	/**
	 * Nom de l'extension qui possède cette table
	 *
	 * @var string
	 */
	public $extname = '';

	/**
	 * Clef primaire de l'enregistrement
	 *
	 * @var integer
	 */
	public $id=null;

	/**
	 * Champ de vérou
	 *
	 * @var string
	 */
	public $lockRecord=null;

	/**
	 * Chaine descriptif de l'enregistrement
	 *
	 * @access public
	 * @var string
	 */
	public $__toText = '';

	/**
	 * Description de chaques champs de la table
	 *
	 * @access public
	 * @var array
	 */
	public $__DBMetaDataField = array();

	/**
	 * Liste des indexes personnalisées
	 *
	 * @access public
	 * @var array
	 */
	public $__DBCustomIndexes = array();

	/**
	 * Liste d'enregistrement par défaut.
	 *
	 * @var array
	 */
	public $DefaultFields = array();

	/**
	 * Nombre de champs utilisés pour determiner si les enregistrements par defaut existes déjà .
	 *
	 * @var integer
	 */
	public $NbFieldsCheck = 1;

	/**
	 * Nom de la classe d'héritage
	 *
	 * @access public
	 * @var string
	 */
	public $Heritage = "";

	/**
	 * Clef lié de l'enregistrement mère
	 *
	 * @var integer
	 */
	public $superId = null;

	/**
	 * Object mère
	 *
	 * @var DBObj_Basic
	 * @access public
	 */
	public $__super = null;

	/**
	 * DNS
	 * @access private
	 */
	public $_database_dsn = DSN;

	/**
	 * Nombre d'enregistrement de la dernière requete
	 *
	 * @var int
	 * @access public
	 */
	public $N=0;


	protected $is_super = false;

	/**
	 * Constructeur DBObj_Basic
	 *
	 * @return DBObj_Basic
	 */
	public function __construct($is_super = false) {
		$this->is_super = $is_super;
		$this->__super = null;

		foreach($this->__DBMetaDataField as $col_name => $item)
			$this->$col_name=null;
	}

	protected $__son = null;

	public function getSon() {
		if(($this->__son == null) && ($this->id>0)) {
			require_once('CORE/extensionManager.inc.php');
			$class_list = getDaughterClassesList($this->extname.'/'.$this->tblname);
			$file_class_name = '';
			$class_name = '';
			$son_id = 0;
			foreach($class_list as $key => $item) {
				global $connect;
				$q = 'SELECT id FROM '. str_replace('/','_',$key).' WHERE superId='.$this->id;
				$res = $connect->execute($q);
				if($first_row = $connect->getRow($res)) {
					list($file_class_name,$class_name) = $this->getTableAndClass($key);
					$son_id = $first_row[0];
				}
				$this->debug("getSon:Q=$q - son_id=$son_id",3);
			}
			if($class_name != '') {
				require_once($file_class_name);
				$this->__son = new $class_name();
				$this->__son->get($son_id);
			}
		}
		return $this->__son;
	}

	public function getMotherId($ClassMother) {
		if( get_class($this) == $ClassMother)
		return $this->id;
		if($this->Heritage != "")
		return $this->Super->getMotherId($ClassMother);
		return 0;
	}

	/**
	 * Retourne un tableau donnant le type de chaque champs persistants.
	 *
	 * @return array
	 */
	public function table($withFunction=false,$withMethod=false) {
		global $field_dico;
		$tbl_fld = array('id' => DBOBJ_INT,'lockRecord' => DBOBJ_STR, 'superId' => DBOBJ_INT);
		foreach($this->__DBMetaDataField as $col_name => $item) {
			$dbt = $field_dico[$item['type']][0];
			if (($withMethod || ($dbt != DBOBJ_METHOD)) &&  ($dbt != DBOBJ_CHILD) && ($withFunction || ($dbt != DBOBJ_STORAGE)))
				$tbl_fld[$col_name] = $dbt;
		}
		return $tbl_fld;
	}

	/**
	 * keys
	 *
	 * @return string
	 * @access private
	 */
	public function keys() {
		return array('id');
	}

	/**
	 * Evaluateur de chaine
	 *
	 * Evalue le parametre $TextEvalable en remplacant.
	 * Chaque identifiant de champs, précédé par un dollard, est remplacé par sa valeur DB
	 * Si le champ n'est pas persistant (reference ou fils), il est remplacé par sont toText()
	 * @param string $TextEvalable
	 * @return string
	 */
	public function evalByText($TextEvalable) {
		$ret = $TextEvalable;
		if($ret[0] == '#') {
			$fct_name = substr($ret,1);
			$ret = $this->$fct_name();
		}
		else foreach($this->getDBMetaDataField() as $field_names => $field_item) {
			if( strpos($ret,'$'.$field_names) !== FALSE) {
				$value = $this->$field_names;
				if($field_item['type'] == 5) {
					require_once'xfer.inc.php';
					$value = convertTime($value);
				}
				elseif ($field_item['type'] == 4) {
					require_once'xfer.inc.php';
					$value = convertDate($value);
				}
				elseif ($field_item['type'] == 8) {
					$params = $field_item['params'];
					$enum = $params['Enum'];
					$value = $enum[(int)$value];
				}
				elseif (($field_item['type'] == 9) || ($field_item['type'] == 10)) {
					$value = $this->getField($field_names);
					if( is_object($value))$value = $value->toText();
					else $value = "---";
				}
				$ret = str_replace('$'.$field_names,$value,$ret);
			}
		}
		return $ret;
	}

	/**
	 * Description de l'enregistrement
	 *
	 * Correspond à $this->evalByText($this->__toText) ou à $this->id
	 * @return string
	 */
	public function toText() {
		$son = $this->getSon();
		if($son != null) {
			return $son->toText();
		}
		elseif (!isset($this->__toText) || ($this->__toText == "")) {
			return "".$this->id;
		}
		else {
			return $this->evalByText($this->__toText);
		}
	}

	/**
	 * List de description de chaques champs de la table
	 *
	 * @access public
	 * @var array
	 */
	public function getDBMetaDataField() {
		if($this->Heritage != "") {
			$part1 = array_slice($this->__DBMetaDataField,0,$this->PosChild);
			$part2 = array_slice($this->__DBMetaDataField,$this->PosChild);
			$fields = array();
			foreach($part1 as $key => $field) $fields[$key] = $field;
			$cur_super = $this->Super;
			$part_sup = $cur_super->getDBMetaDataField();
			foreach($part_sup as $key => $field)$fields[$key] = $field;
			foreach($part2 as $key => $field)$fields[$key] = $field;
		}
		else $fields = $this->__DBMetaDataField;
		return $fields;
	}

	/**
	 * Retourne l'ensemble des champs references ou liés à la table $RefTableName
	 *
	 * @param string $RefTableName
	 * @return array
	 */
	public function getFieldEditable($RefTableName = "",$nbfield = -1) {
		$FieldNames = array();
		$meta_data_field = $this->getDBMetaDataField();
		foreach($meta_data_field as $field_names => $field_item) {
			if(($field_item['type'] != 10) || ($field_item['params']['TableName'] != $RefTableName))
				array_push($FieldNames,$field_names);
		}
		if($nbfield>-1)
			return array_slice($FieldNames,0,$nbfield);
		else
			return $FieldNames;
	}

	/**
	 * retourne le nom du fichier PHP decrivant la table/classe
	 *
	 * @param string $tbl_select
	 * @return string
	 */
	public function getTableName($tbl_select,$ByLeft = false,$sep = '_') {
		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		if($ByLeft)
			$pos = strpos($tbl_select,$sep);
		else
			$pos = strrpos($tbl_select,$sep);
		if($pos === false) {
			$extName = "";
			$tableName = $tbl_select;
		}
		else {
			$extName = substr($tbl_select,0,$pos);
			$tableName = substr($tbl_select,$pos+1);
		}
		if($extName != "CORE")
			$table_file_name = "extensions/$extName/$tableName.tbl.php";
		else
			$table_file_name = "$extName/$tableName.tbl.php";
		if(! is_file($rootPath.$table_file_name))
			$table_file_name = DBObj_Basic:: getTableName($tbl_select, true);
		else
			$table_file_name=$rootPath.$table_file_name;
		return $table_file_name;
	}

	/**
	 * retourne le nom du fichier PHP decrivant la table/classe + la classe
	 *
	 * @param string $Heritage
	 * @return array(string,string)
	 */
	public function getTableAndClass($Heritage) {
		$file_class_name = $this->getTableName($Heritage, true,'/');
		$class_name = 'DBObj_'. str_replace('/','_',$Heritage);
		return array($rootPath.$file_class_name,$class_name);
	}

	/**
	 * assigne chaque champs référencés dans le tableau $object
	 *
	 * @param array $object
	 */
	public function setFrom($object) {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				$son->setFrom($object);
			}
		}
		if (is_object($object))
			$object=get_object_vars($object);
		if (is_array($object)) {
			$fields = $this->table(true,true);
			foreach($fields as $field_name => $field_item) {
				if ($field_item!=DBOBJ_METHOD) {
					if (isset($object[$field_name]))
						$this->$field_name=$object[$field_name];
					else if (isset($object[$this->__table.".".$field_name]))
						$this->$field_name=$object[$this->__table.".".$field_name];
				}
			}
			foreach($fields as $field_name => $field_item) {
				if (isset($object[$field_name]) && ($field_item==DBOBJ_METHOD)) {
						$params=$this->__DBMetaDataField[$field_name]['params'];
						$method=$params['MethodSet'];
						$this->Call($method,$object[$field_name]);
				}
			}
			if($this->Heritage != "") {
				$ret=$this->Super->setFrom($object);
				if ($ret && ($this->superId>0) && empty($this->Super->id)) {
					$this->Super->get($this->superId);
				}
				return $ret;
			}
			return true;
		}
		return false;
	}

	protected function _prepQuery($withValue=false){
		$tables=array();
		$fields=array();
		$wheres=array();
		if($this->Heritage != "") {
			list($fields,$tables,$wheres)=$this->Super->_prepQuery();
			$wheres[]=$this->__table.".superId=".$this->Super->__table.".id";
		}
		$tables[]=$this->__table;
		$field_names = $this->table(true);
		foreach($field_names as $field_name => $field_item) {
			if ($field_item!=DBOBJ_STORAGE)
				$fields[]=$this->__table.".".$field_name;
			else {
				$params=$this->__DBMetaDataField[$field_name]['params'];
				$nb_field=max(1,(int)$params['NbField']);
				$field_text=$this->__table.".id";
				if ($nb_field>1){
					for($i=1;$i<$nb_field;$i++)
						$field_text.=",NULL";
				}
				$fields[]= $params['Function']."(".$field_text.") AS ".$field_name;
			}
			if ($withValue && ($field_name!='superId')) {
				if(!is_null($this->$field_name)) {
					$value = $this->$field_name;
					if ($field_item==DBOBJ_STR) {
						$value = str_replace("'","''",$value);
						$fied_eq_value="$field_name='$value'";
					}
					else if ($field_item!=DBOBJ_STORAGE)
						$fied_eq_value="$field_name=$value";
					$wheres[]=$this->__table.".".$fied_eq_value;
				}
			}
		}
		return array($fields,$tables,$wheres);
	}

	/**
	 * selectionne 1 enregistrement
	 *
	 * @param int $id
	 */
	public function get($id) {
		$this->__son = null;
		if ($id>0) {
			list($fields,$tables,$wheres)=$this->_prepQuery();
			$wheres[]=$this->__table.".id=".$id;
			$q="SELECT ".implode(',',$fields)." FROM ".implode(',',$tables)." WHERE ".implode(' AND ',$wheres);
			global $connect;
			$qId= $connect->execute($q,true);
			if ($connect->getNumRows($qId)!=1) {
				__log($q,'Query get failure (nb)='.$connect->getNumRows($qId));
				require_once"Lucterios_Error.inc.php";
				throw new LucteriosException( IMPORTANT,"Selection impossible{[newline]}Veuillez rafraichir votre application.");
			}
			$this->debug("get($id):Q=$q",2);
			$row=$connect->getRowByName($qId);
			$result = $this->setFrom($row);
			return $result;
		}
		else
			return false;
	}

	private $lastQuery=null;
	/**
	 * selectionne des enregistrements
	 *
	 * @param string $string
	 */
	public function query($query) {
		$this->__son = null;
		global $connect;
		$this->lastQuery= $connect->execute($query,true);
		$result = $connect->getNumRows($this->lastQuery);
		$this->N=$result;
		$this->debug("Query:Q=$query - nb=$result",2);
		return $result;
	}

	private $whereAddList=array();

	/**
	 * whereAdd
	 *
	 *
	 */
	public function whereAdd($whereAdd){
		$this->whereAddList[]=$whereAdd;
	}

	private $order=null;

	/**
	 * orderBy
	 *
	 *
	 */
	public function orderBy($order) {
		$this->order=$order;
	}

	/**
	 * find
	 *
	 *
	 */
	public function find() {
		list($fields,$tables,$wheres)=$this->_prepQuery(true);
		$query="SELECT ".implode(',',$fields)." FROM ".implode(',',$tables);
		foreach($this->whereAddList as $other_where)
			$wheres[]=$other_where;
		if (count($wheres)>0)
			$query.=" WHERE ".implode(' AND ',$wheres);
		if (is_string($this->order))
			$query.=" ORDER BY ".$this->order;
		$result = $this->query($query);
		return $result;
	}

	/**
	 * fetch
	 *
	 *
	 */
	public function fetch() {
		$this->__son = null;
		global $connect;
		$row=$connect->getRowByName($this->lastQuery);
		if ($row) {
			$this->__super = null;
			$result=$this->setFrom($row);
			$this->debug("fetch:row=".print_r($row,true)." \n result=$result ",5);
			return $result;
		}
		else
			return false;
	}

	/**
	 * supprime l'enregistrement
	 *
	 */
	public function delete() {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				return $son->delete();
			}
		}
		if($this->Heritage != "")
			$this->Super->delete();
		$q = "DELETE FROM ".$this->__table." WHERE id=".$this->id;
		global $connect;
		$result = $connect->execute($q);
		$this->debug("Delete:Q=$q >> $result",1);
		if($result == false) {
			__log($q,'Query delete failure:');
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Suppression impossible{[newline]}Veuillez rafraichir votre application.");
		}
		return $result;
	}
	/**
	 * ajoute 1 enregistrement
	 *
	 */
	public function insert() {
		$this->__son = null;
		if($this->Heritage != "") {
			$this->superId = $this->Super->insert();
		}

		$field_names=array();
		$field_values=array();
		$fields = $this->table();
		foreach($fields as $field_name => $field_item)
			if(!is_null($this->$field_name)) {
				$type=$this->__DBMetaDataField[$field_name]['type'];
				$value = $this->$field_name;
				if ($field_item==DBOBJ_STR) {
					$value = str_replace("'","''",$value);
					$field_values[]="'$value'";
					$field_names[]=$field_name;
				}
				else if (($type!=10) || (((int)$value)!=0)) {
					$field_values[]=$value;
					$field_names[]=$field_name;
				}
			}
		$q = "INSERT INTO ".$this->__table;
		$q .= " (".implode(' ,',$field_names).")";
		$q .= " VALUES (".implode(' ,',$field_values).")";
		global $connect;
		$result = $connect->execute($q);
		$this->debug("Insert:Q=$q -> $result",1);
		if($connect->errorCode) {
			__log($q,'Query insert failure:');
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Insertion impossible.");
		}
		$this->id=$result;
		if(!($result>0)) {
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Insertion impossible.");
		}
		return $result;
	}

	/**
	 * modifie l'enregistrement
	 *
	 */
	public function update() {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				return $son->update();
			}
		}
		$set=array();
		$fields = $this->table();
		unset($fields['id']);
		unset($fields['superId']);
		foreach($fields as $field_name => $field_item)
			if(!is_null($this->$field_name)) {
				$type=$this->__DBMetaDataField[$field_name]['type'];
				$value = $this->$field_name;
				if ($field_item==DBOBJ_STR) {
					$value = str_replace("'","''",$value);
					$fied_eq_value="$field_name='$value'";
				}
				else if (($type!=10) || (((int)$value)!=0))
					$fied_eq_value="$field_name=$value";
				if ($withTable)
					$set[]=$this->__table.'.'.$fied_eq_value;
				else
					$set[]=$fied_eq_value;
			}
		$q = "UPDATE ".$this->__table;
		$q .= " SET ".implode(' ,',$set);
		$q .= " WHERE id=".$this->id;
		global $connect;
		$result = $connect->execute($q);
		$this->debug("Update:Q=$q",1);
		if($result === false) {
			__log($q,'Query update failure:');
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Modification impossible{[newline]}Veuillez rafraichir votre application.");
		}
		if($this->Heritage != "")
			$this->Super->update();
		return $result;
	}

	/**
	 * Recherche le super objet d'une classe donnee.
	 *
	 * @param string $className
	 */
	public function getSuperObject($tableName) {
		$obj=$this;
		while (($obj!=null) && ($obj->__table!=$tableName))
			$obj=$obj->Super;
		return $obj;
	}

	/**
	 * retourne le nom de la classe DBObject correspondant
	 *
	 * @return string
	 */
	public function GetClassName() {
		return "DBObj_".$this->extname."_".$this->tblname;
	}

	/**
	 * Surcharge de __set
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function __set($key,$value) {
		if ($this->Heritage != "")
			$this->Super->$key = $value;
	}

	/**
	 * Surcharge de __get
	 *
	 * @param string $key
	 * @return unknown
	 */
	public function __get($key) {
		if($key == 'Super') {
			if(($this->Heritage != "") && ($this->__super == null)) {
				list($file_class_name,$class_name) = $this->getTableAndClass($this->Heritage);
				if(! class_exists($class_name)) {
					if(! is_file($file_class_name))
						throw new Exception("file $file_class_name notfound!");
					require_once($file_class_name);
					if(! class_exists($class_name))
						throw new Exception("class $class_name notfound!");
				}
				$this->__super = new $class_name( true);
				$this->__super->__son=$this;
			}
			return $this->__super;
		}
		$find=false;
		if (isset($this->__DBMetaDataField[$key])) {
			$type=$this->__DBMetaDataField[$key]['type'];
			$params=$this->__DBMetaDataField[$key]['params'];
			if (($type==12) || ($type==13) && ($params['MethodGet']!='')) {
				return $this->Call($params['MethodGet']);
			}
		}
		if ($this->__super != null)
			return $this->__super->$key;
	}

	/**
	* debug
	* @access private
	*/
	public function debug($message,$level) {
		global $connect;
		if ($connect->debugLevel>=$level) {
			require_once('CORE/log.inc.php');
			__log($message,"DBOBJ:".get_class($this)." ($level)");
		}
	}
}
//@END@
?>
