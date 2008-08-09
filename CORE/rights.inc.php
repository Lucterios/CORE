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
// --- Last modification: Date 08 August 2008 22:51:18 By  ---

//@BEGIN@

function checkRight($login,$extension,$action) {
	global $connect;
	list($usec,$sec) = split(" ", microtime());
	$q = "SELECT cgr.value
FROM
	CORE_extension ce
		JOIN CORE_extension_actions cea
			ON(ce.id=cea.extension)
		JOIN CORE_extension_rights cer
			ON(cea.rights=cer.id)
		JOIN CORE_group_rights cgr
			ON(cer.id=cgr.rightref)
		JOIN CORE_groups cg
			ON(cgr.groupref=cg.id)
		JOIN CORE_users cu
			ON(cg.id=cu.groupId)
WHERE
	cu.login='$login' AND
	ce.extensionId='$extension' AND
	cea.action='$action'";
	$r = $connect->execute($q);
	while(list($droit) = $connect->getRow($r)) {
		if($droit == 'o') {
			break;
		}
	}
	list($usec2,$sec2) = split(" ", microtime());
	$t = ($sec2-$sec)+(($usec2-$usec)/10); logAutre("Demande de droit checkRight:$login,$extension,$actionreponse:$droittemps:$t");
	return ($droit == 'o');
}

//@END@
?>
