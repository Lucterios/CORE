<?php
// This file is part of Lucterios, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// Thanks to have payed a donation for using this module.
// 
// Lucterios is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Lucterios; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// library file write by Lucterios SDK tool

//@BEGIN@
/**
 * fichier gerant le DBObject
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
$field_dico[1] = array( DBOBJ_INT,"R�el","decimal(%d,%d)");
$field_dico[2] = array( DBOBJ_STR,"Cha�ne","varchar(%d)");
$field_dico[3] = array( DBOBJ_STR,"Booleen","enum('n','o')");
$field_dico[4] = array( DBOBJ_STR,"Date","date");
$field_dico[5] = array( DBOBJ_STR,"Heure","time");
$field_dico[6] = array( DBOBJ_STR,"Date/Heure","datetime");
$field_dico[7] = array( DBOBJ_STR,"Text long","longtext");
$field_dico[8] = array( DBOBJ_INT,"�num�ration","tinyint(3)");
$field_dico[9] = array( DBOBJ_CHILD,"Enfants","");
$field_dico[10] = array( DBOBJ_INT,"R�f�rent","int(10) unsigned");
$field_dico[11] = array( DBOBJ_STORAGE,"Fonction","");
$field_dico[12] = array( DBOBJ_METHOD,"M�thode (chaine)","");
$field_dico[13] = array( DBOBJ_METHOD,"M�thode (r�el)","");

require_once("CORE/log.inc.php");
require_once("CORE/dbcnx.inc.php");
global $connect;

/**
* Classe abstraite au DBObject Lucterios
*
* Classe principale de manipulation des tables utilisees par Lucterios. 
*
* Cette classe permet differentes manipulations des tables:
* 1. Description, creation et mise a jour des tables de l'application
* 2. Abstraction d'un enregistrement sous forme d'un objet : insertion, selection, modification, suppression.
* 3. Recherche simple et complexe d'enregistrement.
* 4. Association de traitements (actions, methdes) a une table.
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
	 * Nom de l'extension qui possede cette table
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
	 * Champ de verou
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
	 * Liste des indexes personnalisees
	 *
	 * @access public
	 * @var array
	 */
	public $__DBCustomIndexes = array();

	/**
	 * Liste d'enregistrement par defaut.
	 *
	 * @var array
	 */
	public $DefaultFields = array();

	/**
	 * Nombre de champs utilises pour determiner si les enregistrements par defaut existes deja�.
	 *
	 * @var integer
	 */
	public $NbFieldsCheck = 1;

	/**
	 * Nom de la classe d'heritage
	 *
	 * @access public
	 * @var string
	 */
	public $Heritage = "";

	/**
	 * Clef lie de l'enregistrement mere
	 *
	 * @var integer
	 */
	public $superId = null;

	/**
	 * Object mere
	 *
	 * @var DBObj_Basic
	 * @access public
	 */
	public $__super = null;

	/**
	 * Nombre d'enregistrement de la derniere requete
	 *
	 * @var int
	 * @access public
	 */
	public $N=0;

	/**
	 * Offset de selection
	 *
	 * @var int
	 * @access public
	 */
	public $offset=0;

	/**
	 * Nombre d'enregistrement de selection
	 *
	 * @var int
	 * @access public
	 */
	public $rowCount=0;


	protected $is_super = false;

	/**
	 * Constructeur DBObj_Basic
	 *
	 * @param bool $is_super True si l'objet est instancie dans une logique d'heritage
	 */
	public function __construct($is_super = false) {
		$this->is_super = $is_super;
		$this->__super = null;

		foreach($this->__DBMetaDataField as $col_name => $item)
			$this->$col_name=null;
	}

	protected $__son = null;

	/**
	 * Retourne l'objet fils le plus bas hierachique
	 *
	 * @return DBObj_Abstract objet fils
	 */
	public function getSon() {
		if(($this->__son == null) && ($this->id>0)) {
			global $rootPath;
			if(!isset($rootPath)) $rootPath = "";
			require_once('CORE/extensionManager.inc.php');
			$class_list = getDaughterClassesList($this->extname.'/'.$this->tblname,$rootPath);
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

	/**
	 * Retourne l'objet fils le plus bas hierachique
	 * 
	 * @param string nom de la classe recherchee
	 * @return int ID de la classe mere (0 si non trouve)
	 */
	public function getMotherId($ClassMother) {
		if( get_class($this) == $ClassMother)
		return $this->id;
		if($this->Heritage != "")
		return $this->Super->getMotherId($ClassMother);
		return 0;
	}

	/**
	 * Retourne la list des classes heritantes
	 * 
	 * @return array list des classes
	 */
	public function get_classes_herited() {
		$ret=array();
		$root_obj = $this;
		while ($this->Super!=null) {
		  $root_obj = $this->Super;
		}
		$ret[] = get_class($root_obj);
		while ($root_obj->getSon()!=null) {
		    $root_obj = $root_obj->getSon();
		    $ret[] = get_class($root_obj);
		}
		return $ret;
	}

	/**
	 * Retourne un tableau donnant le type de chaque champs persistants.
	 *
	 * @param bool $withFunction avec champ 'fonction'
	 * @param bool $withMethod avec champ 'method'
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
	 * nom de la clef
	 *
	 * @return string
	 */
	public function keys() {
		return array('id');
	}

	/**
	 * Evaluateur de chaine
	 *
	 * Evalue le parametre $TextEvalable en remplacant.
	 * Chaque identifiant de champs, precede par un dollard, est remplace par sa valeur DB
	 * Si le champ n'est pas persistant (reference ou fils), il est remplace par sont toText()
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
	 * Correspond a $this->evalByText($this->__toText) ou a $this->id
	 * @return string
	 */
	public function toText() {
		if ($this->id>0) {
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
		return "";
	}

	/**
	 * List de description de chaques champs de la table
	 *
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
	 * Retourne l'ensemble des champs references ou lies a la table $RefTableName
	 *
	 * @param string $RefTableName
	 * @param int $nbfield nombre de champs desires (-1=tous)
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
	 * retourne le nom du fichier PHP decrivant la classe associee a une table
	 *
	 * @param string $tbl_select nom de table souhaitee
	 * @param bool $ByLeft recherche par la gauche
	 * @param string $sep separateur extension/classe
	 * @return string
	 */
	public static function getTableName($tbl_select,$ByLeft = false,$sep = '_') {
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
			$table_file_name = DBObj_Abstract::getTableName($tbl_select, true);
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
	public static function getTableAndClass($Heritage) {
		$file_class_name = DBObj_Abstract::getTableName($Heritage, true,'/');
		$class_name = 'DBObj_'. str_replace('/','_',$Heritage);
		return array($file_class_name,$class_name);
	}

	/**
	 * assigne chaque champs references dans le tableau $object
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
					else if (isset($object[strtolower($this->__table).".".$field_name]))
						$this->$field_name=$object[strtolower($this->__table).".".$field_name];
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

	/**
	 * Prepare une requete
	 *
	 * @param bool $withValue Avec valeurs
	 * @param bool $withFct Avec fonctions stockees
	 * @return array ensemble des champs, des tables et des conditions
	 */
	public function prepQuery($withValue=false,$withFct=true){
		$tables=array();
		$fields=array();
		$wheres=array();
		if($this->Heritage != "") {
			list($fields,$tables,$wheres)=$this->Super->prepQuery(false,$withFct);
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
				if ($withFct)
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
	 * @param bool $withFct Avec fonctions stockees
	 * @return bool
	 */
	public function get($id,$withFct=true) {
		$this->__son = null;
		if ($id>0) {
			list($fields,$tables,$wheres)=$this->prepQuery(false,$withFct);
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
     * @param int $offset offset de selection (limit)
     * @param int $row_count nb d'enregistrement de selection (limit)
	 * @return int nombre trouve
	 */
	public function query($query, $offset=null, $row_count=null) {
		$this->__son = null;
        if (($row_count!=null) && (strtoupper(substr($query,0,6))=='SELECT')) {
            $this->offset=(int)$offset;
            $this->rowCount=(int)$row_count;
            $query='SELECT SQL_CALC_FOUND_ROWS '.substr($query,7)." LIMIT ".$this->offset.",".$this->rowCount;
        }
        else {
            $this->offset=0;
            $this->rowCount=0;
        }
		global $connect;
		$this->lastQuery= $connect->execute($query,true);
		$result = $connect->getNumRows($this->lastQuery);
        $row_datas=array(0);
        if ($this->rowCount==0)
    		$this->N=$result;
        else {
            $query_id_found_rows= $connect->execute("SELECT FOUND_ROWS()",true);
            $row_datas=$connect->getRow($query_id_found_rows);
    		$this->N=(int)$row_datas[0];
        }
		$this->debug("Query:Q=$query - offset=".$this->offset.",row_count=".$this->rowCount." - nb=$result",2);
		return $result;
	}

	private $whereAddList=array();

	/**
	 * Ajoute une condition
	 * @param string $whereAdd condition
	 */
	public function whereAdd($whereAdd){
		$this->whereAddList[]=$whereAdd;
	}

	private $order=null;

	/**
	 * Defini l'ordre
	 * @param string $order ordre
	 */
	public function orderBy($order) {
		$this->order=$order;
	}

	/**
	 * Lance une recheche
	 *
	 * @param bool $withFct Avec fonctions stockees
     * @param int $offset offset de selection (limit)
     * @param int $row_count nb d'enregistrement de selection (limit)
	 * @return bool
	 */
	public function find($withFct=true, $offset=NULL, $row_count=NULL) {
		list($fields,$tables,$wheres)=$this->prepQuery(true,$withFct);
		$query="SELECT ".implode(',',$fields)." FROM ".implode(',',$tables);
		foreach($this->whereAddList as $other_where)
			$wheres[]=$other_where;
		if (count($wheres)>0)
			$query.=" WHERE ".implode(' AND ',$wheres);
		if (is_string($this->order))
			$query.=" ORDER BY ".$this->order;
		$result = $this->query($query, $offset, $row_count);
		return $result;
	}


	private $lastRow=null;
	
	/**
	 * Renvoie le dernier enregistrement trouve
	 *
	 * @return array
	 */
	public function getLastRow() {
		return $this->lastRow;
	}

	/**
	 * Selectionne l'enregistrement suivant
	 *
	 * @return bool
	 */
	public function fetch() {
		$this->__son = null;
		foreach($this->__DBMetaDataField as $col_name => $item)
			$this->$col_name = null;
		global $connect;
		$this->lastRow=$connect->getRowByName($this->lastQuery);
		if ($this->lastRow) {
			$this->__super = null;
			$result=$this->setFrom($this->lastRow);
			$this->debug("fetch:row=".print_r($this->lastRow,true)." \n result=$result ",5);
			return $result;
		}
		else
			return false;
	}

	/**
	 * supprime l'enregistrement
	 *
	 * @return bool
	 */
	public function delete() {
		if (((int)$this->id)==0) {
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Suppression impossible{[newline]}Enregistrement vide.");
		}
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
	 * @return int nouvel identifiant
	 */
	public function insert() {
		$this->__son = null;
		if($this->Heritage != "") {
			$this->superId = $this->Super->insert();
		}

		$field_names=array();
		$field_values=array();
		$fields = $this->table();
		unset($fields['id']);
		foreach($fields as $field_name => $field_item) {
			$type=-1;
			if (isset($this->__DBMetaDataField[$field_name]))
				$type=$this->__DBMetaDataField[$field_name]['type'];
			if(!is_null($this->$field_name)) {
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
				else if ($type==10) {
					$field_values[]='NULL';
					$field_names[]=$field_name;
				}
			}
			else {
				if ($type==10) {
					$field_values[]='NULL';
					$field_names[]=$field_name;
				}
			}
		}
		$q = "INSERT INTO ".$this->__table;
		$q .= " (".implode(' ,',$field_names).")";
		$q .= " VALUES (".implode(' ,',$field_values).")";
		global $connect;
		$result = $connect->execute($q);
		$this->debug("Insert:Q=$q -> $result /".$connect->errorMsg,1);
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
	 * @return bool
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
				if (isset($this->__DBMetaDataField[$field_name]))
					$type=$this->__DBMetaDataField[$field_name]['type'];
				else
					$type=2;
				$value = $this->$field_name;
				if ($field_item==DBOBJ_STR) {
					$value = str_replace("'","''",$value);
					$fied_eq_value="$field_name='$value'";
				}
				else if (($type!=10) || (((int)$value)!=0))
					$fied_eq_value="$field_name=$value";
				else
					$fied_eq_value="$field_name=NULL";
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
	 * @return DBObj_Abstract
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
