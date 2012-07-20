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
class SecurityLock {

	/**
	 * Session vérouillant l'application
	 *
	 * @var string
	 */
	private $m_lockSession = "";

	/**
	 * Date de début de vérroux (format Y-m-d h:i)
	 *
	 * @var string
	 */
	private $m_lockDate = null;


	/**
	 * Constructeur
	 *
	 * @return SecurityLock
	 */
	public function __construct() {
		$this->readLockFile();
		$this->checkLockFile();
	}

	private function getLockFileName() {
		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		return $rootPath."conf/security.lock";
	}

	private function readLockFile() {
		$fileName=$this->getLockFileName();
		if (is_file($fileName)) {
		    $content=file($fileName);
		    $this->m_lockSession = trim($content[0]);
		    $this->m_lockDate = trim($content[1]);
		}
		else {
		    $this->m_lockSession = "";
		    $this->m_lockDate = null;
		}
	}

	private function writeLockFile() {
		$ret=false;
		$fileName=$this->getLockFileName();
		if (is_file($fileName) && ($this->m_lockSession=='')) {
			unlink($fileName);
			$ret=is_file($fileName);
		}
		else if ($this->m_lockSession!='') {
			$this->m_lockDate=date('Y-m-d h:i');
			$fp = fopen($fileName, 'w');
			if ($fp) {
				fwrite($fp, $this->m_lockSession."\n");
				fwrite($fp, $this->m_lockDate."\n");
				fclose($fp);
				$ret=is_file($fileName);
			}
		}
		return $ret;
	}

	private function checkSessionExisting() {
		global $connect;
		$rep = $connect->execute("SHOW TABLE STATUS LIKE 'CORE_sessions'");
		return ($rep && ($connect->getNumRows($rep) == 1));
	}

	private function checkLockFile() {
		if (($this->m_lockSession!='') && (strpos($this->m_lockSession,'@')===false) && $this->checkSessionExisting()) {
			include_once('CORE/sessions.tbl.php');
			$DBSession=new DBObj_CORE_sessions;
			$DBSession->query("SELECT * FROM CORE_sessions WHERE sid='".$this->m_lockSession."' AND valid='o'");
			if (!$DBSession->fetch()) {
				$this->m_lockSession='';
			}
		}
		if ($this->m_lockSession!='') {
			$limitdate=date("Y-m-d H:i",strtotime(date("Y-m-d H:i") . " -15 minutes"));
			if ($this->m_lockDate<$limitdate)
				$this->m_lockSession='';
		}
		if ($this->m_lockSession=='')
			$this->writeLockFile();
	}

	public function isLock() {
		$res=0;
		if ($this->m_lockSession!='') {
			global $GLOBAL;
			if ($GLOBAL["ses"]==$this->m_lockSession)
				$res=1;
			else
				$res=-1;
		}
		return $res;
	}

	public function open($throwException=false) {
		$msg="";
		$lock=$this->isLock();
		if ($lock!=-1) {
			$nb=0;
			if (($lock==0) && $this->checkSessionExisting()) {
				global $GLOBAL;
				include_once('CORE/sessions.tbl.php');
				$DBSession=new DBObj_CORE_sessions;
				$DBSession->query("SELECT * FROM CORE_sessions WHERE valid='o'");
				while ($DBSession->fetch()) {
					  if ($DBSession->sid!=$GLOBAL["ses"])
						$nb++;
				}
			}
			if ($nb==1) {
				$msg="Verrouillage de sécurité impossible: une autre connexion est active!";
			}
			else if ($nb>1) {
				$msg="Verrouillage de sécurité impossible: $nb autres connexions sont active!";
			}
			else {
				global $GLOBAL;
				$this->m_lockSession=$GLOBAL["ses"];
				if (!$this->writeLockFile())
					$msg="Le verrouillage a échoué!";
			}
		}
		else
			$msg="Un verrouillage de sécurité est déjà réalisé par une autre connexion!";
		$ret=($msg=="");
		if ($throwException && !$ret) {
			echo "<!-- [".date('d/m/Y h:i:s')."] ****** VEROUX SECURITE : $msg ****** -->\n";
			require_once "CORE/Lucterios_Error.inc.php";
			throw new LucteriosException(IMPORTANT,$msg);
		}
		return 	array($ret,$msg);
	}

	public function close() {
		if ($this->isLock()==1) {
			$this->m_lockSession="";
			return !$this->writeLockFile();
		}
		else
			return false;
	}

}
//@END@
?>
