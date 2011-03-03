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
// --- Last modification: Date 03 March 2011 13:28:53 By  ---

//@BEGIN@
class ArchiveTar {

	var $pear_object=null;

	var $phar_object=null;

	function __construct($tarFile,$isCompress=false) {
		if (class_exists('PharData')) {
			if (!is_file($tarFile) && ($isCompress || ($isCompress=='gz'))) {
				$pos=strrpos($tarFile,'.');
				$ext=substr($tarFile,$pos);
				$tmp_tarFile=substr($tarFile,0,$pos).".tar";
				$temp_phar_object = new PharData($tmp_tarFile);
				$this->phar_object=$temp_phar_object->compress(Phar::GZ,$ext);
			}
			else
				$this->phar_object = new PharData($tarFile);
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
			return $this->phar_object[$fileName]->getContent();
		}
		if (!is_null($this->pear_object)) {
	    		return $this->pear_object->extractInString($fileName);
		}
		$this->_throwError();
    	}

    	function extract($targetDir) {
		if (!is_null($this->phar_object)) {
			if (substr($targetDir,-1)=='/')
				$targetDir=substr($targetDir,0,-1);
			return $this->phar_object->extractTo($targetDir,null,true);
		}
		if (!is_null($this->pear_object)) {
	    		$res=$this->pear_object->extract($targetDir);
	    		return !PEAR::isError($res);
		}
		$this->_throwError();
    	}

    	function extractList($listToExtract,$targetDir = '',$removePath='') {
		if (!is_null($this->phar_object)) {
			if (is_string($listToExtract))
				$listToExtract=array($listToExtract);
			foreach($listToExtract as $file) {
				$localname=substr($file,strlen($removePath));
				$localname=$targetDir.$localname;
				if ($localname[0]=='/')
					$localname=substr($localname,1);
				$content=$this->phar_object[$file]->getContent();
				$fh = fopen($localname, 'w');
				fwrite($fh, $content);
				fclose($fh);
			}
			return true;
		}
		if (!is_null($this->pear_object)) {
	    		$res=$this->pear_object->extractList($listToExtract,$targetDir,$removePath);
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
				$sub_files=$this->_getFiles($file);
				//echo "<!-- Add = $file // ".print_r($sub_files,true)." -->\n";
				$this->phar_object->buildFromIterator(new ArrayIterator($sub_files));
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
				$sub_files=$this->_getFiles($file,$removePath);
				//echo "<!-- AddModify = $file // $removePath //".print_r($sub_files,true)." -->\n";
				$this->phar_object->buildFromIterator(new ArrayIterator($sub_files));
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
		if(is_dir($dir)) {
			if (substr($dir,-1)!='/')
			  $dir.='/';
			$dh = opendir($dir);
			while(($file = readdir($dh)) != false) {
				if (is_file($dir.$file)) {
					$name=substr($dir.$file,strlen($removePath));
					$ret[$name]=$dir.$file;
				}
				else if(is_dir($dir.$file) && ($file[0] != '.')) {
					$sub=$this->_getFiles($dir.$file);
					foreach($sub as $item) {
					    $name=substr($item,strlen($removePath));
					    $ret[$name]=$item;
					}
				}
			}
			closedir($dh);
		}
		else if (is_file($dir)) {
		    $name=substr($dir,strlen($removePath));
		    $ret[$name]=$dir;
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
