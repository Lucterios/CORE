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
// --- Last modification: Date 27 June 2011 13:46:58 By  ---

//@BEGIN@
class CodeCover {

	private $mMetrics=array();
	private $mStarted=false;
	private $mActif=false;

	public function __construct($pActif){
		$this->mActif=$pActif;
		if (!function_exists('xdebug_start_code_coverage') || !function_exists('xdebug_stop_code_coverage') || !function_exists('xdebug_get_code_coverage'))
			$this->mActif=false;
	}

	public function load($extDir) {
		if (!$this->mActif)
			return;
		$dh = @opendir($extDir);
		while(($currentfile = @readdir($dh)) != false) {
			$path=realpath("$extDir/$currentfile");
			$val=$this->initial($path);
		}
		@closedir($dh);
	}

	public function startCodeCover(){
		if (!$this->mActif)
			return;
		if ($this->mStarted)
		    stopCodeCover();
		xdebug_start_code_coverage();
		$this->mStarted=true;
	}

	private function convertFileName($file_name) {
		$name=basename($file_name);
		$root=basename(substr($file_name,0,-1*strlen($name)));
		return $root."|".$name;
	}

	private function initial($fileName) {
		if ((substr($fileName,-8)=='.act.php') || (substr($fileName,-8)=='.mth.php') || (substr($fileName,-8)=='.inc.php') || (substr($fileName,-8)=='.evt.php')) {
			if (substr($fileName,-8)=='.inc.php') {
				$BEGIN_TAG='//@BEGIN@';
				$END_TAG='//@END@';
			}
			else {
				$BEGIN_TAG='//@CODE_ACTION@';
				$END_TAG='//@CODE_ACTION@';
			}
			$fileId=$this->convertFileName($fileName);
			if (!isset($this->mMetrics[$fileId]) && !in_array($fileId,array('CORE|CodeCover.inc.php','CORE|UnitTest.inc.php','applis|application.inc.php','applis|setup.inc.php','applis|postInstallation.inc.php')) ) {
				$lineList=array();
				$begin=false;
				$comment=false;
				$source_code = file($fileName);
				foreach($source_code as $line=>$source) {
					$source=trim($source);
					if (!$comment && (substr($source,0,2)=='/*'))
						$comment=true;
					if (!$comment) {
						if ($begin && ($source==$END_TAG))
							$begin=false;
						else {
							if (($begin) && ($source!='')  && ($source!='else') && ($source!='{')  && ($source!='}') && (substr($source,0,2)!='//') && (substr($source,0,9)!='public function ') && (substr($source,0,6)!='class ') && (substr($source,0,4)!='var ') && (substr($source,0,7)!='define(') && (substr($source,0,7)!='public ') && (substr($source,0,8)!='private ') && (substr($source,0,10)!='protected ') && (substr($source,0,7)!='require') && (substr($source,0,7)!='include') && (substr($source,0,7)!='global '))
								$lineList[$line+1]=0;
							if (!$begin && ($source==$BEGIN_TAG))
								$begin=true;
						}
					}
					if ($comment && (substr($source,-2)=='*/'))
						$comment=false;
				}
				$this->mMetrics[$fileId]=$lineList;
			}
			if (isset($this->mMetrics[$fileId]))
				return $this->mMetrics[$fileId];
			else
				return null;
		}
		else
			return null;
	}

	public function stopCodeCover(){
		if (!$this->mActif)
			return;
		if ($this->mStarted) {
			unset($code_coverage_analysis);
			file_put_contents("tmp/code_coverage.var","<?php\n\$code_coverage_analysis = ".var_export(xdebug_get_code_coverage(),TRUE)."\n?>");
			xdebug_stop_code_coverage(true);
			$this->mStarted=false;
			require "tmp/code_coverage.var";
			foreach($code_coverage_analysis as $file_name=>$lines_executed) {
				$lineList=$this->initial($file_name);
				if (is_array($lineList)) {
					foreach($lines_executed as $num=>$val) {
						if (isset($lineList[$num]))
							$lineList[$num]=$lineList[$num]+$val;
					}
					$fileId=$this->convertFileName($file_name);
					$this->mMetrics[$fileId]=$lineList;
				}
			}
		}
	}

	public function AllCover() {
		if (!$this->mActif)
			return "";
		if ($this->mStarted)
		    stopCodeCover();
		ksort($this->mMetrics);
		$global_linesNB=0.0;
		$global_linesOK=0.0;
		$total_linesNB=0.0;
		$total_linesOK=0.0;

		$string_text="<sources>\n";
		$string_text.="<source>testing</source>\n";
		$string_text.="</sources>\n";
		$string_text.="<packages>\n";

		$last_ext="";
		$current_ext="";
		foreach($this->mMetrics as $file_name=>$lineList) {
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
				$global_linesNB=$global_linesNB+(int)$total_linesNB;
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
