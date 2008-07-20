<?php
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


/* CONF */
$session_table="CORE_sessions";
$access_table="CORE_access";

function refresh_session($d,$timeOut,$link) {
	global $session_table;

	$time_limid_valid=$d-($timeOut*60); // time out de validité de la session => timeOut minutes
	$time_limid_delete=$d-($timeOut*60*60*24); // time out d'existance de la session => timeOut jours

	// mise hors service des sessions en time-out
	$link->execute("DELETE FROM $session_table WHERE dtmod<$time_limid_delete");
	$link->execute("UPDATE $session_table SET valid='n' WHERE valid='o' AND dtmod<$time_limid_valid");

	/*$res = $link->execute("SELECT sid, dtmod FROM $session_table WHERE uid='$uid' AND valid='o'");
	while(list($ses, $dtmod) = $link->getRow($res)) {
		if($d>$dtmod+($timeOut*60)) {
			$link->execute("UPDATE $session_table SET valid='n' WHERE sid='$ses'");
		}
	}*/
	
}

// create a session in database with limited acces to a lonly ip adress.
function get_session_id($uid, $timeOut, $link, $ip="0.0.0.0/0", $wayOfCheck="multiple") {
	global $session_table, $access_table;

	$d=time();
	refresh_session($d,$timeOut,$link);

	// decoupage de l'IP
	if(strstr($ip, "/")!=false)
		list($inetAddr, $netmask) = split("/", $ip);
	else {
		$inetAddr = $ip;
		$netmask = 32;
	}
	
	list($classA, $classB, $classC, $classD) = split("\.", $inetAddr);
	// chargement de la liste des IPs autorisée à se connecter
	$q = "SELECT inetAddr FROM $access_table WHERE inetAddr LIKE '$classA.%' OR inetAddr LIKE '255.%'";
	$r = $link->execute($q);
	$ipIsAutorised = false;
	while(list($inet) = $link->getRow($r)) {
		// decoupage de inet
		list($dbinet, $dbnetmask) = split("/", $inet);
		list($DBclassA, $DBclassB, $DBclassC, $DBclassD) = split("\.", $dbinet);
		
		// verif de la pertinance et dc du droit
		if($dbnetmask<=$netmask) {
			// verif de la compatibilité de la classe d'ip representee par $inet avec notre ip courante
			// on ne traite que les netmasks de classes completes
			if($netmask!=8 && $netmask!=16 && $netmask!=24 && $netmask!=32) return false;
			
			if($dbnetmask==8 && ($DBclassA==255 || $classA==$DBclassA)) $ipIsAutorised = true; // toutes classes A autorisées
			else {
				if($dbnetmask==16 && ($DBclassB==255 || $classB==$DBclassB)) $ipIsAutorised = true; // toutes classes B autorisées pour cette classe A
				else {
					if($dbnetmask==24 && ($DBclassC==255 || $classC==$DBclassC)) $ipIsAutorised = true; // toutes classes C autorisées pour cette classe B
					else {
						if($dbnetmask==32 && ($DBclassD==255 || $classD==$DBclassD)) $ipIsAutorised = true; // toutes classes D autorisées pour cette classe C
							else $ipIsAutorised = false;
					}
				}
			}
		}
	} // fin while
	if(!$ipIsAutorised) return false;
	
	// on effectue les verifs necessaires
	if($wayOfCheck == "singlecheck") {
		// reste t-il une session valide?
		$q="SELECT COUNT(*) FROM $session_table WHERE uid='$uid' AND valid='o'";
		list($nb)=$link->getRow($link->execute($q));
		// oui? alors on s'arrete la
		if($nb>0) return false;
	}
	elseif($wayOfCheck == "singleforce") {
		$q="UPDATE $session_table SET valid='n' WHERE uid='$uid'";
		$link->execute($q);
	}
	// on genere l'id session
	$sid = $uid.$d;
	$q="INSERT INTO $session_table (sid, uid, dtcreate, dtmod, valid, ip) VALUES ('$sid', '$uid', '$d', '$d', 'o', '$ip')";
	if(!$link->execute($q)) print $link->errorMsg;
	//setcookie("ses", $sid, time()+($timeOut*60));
	return $sid;
}

// checks a session's validity taking care of the ip adress.
function verif_session($ses, $timeOut, $link, $ip="0.0.0.0/0") {
	global $session_table;
	$d=time();
	refresh_session($d,$timeOut,$link);
	$ses_ok=false;
	$res = $link->execute("SELECT dtmod, valid, ip FROM $session_table WHERE sid='$ses'");
	list($dtmod, $valid, $inet) = $link->getRow($res);
	if($valid=="o") {
		if(!strcmp($inet, "0.0.0.0/0") || !strcmp($inet, $ip)) {
			if($d>$dtmod+($timeOut*60)) {
				$link->execute("UPDATE $session_table SET valid='n' WHERE sid='$ses'");
			}
			else {
				$q="UPDATE $session_table SET dtmod='$d' WHERE sid='$ses'";
				$link->execute($q);
				//setcookie("ses", $ses, $d+($timeOut*60));
				$ses_ok = true;
			}
		}
	}
	return $ses_ok;
}

// disconnect a session
function deconnect($ses, $link) {
	global $session_table;
	$link->execute("UPDATE $session_table SET valid='n' WHERE sid='$ses'");
	//setcookie("ses");
}

?>
