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
// --- Last modification: Date 05 March 2008 21:59:58 By  ---

//@BEGIN@
function current_date()
{
  return date("H:i:s j/m/Y");
}

function __log(&$XMLinput,$Title)
{
	//require_once 'debug_tools.php';
  	global $tmpPath, $debugMode;

        if($debugMode == 'o') {
	  // debug: requette logu?fichier
	  $msg = "---$Title------".current_date()."----------------------------\n";
	  $msg.= print_r($XMLinput, true);//Array_To_String($XMLinput);
	  $msg.= "\r\n\r\n";

	  $f = fopen($tmpPath."LuceriosCORE.log", "a");
	  fwrite($f, $msg);
	  fclose($f);
        }
}

function logRequette($XMLinput)
{
	__log($XMLinput,"REQUETTE");
}

function logReponse($reponse)
{
	__log($reponse,"REPONSE");
}

function logAutre($message)
{
	__log($message,"AUTRE");
}
//@END@
?>
