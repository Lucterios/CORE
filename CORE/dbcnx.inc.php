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
// --- Last modification: Date 17 June 2009 1:21:25 By  ---

//@BEGIN@

class DBCNX {
	var $mysql;
	var $res;
	var $resIndex;
	var $errorMsg;
	var $errorCode;
	var $connected;
	var $debugLevel;

	var $dsn;

	public function createDataBase($dbcnf) {
		$mysql = new mysqli($dbcnf['dbhost'], $dbcnf['dbuser'], $dbcnf['dbpass']);
		$last_error=$mysql->connect_errno;
		if ($last_error) {
			return $mysql->connect_error;
		}
		$mysql->query('CREATE DATABASE '.$dbcnf['dbname'].";");
		$last_error=$mysql->errno;
		if ($last_error) {
			return $mysql->connect_error;
		}
		return true;
	}

	public function __construct() {
		$this->res = array();
		$this->resIndex = 0;
		$this->connected = false;
		global $debugLevel;
		if (isset($debugLevel))
			$this->debugLevel = $debugLevel;
		else
			$this->debugLevel = 0;
	}

 	public function __destruct() 
	{
		if ($this->mysql!=null)
			$this->mysql->close();
     		session_write_close();
 	}

	function printDebug($msg) {
		if (($this->debugLevel==-1) || ($this->debugLevel>10)) {
			require_once('CORE/log.inc.php');
			__log($msg,"DBCNX");
		}
	}

	public function connect ($dbcnf){
		$this->dsn = "mysql://".
			$dbcnf['dbuser'].":".
			$dbcnf['dbpass']."@".
			$dbcnf['dbhost']."/".
			$dbcnf['dbname'];

		$this->printDebug("DBCNX::connect : DSN = $this->dsn\n");

		$this->mysql = new mysqli($dbcnf['dbhost'], $dbcnf['dbuser'], $dbcnf['dbpass']);
		$last_error=$this->mysql->connect_errno;
		if ($last_error) {
			$this->errorMsg = $this->mysql->connect_error;
			$this->errorCode = $last_error;
			return false;
		}
		$this->mysql->query("use ".$dbcnf['dbname'].";");
		$last_error=$this->mysql->errno;
		if ($last_error) {
			$this->errorMsg = $this->mysql->error;
			$this->errorCode = $last_error;
			return false;
		}

		$this->connected = true;
		$this->mysql->autocommit(TRUE);
		return true;
	}

	public function execute($query,$throw=false) {
		$this->printDebug("DBCNX::execute : $query\n");

		$this->errorMsg = "";
		$this->errorCode = false;

		if(!$this->connected || $this->connect_errno) {
			$this->printDebug("DBCNX::execute : non connecté à une base de données\n");
			$this->errorMsg = "non connecté à une base de données (".$this->connect_error.")";
			$this->errorCode = "NOTCONNECTED";
			if ($throw) $this->throwError();
			return false;
		}

		$result = $this->mysql->query($query);
		if (!$result) {
			$this->printDebug("DBCNX::execute : apres execution de la requette: ".$this->mysql->error."\n");
			$this->errorMsg = $this->mysql->error."[$query]";
			$this->errorCode = $this->mysql->errno;
			if ($throw) 
				$this->throwError();
			return false;
		}
		else {
			// on ne stock que les resultats de requettes SELECT
			if ((substr($query, 0, 6) == "SELECT") || (substr($query, 0, 4) == "SHOW")) {
				$this->printDebug("DBCNX::execute : requette SELECT avec ".$result->num_rows." resultats\n");
				$this->resIndex++;
				$this->res[$this->resIndex] = $result;
				return $this->resIndex;
			}
			else if (substr($query, 0, 11) == "INSERT INTO") {
				$ret=$this->mysql->insert_id;
				$this->printDebug("DBCNX::execute : requette INSERT => ID=$ret\n");
				return $ret;
			}
			else {				
				$ret=$this->mysql->affected_rows;
				$this->printDebug("DBCNX::execute : nombre enregistrement modifiés=$ret\n");
				return true;
			}
		}
	}

	public function isFailed(){
		if ($this->mysql->errno)
			return true;
		else
			return false;
	}

	public function throwExcept($MsgPred='') {
		if ($this->mysql->errno) {
			require_once("CORE/Lucterios_Error.inc.php");
			throw new LucteriosException(GRAVE,$MsgPred.$this->mysql->error);
		}
	}

	public function throwError() {
		if ($this->errorMsg!='') {
			require_once("CORE/Lucterios_Error.inc.php");
			throw new LucteriosException(GRAVE,$this->errorMsg);
		}
	}

	public function begin() {
		$this->mysql->autocommit(FALSE);
		$this->throwExcept('Begin:');
	}
	public function commit() {
		$this->mysql->commit();
		$this->throwExcept('Commit:');
		$this->mysql->autocommit(TRUE);
	}
	public function rollback() {
		$this->mysql->rollback();
		$this->throwExcept('Rollback:');
		$this->mysql->autocommit(TRUE);
	}

	public function getRecord($queryId) {
		$row = array();
		if(is_string($queryId) || is_int($queryId)) {
			if(array_key_exists($queryId, $this->res)) {
				return $this->res[$queryId];
			}
		}
		return false;
	}

	public function getRow($queryId) {
		$req=$this->getRecord($queryId);
		if ($req) {
			if($row =$req->fetch_row()) {
				// on retourne le row, ce n'est peut-etre pas le dernier, on ne fait rien de plus
				return $row;
			}
			else {
				// on a atteint la fin des enregistrements, on enleve l'index du tableau de resultats
				$req->free();
				unset($this->res[$queryId]);
				return false;
			}
		}
		return false;
	}

	public function getRowByName($queryId) {
		$req=$this->getRecord($queryId);
		if ($req) {
			if($row =$req->fetch_row()) {
				$fields=$req->fetch_fields();
				$new_row=array();
				foreach($row as $index=>$value){
					$finfo = $fields[$index];
					$table_name="";
					if (!empty($finfo->orgtable))
						$table_name=$finfo->orgtable.'.';
					else if (!empty($finfo->table))
						$table_name=$finfo->table.'.';
					/*if (!empty($finfo->orgname))
						$field_name=$finfo->orgname;
					else */
						$field_name=$finfo->name;
					$new_row[$table_name.$field_name]=$value;
				}
				return $new_row;
			}
			else {
				// on a atteint la fin des enregistrements, on enleve l'index du tableau de resultats
				$req->free();
				unset($this->res[$queryId]);
				return false;
			}
		}
		return false;
	}

	public function getNumRows($queryId) {
		if(is_string($queryId) || is_int($queryId)) {
			if(array_key_exists($queryId, $this->res)) 
				return $this->res[$queryId]->num_rows;
			else 
				return false;
		}
		else return false;
	}

} // fin de la class DBCNX

$connect = new DBCNX();
$connect->connect($dbcnf);
//if(!$connect->connected) print $connect->errorMsg;
//@END@
?>
