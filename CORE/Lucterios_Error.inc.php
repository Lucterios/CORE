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
// --- Last modification: Date 03 August 2007 16:01:43 By Laurent GAY ---

//@BEGIN@

define('PHP',0);
define('CRITIC',1);
define('GRAVE',2);
define('IMPORTANT',3);
define('MINOR',4);

/**
* Définition de la classe d'exception Lucterios
*/
class LucteriosException extends Exception
{
	public function __construct($code,$message) {
		parent::__construct($message, $code);
	}

	private function arrayToString($value,$sep=false){
		if (is_array($value))
		{

			if ($sep) $text="("; else $text=" ";
			foreach($value as $val)
				$text.=$this->arrayToString($val,true).",";
			if ($sep) $text.=")";
			return $text;
		}
		else if (is_object($value))
			return get_class($value);
		else
			return "$value";
	}

	public function __toString() {
		$error_text= " [{$this->code}]: {$this->message}\n";
		$error_text.="'".$this->getCode()."' - File :".$this->getFile()." Line ".$this->getLine()."\n";
		$error_text.=" **** Stack trace: **** \n";
		foreach($this->getTrace() as $num=>$trace)
			$error_text.="#$num :".$this->arrayToString($trace)."\n";
		return $error_text;
	}

}

function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
	$errortype = array (E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR);
	if (in_array($errno,$errortype))
	{
		echo "ERROR #$errno : '$errmsg' - file: $filename - line $linenum - $vars<br>";
	}
}

set_error_handler("userErrorHandler");








//@END@
?>
