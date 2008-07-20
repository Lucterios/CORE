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
// --- Last modification: Date 10 October 2007 19:55:17 By Laurent GAY ---

//@BEGIN@

class Thread{
	var $func;
	var $arg;
	var $thisFileName;
	var $fp;
	var $host;
	var $port;
	function Thread($host="",$port=""){
		if ($host== ''){
			$this->host = $_SERVER['HTTP_HOST'];
			$this->port = $_SERVER['SERVER_PORT'];
		}
		else {
			$this->host = $host;
			if ($port != ""){
				$this->port = $port;
			}else{
				$this->port = 80;
			}
		}
		$this->setFileName();
	}
	function setFileName($FileName=""){
		if ($FileName=="")
			$this->thisFileName = $_SERVER["SCRIPT_NAME"];
		else
			$this->thisFileName = $FileName
;
	}
	function setFunc($func,$arg=false){
		$i=0;
		$this->arg = "";
		if ($arg){
			foreach ($arg as $argument){
				$this->arg .= "&a[]=".urlencode("$argument");
			}
		}
		$this->func = $func;
	}
	function setPort($port){
		$this->port = $port;
	}
	function setHost($host){
		$this->host = $host;
	}
	function start(){
		/*require_once("HTTP/Request.php");
		$updateserver='http://'.$this->host.':'.$this->port.'/'.$this->thisFileName."?threadrun=1&f=".urlencode($this->func).$this->arg;
		logAutre("Thread - updateserver=$updateserver");
		$this->fp =& new HTTP_Request($updateserver);
		$this->fp->setMethod(HTTP_REQUEST_METHOD_GET);
		$err=$this->fp->sendRequest();
		if (PEAR::isError($err))
			throw new LucteriosException(GRAVE,$err->getMessage());
*/

		$this->fp = fsockopen($this->host,$this->port);
		$header = "GET ".$this->thisFileName."?threadrun=1&f=".urlencode($this->func).$this->arg." HTTP/1.1\r\n";
		$header .= "Host: ".$this->host."\r\n";
		$header .= "Connection: Close\r\n\r\n";
		fwrite($this->fp,$header);
	}
	function getreturn(){
		//return $this->fp->getResponseBody();

		$flag=false;
		while (!feof($this->fp)) {
			$buffer = fgets($this->fp, 4096);
			if ($flag){
				$output .= $buffer;
			}
			if (trim($buffer) == ""){
				$flag = true;
			}
		}
		return trim($output);

	}
}
if (isset($_GET['threadrun'])){
	$arg_str = "";
	$i=0;
	if (isset($_GET['a'])){
		foreach($_GET['a'] as $argument){
			if ($i == 0){
				$arg_str .= "'$argument'";
			}else{
				$arg_str .= ",'$argument'";
			}
			$i++;
		}
	}
	$cmd="\$return = ".$_GET['f']."(".$arg_str.");";
	eval($cmd);
	echo $return;
	exit;
}






















//@END@
?>
