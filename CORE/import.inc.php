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
// --- Last modification: Date 04 October 2008 11:37:56 By  ---

//@BEGIN@

function ConvertCVSLigne($line,$first_line = null) {
	$CVSline = explode(";",$line);
	if( count($CVSline)>1) {
		if($first_line != null) {
			$cvs_line = array();
			$max_line = min( count($CVSline), count($first_line));
			for($i = 0;
			$i<$max_line;
			$i++) {
				$col_name = $first_line[$i];
				$value = $CVSline[$i];
				if($value[0] == '"')$value = substr($value,1,-1);
				$value = trim($value);
				$date_value = explode('/',$value);
				if(( strpos( strtolower($col_name),"date") !== false) && ( count($date_value) == 3)) {
					list($dd,$mm,$yyyy) = $date_value;
					$value = "$yyyy-$mm-$dd";
				}
				$cvs_line[$col_name] = $value;
			}
			return $cvs_line;
		}
		else
		return $CVSline;
	}
	else
	return null;
}

function ConvertTextToCVS($textCVS) {
	$CVS_array = array();
	$textCVS = str_replace("{[newline]}","\n",$textCVS);
	$arrayCVS = explode("\n",$textCVS);
	if( count($arrayCVS)>0) {
		$first_line = ConvertCVSLigne($arrayCVS[0]);
		unset($arrayCVS[0]);
		$index = 0;
		foreach($arrayCVS as $line) {
			$cvs_line = ConvertCVSLigne($line,$first_line);
			if( is_array($cvs_line)) {
				$CVS_array[$index] = $cvs_line;
				$index++;
			}
		}
	}
	return $CVS_array;
}

function ConvertCSVTextToSimpleArray($textCSV) {
	if (is_string($textCSV)) {
		$CSV_array = array();
		$textCSV = str_replace("{[newline]}","\n",$textCSV);
		$arrayCSV = explode("\n",$textCSV);
		foreach($arrayCSV as $id=>$line) {
		      $CSV_array[$id]=explode(';',$line);
		}
		return $CSV_array;
	}
	return null;
}

//@END@
?>
