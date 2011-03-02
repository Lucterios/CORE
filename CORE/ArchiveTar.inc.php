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
// --- Last modification: Date 01 March 2011 22:54:21 By  ---

//@BEGIN@
class ArchiveTar {

	var $pear_object=null;

	var $phar_object=null;

	function __construct($tarFile,$isCompress=false) {
		if (class_exists('PharData')) {
			$this->phar_object = new PharData($tarFile);
			if ($isCompress)
				$this->phar_object->compress(Phar::GZ);
			else
				$this->phar_object->compress(Phar::NONE);
		}
		else {
			require_once("Archive/Tar.php");
			require_once("PEAR.php");
			if (class_exists('Archive_Tar')) {
				$this->pear_object = new Archive_Tar($tarFile,$isCompress?'gz':'');
				PEAR::setErrorHandling(PEAR_ERROR_EXCEPTION);
			}
			else
				$this->_throwError();
		}
	}

	function __destruct() {
		if (!is_null($this->pear_object)) {
			PEAR::setErrorHandling(PEAR_ERROR_RETURN);
			$this->pear_object=null;
		}
    	}

    	function extractInString($fileName){
		if (!is_null($this->phar_object)) {
			return $this->phar_object[$fileName]->getContent();;
		}
		if (!is_null($this->pear_object)) {
	    		return $this->pear_object->extractInString($fileName);
		}
		$this->_throwError();
    	}

    	function extract($targetDir) {
		if (!is_null($this->phar_object)) {
			return $this->phar_object->extractTo($targetDir);
		}
		if (!is_null($this->pear_object)) {
	    		$res=$this->pear_object->extract($targetDir);
	    		return !PEAR::isError($res);
		}
		$this->_throwError();
    	}

    	function extractModify($targetDir,$sourceDir){
		if (!is_null($this->phar_object)) {
			return $this->phar_object->extractTo($targetDir,$sourceDir);
		}
		if (!is_null($this->pear_object)) {
	    		$res=$this->pear_object->extractModify($targetDir,$sourceDir);
	    		return !PEAR::isError($res);
		}
		$this->_throwError();
    	}

    	function extractList($listToExtract,$ignorePath="",$addPath="") {
		if (!is_null($this->phar_object)) {
		}
		if (!is_null($this->pear_object)) {
	    		$res=$this->pear_object->extractList($listToExtract,$ignorePath,$addPath);
	    		return !PEAR::isError($res);
		}
		$this->_throwError();
    	}

    	function addString($fileName,$content) {
		if (!is_null($this->phar_object)) {
			return $this->phar_object->addFromString($fileName,$content);
		}
		if (!is_null($this->pear_object)) {
	    		$res=$this->pear_object->addString($fileName,$content);
	    		return !PEAR::isError($res);
		}
		$this->_throwError();
    	}

    	function add($listToAdd) {
		if (!is_null($this->phar_object)) {
			if (is_string($listToAdd))
				$listToAdd=array($listToAdd);
			foreach($listToAdd as $file) {
				if (is_file($file)) {
					//logAutre("Add file = $file");
					$this->phar_object->addFile($file);
				}
				else {
					//logAutre("Add dir = $file");
					$sub_files=$this->_getFiles($file);
					$this->phar_object->buildFromIterator(new ArrayIterator($sub_files));
				}
			}
			return true;
		}
		if (!is_null($this->pear_object)) {
			$res=$this->pear_object->add($listToAdd);
			return !PEAR::isError($res);
		}
		$this->_throwError();
	}

    	function addModify($listToAdd,$removePath) {
		if (!is_null($this->phar_object)) {
			if (is_string($listToAdd))
				$listToAdd=array($listToAdd);
			foreach($listToAdd as $file) {
				if (is_file($file)) {
					$localname=substr($file,strlen($removePath));
					//logAutre("Add file = $file // $localname");
					$this->phar_object->addFile($file,$localname);
				}
				else {
					//logAutre("Add dir = $file // $removePath");
					$sub_files=$this->_getFiles($file,$removePath);
					$this->phar_object->buildFromIterator(new ArrayIterator($sub_files));
				}
			}
			return true;
		}
		if (!is_null($this->pear_object)) {
			$res=$this->pear_object->addModify($listToAdd,"",$removePath);
			return !PEAR::isError($res);
		}
		$this->_throwError();
	}

	function _getFiles($dir,$removePath="") {
		$ret=array();
		if (substr($dir,-1)!='/')
			$dir.='/';
		if(is_dir($dir)) {
			$dh = opendir($dir);
			while(($file = readdir($dh)) != false) {
				if (is_file($dir.$file))
					$ret[$dir.$file] = substr($dir.$file,strlen($removePath));
				else if(is_dir($dir.$file) && ($file[0] != '.')) {
					$sub=$this->_getFiles($dir.$file);
					foreach($sub as $item)
					  $ret[$item] = substr($item,strlen($removePath));
				}
			}
			closedir($dh);
		}
		return $ret;
	}

	function _throwError() {
		require_once("CORE/Lucterios_Error.inc.php");
		throw new LucteriosException(CRITIC,'Mauvais droit');
	}

}
//@END@
?>
