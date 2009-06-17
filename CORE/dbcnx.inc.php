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
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//

//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//


// connexion à la base de données
require_once "DB.php";

class DBCNX {
	var $dbh;
	var $res;
	var $resIndex;
	var $errorMsg;
	var $errorCode;
	var $connected;
	var $debugLevel;

	var $dsn;

	function DBCNX($dbgLev = 0) {
		$this->res = array();
		$this->resIndex = 0;
		$this->connected = false;
		$this->debugLevel = $dbgLev;
	}

	function printDebug($msg) {
		if($this->debugLevel>0) print $msg;
	}

	function connect ($dbcnf){
		$this->dsn = "mysql://".
			$dbcnf['dbuser'].":".
			$dbcnf['dbpass']."@".
			$dbcnf['dbhost']."/".
			$dbcnf['dbname'];

		$this->printDebug("DBCNX::connect : DSN = $this->dsn\n");

		$options = array(
			'debug'       => 2,
			'portability' => DB_PORTABILITY_ALL,
		);

		$this->dbh =& DB::connect($this->dsn, $options);

		if (DB::isError($this->dbh)) {
			$this->errorMsg = $this->dbh->getMessage();
			$this->errorCode = $this->dbh->getCode();
			return false;
		}
		$this->connected = true;
		return true;
	}

	function execute($query,$throw=false) {
		$this->printDebug("DBCNX::execute : $query\n");

		$this->errorMsg = "";
		$this->errorCode = "";

		if(!$this->connected) {
			$this->printDebug("DBCNX::execute : non connecté à une base de données\n");
			$this->errorMsg = "non connecté à une base de données";
			$this->errorCode = "NOTCONNECTED";
			if ($throw) $this->throwError();
			return false;
		}

		$r =& $this->dbh->query($query);
		if (DB::isError($r)) {
			$this->printDebug("DBCNX::execute : apres execution de la requette: ".$r->getMessage()."\n");
			$this->errorMsg = $r->getMessage()."[$query]";
			$this->errorCode = $r->getCode();
			if ($throw) $this->throwError();
			return false;
		}
		else {
			// on ne stock que les resultats de requettes SELECT
			if ((substr($query, 0, 6) == "SELECT") || (substr($query, 0, 4) == "SHOW")) {
				$this->printDebug("DBCNX::execute : requette SELECT avec ".$r->numRows()." resultats\n");
				$this->resIndex++;
				$this->res[$this->resIndex] = $r;
				return $this->resIndex;
			}
			else return true;
		}
	}

	function throwError() {
		if ($this->errorMsg!='') {
			require_once("CORE/Lucterios_Error.inc.php");
			throw new LucteriosException(GRAVE,$this->errorMsg);
		}
	}

	function begin() {
		if (!$this->execute("BEGIN"))
		{
			require_once "Lucterios_Error.inc.php";
			throw new LucteriosException(GRAVE,"BEGIN:".$this->errorMsg);
		}
	}
	function commit() {
		if (!$this->execute("COMMIT"))
		{
			require_once "Lucterios_Error.inc.php";
			throw new LucteriosException(GRAVE,"COMMIT:".$this->errorMsg);
		}
	}
	function rollback() {
		if (!$this->execute("ROLLBACK"))
		{
			require_once "Lucterios_Error.inc.php";
			throw new LucteriosException(GRAVE,"ROLLBACK:".$this->errorMsg);
		}
	}

	function getRow($queryId) {
		$row = array();
		if(is_string($queryId) || is_int($queryId)) {
			if(array_key_exists($queryId, $this->res)) {
				if($row =& $this->res[$queryId]->fetchRow()) {
					// on retourne le row, ce n'est peut-etre pas le dernier, on ne fait rien de plus
					return $row;
				}
				else {
					// on a atteint la fin des enregistrements, on enleve l'index du tableau de resultats
					$this->res[$queryId]->free();
					unset($this->res[$queryId]);
					return false;
				}
			}
		}
		return false;
	}

	function getNumRows($queryId) {
		if(is_string($queryId) || is_int($queryId)) {
			if(array_key_exists($queryId, $this->res)) return $this->res[$queryId]->numRows();
			else return false;
		}
		else return false;
	}

} // fin de la class DBCNX

$connect = new DBCNX();
$connect->connect($dbcnf);
//if(!$connect->connected) print $connect->errorMsg;
//@END@
?>
