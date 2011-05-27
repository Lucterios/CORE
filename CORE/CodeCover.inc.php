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
// --- Last modification: Date 25 May 2011 2:12:43 By  ---

//@BEGIN@
class CodeCover {

	var $metrics=array();
	var $started=false;
	var $actif=false;

	function CodeCover($actif){
		$this->actif=$actif;
		if (!function_exists('xdebug_start_code_coverage') || !function_exists('xdebug_stop_code_coverage') || !function_exists('xdebug_get_code_coverage'))
			$this->actif=false;
	}

	function load($extDir) {
		if (!$this->actif)
			return;
		$dh = @opendir($extDir);
		while(($currentfile = @readdir($dh)) != false) {
			$path=realpath("$extDir/$currentfile");
			$val=$this->_initial($path);
		}
		@closedir($dh);
	}

	function startCodeCover(){
		if (!$this->actif)
			return;
		if ($this->started)
		    stopCodeCover();
		xdebug_start_code_coverage();
		$this->started=true;
	}

	function _convertFileName($file_name) {
		$name=basename($file_name);
		$root=basename(substr($file_name,0,-1*strlen($name)));
		return $root."|".$name;
	}

	function _initial($fileName) {
		if ((substr($fileName,-8)=='.act.php') || (substr($fileName,-8)=='.mth.php') || (substr($fileName,-8)=='.inc.php')) {
			if (substr($fileName,-8)=='.inc.php') {
				$BEGIN_TAG='//@BEGIN@';
				$END_TAG='//@END@';
			}
			else {
				$BEGIN_TAG='//@CODE_ACTION@';
				$END_TAG='//@CODE_ACTION@';
			}
			$fileId=$this->_convertFileName($fileName);
			if (!isset($this->metrics[$fileId]) && !in_array($fileId,array('CORE|CodeCover.inc.php','CORE|UnitTest.inc.php','applis|application.inc.php')) ) {
				$lineList=array();
				$begin=false;
				$source_code = file($fileName);
				foreach($source_code as $line=>$source) {
					$source=trim($source);
					if ($begin && ($source==$END_TAG))
						$begin=false;
					else {
						if (($begin) && ($source!='')  && ($source!='else') && ($source!='{')  && ($source!='}') && (substr($source,0,2)!='//') && (substr($source,0,8)!='function'))
							$lineList[$line+1]=0;
						if (!$begin && ($source==$BEGIN_TAG))
							$begin=true;
					}
				}
				$this->metrics[$fileId]=$lineList;
			}
			return $this->metrics[$fileId];
		}
		else
			return null;
	}

	function stopCodeCover(){
		if (!$this->actif)
			return;
		if ($this->started) {
			file_put_contents("tmp/code_coverage.var","<?php \$code_coverage_analysis = ".var_export(xdebug_get_code_coverage(),TRUE)." ?>");
			xdebug_stop_code_coverage(true);
			$this->started=false;
			require_once "tmp/code_coverage.var";
			foreach($code_coverage_analysis as $file_name=>$lines_executed) {
				$lineList=$this->_initial($file_name);
				if (is_array($lineList)) {
					foreach($lines_executed as $num=>$val) {
						if (isset($lineList[$num]))
							$lineList[$num]=$lineList[$num]+$val;
					}
					$fileId=$this->_convertFileName($file_name);
					$this->metrics[$fileId]=$lineList;
				}
			}
		}
	}

	function AllCover() {
		if (!$this->actif)
			return "";
		if ($this->started)
		    stopCodeCover();
		ksort($this->metrics);
		$global_linesNB=0.0;
		$global_linesOK=0.0;
		
		$string_text="<sources>\n";
		$string_text.="<source>testing</source>\n";
		$string_text.="</sources>\n";
		$string_text.="<packages>\n";

		$last_ext="";
		$current_ext="";
		foreach($this->metrics as $file_name=>$lineList) {
			$pos=strpos($file_name,'|');
			$current_ext=substr($file_name,0,$pos);
			$name=substr($file_name,$pos+1);
			$name="[".strtoupper(substr($name,-7,-4))."] ".str_replace('_APAS_','::',substr($name,0,-8));
			if ($current_ext!=$last_ext) {
				if ($last_ext!='') {
					$class_text.="</classes>\n";
					if ($total_linesNB>=1)
						$rate=1.0*$total_linesOK/$total_linesNB;
					else
						$rate=0.0;
					$string_text.="<package name='$last_ext' line-rate='$rate' branch-rate='1.0' complexity='1.0'>\n";
					$string_text.=$class_text;
					$string_text.="</package>\n";
				}
				$global_linesNB=$global_linesNB+(int)$global_linesOK;
				$global_linesOK=$global_linesOK+(int)$total_linesOK;
				$total_linesNB=0.0;
				$total_linesOK=0.0;
				$class_text="<classes>\n";
				$last_ext=$current_ext;
			}

			$line_text="";
			$linesNB=0.0;
			$linesOK=0.0;
			foreach($lineList as $num=>$val) {
				$line_text.="<line number='$num' hits='$val' branch='false'/>\n";
				$linesNB=$linesNB+1.0;
				if ($val>0)
					$linesOK=$linesOK+1.0;
			}
			$file_name=str_replace('|','/',$file_name);
			if ($current_ext!='CORE')
				$file_name="extensions/$file_name";
			if ($linesNB>=1)
				$rate=$linesOK/$linesNB;
			else
				$rate=0.0;
			$class_text.="<class name='$name' filename='$file_name' line-rate='$rate' branch-rate='1.0' complexity='1.0'>\n";
			$class_text.="<lines>\n";
			$class_text.=$line_text;
			$class_text.="</lines>\n";
			$class_text.="</class>\n";
			$total_linesNB=$total_linesNB+$linesNB;
			$total_linesOK=$total_linesOK+$linesOK;
		}

		$global_linesNB=$global_linesNB+(int)$total_linesNB;
		$global_linesOK=$global_linesOK+(int)$total_linesOK;
		if ($current_ext!='') {
			$class_text.="</classes>\n";
			if ($total_linesNB>=1)
				$rate=$total_linesOK/$total_linesNB;
			else
				$rate=0.0;
			$string_text.="<package name='$current_ext' line-rate='$rate' branch-rate='1.0' complexity='1.0'>\n";
			$string_text.=$class_text;
			$string_text.="</package>\n";
		}

		if ($global_linesNB>=1)
			$rate=$global_linesOK/$global_linesNB;
		else
			$rate=0.0;
		$string_text="<coverage line-rate='$rate' branch-rate='1' lines-covered='$global_linesOK' lines-valid='$global_linesNB' branches-covered='1' branches-valid='1' complexity='1.0'>\n".$string_text;
		$string_text.="</packages>\n";
		$string_text.="</coverage>\n";
		return $string_text;
	}

}
//@END@
?>
