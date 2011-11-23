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
// --- Last modification: Date 22 November 2011 21:57:55 By  ---

//@BEGIN@
/**
 * fichier gérant le DBObject
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage DBObject
 */

require_once("CORE/DBAbstract.inc.php");

/**
* SEP_SEARCH
* @access private
*/
define('SEP_SEARCH','%');

/**
* SEP_SHOW
* @access private
*/
define('SEP_SHOW','#|#');

/**
* Classe mère au DBObject Luctérios
*
* Classe principale de manipulation des tables utilisées par Luctérios. Repose sur PEAR/DB_DataObject
*
* Cette classe permet différentes manipulations des tables:
* 1. Déscription, création et mise à jour des tables de l'application
* 2. Abstraction d'un enregistrement sous forme d'un objet : insertion, selection, modification, suppression.
* 3. Recherche simple et complexe d'enregistrement.
* 4. Association de traitements (actions, methdes) à une table.
* @package Lucterios
* @subpackage DBObject
* @author Pierre-Oliver Vershoore/Laurent Gay
*/
class DBObj_Basic extends DBObj_Abstract {

	/**
	 * Controle et modifie la structure d'une table DB
	 *
	 * @return array(boolean,string) tableau:succes + retour console
	 */
	public function setup($throwExcept = false) {
		require_once'DBSetup.inc.php';
		$install = new DBObj_Setup($this);
		$install->throwExcept=$throwExcept;
		$success = $install->execute();
		$result = $install->RetMsg;
		return array($success,$result);
	}

	/**
	 * Verifier le vérouillage
	 *
	 * @return void
	 */
	private function checkLockRecord() {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				return $son->checkLockRecord();
			}
		}
		if($this->lockRecord != "") {
			list($lock_session,$lock_origine) = explode('@',$this->lockRecord);
			global $connect;
			$res = $connect->execute("SELECT sid FROM CORE_sessions WHERE valid='o' AND sid='$lock_session'");
			list($sid) = $connect->getRow($res);
			if($sid != $lock_session) {
				$this->lockRecord = "";
				$this->update();
			}
		}
		if($this->Heritage != "")
			$this->Super->checkLockRecord();
	}

	/**
	 * Retourne l'état de vérouillage
	 *
	 * @return int
	 */
	public function setLockRecord() {
		if($this->id<=0)
			return LOCKRECORD_THIS;
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				return $son->setLockRecord();
			}
		}
		$this->checkLockRecord();
		global $GLOBAL;
		$session = $GLOBAL["ses"];
		if (strpos($session,'@')!==false) $session="";
		list($lock_session,$lock_origine) = explode('@',$this->lockRecord);
		if($lock_session == $session)
			return LOCKRECORD_THIS;
		else {
			if($this->lockRecord == "") {
				if($this->Heritage != "")
					return $this->Super->setLockRecord();
				else
					return LOCKRECORD_NO;
			}
			else
			return LOCKRECORD_OTHER;
		}
	}

	/**
	 * Verouille l'enregistrement
	 *
	 * @return void
	 */
	public function lockRecord($origine) {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				return $son->lockRecord($origine);
			}
		}
		$lock = $this->setLockRecord();
		if($lock == LOCKRECORD_OTHER) {
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Données vérouillées par un autre utilisateur.{[newline]}Veuillez réessayer ulterieurement.");
		}
		else if($lock == LOCKRECORD_NO) {
			global $GLOBAL;
			$session = $GLOBAL["ses"];
			if (strpos($session,'@')!==false) $session="";
			$this->lockRecord = "$session@$origine";
			if($this->Heritage != "")
				$this->Super->lockRecord($origine);
			$this->update();
		}
		return true;
	}

	/**
	 * Déverouille l'enregistrement
	 *
	 * @return void
	 */
	public function unlockRecord($origine) {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				return $son->unlockRecord($origine);
			}
		}
		if(($this->id<=0) || ($this->lockRecord == ''))
			return true;
		$this->checkLockRecord();
		global $GLOBAL;
		$session = $GLOBAL["ses"];
		if (strpos($session,'@')!==false) $session="";
		list($lock_session,$lock_origine) = explode('@',$this->lockRecord);
		if($lock_session == $session) {
			if($lock_origine = $origine) {
				$this->lockRecord = "";
				if($this->Heritage != "")
					$this->Super->unlockRecord($origine);
				global $connect;
				$q="UPDATE ".$this->__table." SET lockRecord='' WHERE id=".$this->id;
				$result = $connect->execute($q);
				if ($result === false) {
					__log($q,'Query unlock failure:'.$connect->errorMsg);
				}
			}
		}
		else {
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( GRAVE,"Déverrouillage impossible.$lock_session=$session");
		}
		return true;
	}

	/**
	 * test si l'on peut supprimer cette enregistrement (cf deleteCascade)
	 * @return int (0: Oui ; 1: reference existe ; 2: blocage via methode canDelete()
	 */
	public function canBeDelete() {
		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		try {
			if (!$this->canDelete())
				return 2;
		} catch(Exception $e){}

		global $connect;
		$res=0;
		require_once('CORE/extensionManager.inc.php');
		$class_list = getReferenceTablesList($this->__table,$rootPath);
		foreach($class_list as $table=>$fieldname) {
			$search=true;
			foreach($this->__DBMetaDataField as $values)
				if ($values['type']==9) {
					$params=$values['params'];
					if (($params['TableName']==$table) && ($params['RefField']==$fieldname))
						$search=false;
				}
			if ($search) {
				$file_class_name = DBObj_Abstract::getTableName($table);
				$class_name = 'DBObj_'. $table;
				require_once($file_class_name);
				$DBObj=new $class_name;
				$sub_param=$DBObj->__DBMetaDataField[$fieldname];
				$search=$sub_param['notnull'];
			}
			if ($search) {
				try {
					$q="SELECT id FROM $table WHERE $fieldname=$this->id";
					$row_id=$connect->execute($q,true);
					if ($connect->getRow($row_id)!=false)
						$res=1;
				}
				catch (LucteriosException $e) {
				}
			}
		}
		if(($res==0) && !$this->is_super) {
			$son = $this->getSon();
			if($son != null)
				$res=$son->canBeDelete();
		}
		if (($res==0) && ($this->Heritage != ""))
			$res=$this->Super->canBeDelete();
		return $res;
	}

	/**
	 * supprime l'enregistrement et ses enfants en cascade
	 *
	 */
	public function deleteCascade() {
		if (((int)$this->id)==0) {
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( IMPORTANT,"Suppression impossible{[newline]}Enregistrement vide.");
		}
		require_once"Lucterios_Error.inc.php";
		$res=$this->canBeDelete();
		if ($res==1)
			throw new LucteriosException(GRAVE,"Suppression impossible: Des references existent");
		if ($res==2)
			throw new LucteriosException(GRAVE,"Suppression impossible: Enregistrement protege");
		global $connect;
		$connect->begin();
		try {
			if(!$this->is_super) {
				$son = $this->getSon();
				if($son != null) {
					return $son->deleteCascade();
				}
			}
			foreach($this->__DBMetaDataField as $fieldname=>$values) {
				if ($values['type']==9) {
					$children=$this->getField($fieldname);
					while ($children->fetch())
						$children->deleteCascade();
				}
			}
			if($this->Heritage != "")
				$this->Super->deleteCascade();
			$this->delete();
			$connect->commit();
		} catch(Exception $e) {
			$connect->rollback();
			throw $e;
		}
	}

	/**
	 * Remplace les references de l'objet externe.
	 *
	 * @param DBObj_Basic $DBObject
	 */
	private function __replaceReference($DBObject) {
		global $connect;
		require_once('CORE/extensionManager.inc.php');
		$class_list = getReferenceTablesList($this->__table);
		foreach($class_list as $table=>$fieldname) {
			$q="UPDATE $table SET $fieldname=$this->id WHERE $fieldname=$DBObject->id";
			$connect->execute($q,true);
		}
		if($this->Heritage != "")
			$this->Super->__replaceReference($DBObject->Super);
	}

	/**
	 * Fusionne un autre objet de même nature (fille ou mere)
	 * Les reference seront remplacé et l'ancien objet supprimer.
	 *
	 * @param DBObj_Basic $DBObject
	 */
	public function merge($DBObject) {
		require_once"Lucterios_Error.inc.php";
		if (!is_object($DBObject))
			throw new LucteriosException(GRAVE,"Fusion impossible: Objet non un DBObject");
		global $connect;
		$connect->begin();
		try {
			if ($this->__table==$DBObject->__table) {
				$this_son = null;
				if(!$this->is_super)
					$this_son = $this->getSon();
				$DBObject_son = null;
				if(!$DBObject->is_super)
					$DBObject_son = $DBObject->getSon();

				if (($this_son == null) && ($DBObject_son == null)) {
					if ($this->id==$DBObject->id)
						throw new LucteriosException(GRAVE,"Fusion impossible: Objet identique");
					$this->__replaceReference($DBObject);
					$DBObject->delete();
				}
				else if ($this_son == null)
					$this->merge($DBObject_son);
				else if ($DBObject_son == null)
					$this_son->merge($DBObject);
				else
					$this_son->merge($DBObject_son);
			}
			else {
				$sup_obj=$DBObject->getSuperObject($this->__table);
				if ($sup_obj!=null) {
					$this->__replaceReference($sup_obj);
					$q="UPDATE $DBObject->__table SET superId=$this->id WHERE id=$DBObject->id";
					$connect->execute($q,true);
					$sup_obj->delete();
				}
				else {
					$sup_obj=$this->getSuperObject($DBObject->__table);
					if ($sup_obj!=null) {
						$sup_obj->__replaceReference($DBObject);
						$DBObject->delete();
					}
					else
						throw new LucteriosException(GRAVE,"Fusion impossible: Objet incompatible ".get_class($this).' !! '.get_class($DBObject));
				}
			}
			$connect->commit();
		} catch(Exception $e) {
			$connect->rollback();
			throw $e;
		}
	}

	/**
	 * retourne la valeur d'un champs
	 *
	 * Si le champ est persistant, retourne la valeur DB
	 * Si le champ est une référence, retourne un object DB associé Ã  l'enregistrement
	 * Si le champ est un fils, retourne un object DB commencant un liste d'enregistrements associÃ©s
	 * @param string $fieldname
	 * @param string $filter
	 * @param string $order
	 * @return DBObj_Basic|integer|string|boolean|real
	 */
	public function getField($fieldname,$filter = "",$order = "") {
		if( array_key_exists($fieldname,$this->__DBMetaDataField)) {
			$item = $this->__DBMetaDataField[$fieldname];
			$field_val = $this->$fieldname;
			if($item['type'] == 3) {
				return ($field_val != 'n');
			}
			elseif (($item['type'] == 9) || ($item['type'] == 10)) {
				if(array_key_exists('TableName',$item['params'])) {
					$tbl_select = $item['params']['TableName'];
					$table_file_name = $this->getTableName($tbl_select);
					if( is_file($table_file_name)) {
						require_once($table_file_name);
						$class_name = "DBObj_".$tbl_select;
						$sub_object = new $class_name;
						if($item['type'] == 10) {
							if($field_val>0)
								$sub_object->get($field_val);
						}
						else if ($this->id>0) {
							$child_field_name = $item['params']['RefField'];
							$sub_object->$child_field_name = $this->id;
							if($filter != "")
								$sub_object->whereadd($filter);
							if($order != "")
								$sub_object->orderby($order);
							else
								$sub_object->orderby('id');
							$sub_object->find();
						}
					}
					else
						$sub_object = "???";
					return $sub_object;
				}
				else {
					return $field_val;
				}
			}
			elseif ($item['type'] == 8) {
				if(! array_key_exists('Enum',$item['params'])) {
					return "---";
				}
				elseif (! array_key_exists($field_val,$item['params']['Enum'])) {
					return "???";
				}
				else {
					return $item['params']['Enum'][$field_val];
				}
			}
			elseif (($item['type'] == 12) || ($item['type'] == 13)) {
				$params=$item['params'];
				return $this->Call($params['MethodGet']);
			}
			else
				return $field_val;
		}
		else if(($this->Super != null) && array_key_exists($fieldname,$this->Super->getDBMetaDataField()))
			return $this->Super->getField($fieldname,$filter,$order);
		else {
			$pos = strpos($fieldname, SEP_SHOW);
			if($pos === false)
				$field_name = $fieldname;
			else
				$field_name = substr($fieldname,$pos+3);
			return $this->evalByText($field_name);
		}
	}

	/**
	 * getMethodFileName
	 *
	 * @param string $method
	 * @return string|False
	 */
	public function getMethodFileName($method) {
		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		$fct_name = $this->tblname."_APAS_$method";
		if($this->extname == "CORE")
			$fct_file_name = $rootPath.$this->extname."/$fct_name.mth.php";
		else
			$fct_file_name = $rootPath."extensions/".$this->extname."/$fct_name.mth.php";
		return array($fct_file_name,$fct_name);
	}

	/**
	 * call Method
	 *
	 * @param string $fctFileName
	 * @param string $fctName
	 * @param Array $params
	 * @return unknown
	 */
	public function callMethod($fct_file_name,$fct_name,&$params) {
		if( is_file($fct_file_name)) {
			require_once($fct_file_name);
			if( function_exists($fct_name)) {
				if (function_exists('call_user_func_array')) {
					$param_arr=array(&$this);
					foreach($params as $val)
						$param_arr[]=$val;
					return call_user_func_array($fct_name,$param_arr);
				}
				else {
					$param_arr = "\$this";
					foreach($params as $val) {
						if( is_string($val)) {
							$param_arr .= ", '$val'";
						}
						elseif ( is_bool($val)) {
							if($val)$param_arr .= ", true";
							else $param_arr .= ", false";
						}
						elseif (( is_object($val)) || ( is_array($val))) {
							if(!isset($obj1)) {
								$obj1 = &$val;
								$param_arr .= ", \$obj1";
							}
							elseif (!isset($obj2)) {
								$obj2 = &$val;
								$param_arr .= ", \$obj2";
							}
							elseif (!isset($obj3)) {
								$obj3 = &$val;
								$param_arr .= ", \$obj3";
							}
						}
						elseif ( is_null($val))
							$param_arr .= ", null";
						else
							$param_arr .= ",$val";
					}
					$cmd = "return ".$fct_name."($param_arr);";
					return eval($cmd);
				}
			}
			else {
				require_once"Lucterios_Error.inc.php";
				throw new LucteriosException( CRITIC,"Function ".$fct_name." not found!");
			}
		}
		else {
			require_once"Lucterios_Error.inc.php";
			throw new LucteriosException( CRITIC,"File ".$fct_file_name." not found!");
		}
	}

	/**
	 * Surcharge de méthodes
	 *
	 * @param string $method
	 * @param Array $params
	 * @return unknown
	 */
	public function __call($method,$params) {
		if(!$this->is_super) {
			$son = $this->getSon();
			if($son != null) {
				list($fct_file_name,$fct_name) = $son->getMethodFileName($method);
				if( is_file($fct_file_name)) {
					return $son-> __call($method,$params);
				}
			}
		}
		list($fct_file_name,$fct_name) = $this->getMethodFileName($method);
		if(! is_file($fct_file_name) && ($this->Super != null))
			return $this->Super-> __call($method,$params);
		return $this->callMethod($fct_file_name,$fct_name,$params);
	}

	/**
	 * Appele une methode lucterios associée a cette classe/table
	 *
	 * @param string $MethodName
	 * @return unknown
	 */
	public function Call($MethodName) {
		$params = array();
		for($i = 1;$i< func_num_args();$i++)
			$params[] = func_get_arg($i);
		return $this-> __call($MethodName,$params);
	}

	/**
	 * Creer une action associée à cette classe/table
	 *
	 * @param string $title titre du bouton
	 * @param string $icon nom de l'icone
	 * @param string $action nom de l'action
	 * @param integer $modal FORMTYPE_MODAL=appel une fenêtre modal - FORMTYPE_NOMODAL=non modal - FORMTYPE_REFRESH=réutilise la fiche appelante
	 * @param integer $close CLOSE_YES=ferme la fenêtre appelante - CLOSE_NO=fenêtre reste en fond
	 * @param integer $select SELECT_NONE=action à l'ensemble d'une grille - SELECT_SINGLE=action associée à une sélection dans une grille - SELECT_MULTI=action associée à une ou plusieurs sélections dans une grille
	 * @return Xfer_Action
	 */
	public function NewAction($title,$icon = "",$action = "",$modal = 1,$close = 1,$select = "") {
		require_once'xfer.inc.php';
		if($action == "")
			return new Xfer_Action($title,$icon,"","",$modal,$close,$select);
		else
			return new Xfer_Action($title,$icon,$this->extname,$this->tblname."_APAS_$action",$modal,$close,$select);
	}

	/**
	 * Rempli un retour d'impression
	 *
	 * @param Xfer_Container_Print &$XferPrint
	 * @param string $Name Nom du model d'impression
	 * @param string $Title Titre de l'impression
	 * @param integer $writeMode Mode de surcharge des sauvegarde
	 * @param integer $printRef reference DB pour la sauvegarde - 0=pas de sauvegarde
	 */
	public function PrintReport(&$XferPrint,$Name,$Title,$writeMode = WRITE_MODE_NONE,$printRef = 0) {
		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		$print_name = $this->tblname."_APAS_$Name";
		if($this->extname == "CORE")
			$print_file_name = $rootPath.$this->extname."/$print_name.prt.php";
		else
			$print_file_name = $rootPath."extensions/".$this->extname."/$print_name.prt.php";
		if( is_file($print_file_name))
			$XferPrint->selectReport($print_name,$XferPrint->m_context,$Title,$writeMode,$printRef);
		else
			return trigger_error("File $print_file_name not found!");
	}

	/**
	 * Lance une recherche d'enregistrement
	 *
	 * Permet de rechercher des enregistrements.
	 * Pour chaque champs intervenant dans la requetes, 2 clefs suffixés par _select et _value1 doivent être référencées dans $Params
	 * _select: référence l'operateur de comparaison
	 * _value1: valeur à comparer
	 * @param array $Params
	 * @param string $OrderBy
	 */
	public function setForSearch($Params,$OrderBy = '',$searchQuery = "",$searchTable=array(),$extraFields=array()) {
		$query="";
		if (isset($Params['CRITERIA'])) {
			include_once("CORE/DBFind.inc.php");
			$newFind= new DBFind($this);
			$query=$newFind->Execute($Params,$OrderBy,$searchQuery,$searchTable,$extraFields);
		}
		logAutre("FIND QUERY=$query");
		if($query != "")
			$this->query($query);
		return $query;
	}

	/**
	 * Construit des part de requettes pour lier les tables héritées.
	 *
	 */
	public function getSubSearchWithHeritage() {
		$search_tbl=array($this->__table);
		$search_query = "";
		$current_obj=$this;
		while($current_obj!=null) {
			$super_class=$current_obj->Super;
			if ($super_class!=null) {
				$super_obj=new $super_class();
				$search_tbl[]=$super_obj->__table;
				if ($search_query!='') $search_query.=" AND ";
				$search_query.=$current_obj->__table.".superId=".$super_obj->__table.".id";
				$current_obj=$super_obj;
			}
			else
				$current_obj=null;
		}
		return array($search_query,$search_tbl);
	}
}
//@END@
?>
