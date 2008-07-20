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
// --- Last modification: Date 03 December 2007 22:54:07 By Laurent GAY ---

//@BEGIN@
function checkRight($login, $extension, $action)
{
	global $connect;

	list($usec, $sec) = split(" ", microtime());

  	$q = "SELECT CORE_group_rights.value FROM CORE_group_rights, CORE_groups, CORE_users, CORE_extension_actions, CORE_extension_rights ";
  	$q.= "WHERE (CORE_group_rights.groupId=CORE_users.groupId OR CORE_group_rights.groupId='0') AND ";
  	$q.= "CORE_users.login='$login' AND CORE_group_rights.groupref=CORE_groups.id AND ";
  	$q.= "CORE_group_rights.rightref=CORE_extension_rights.id AND CORE_extension_rights.id=CORE_extension_actions.rights AND ";
  	$q.= "CORE_extension_actions.extensionId='$extension' AND CORE_extension_actions.action='$action'";
	$r = $connect->execute($q);
	while(list($droit) = $connect->getRow($r)) {
		if($droit == 'o') {
			break;
		}
	}

	list($usec2, $sec2) = split(" ", microtime());
	$t = ($sec2-$sec)+(($usec2-$usec)/10);
	logAutre("Demande de droit checkRight: $login, $extension, $action reponse: $droit temps: $t");
	return ($droit == 'o');
}

function checkGroupRight($group, $extension, $action)
{
	global $connect;

	$q = "SELECT value FROM CORE_extension_actions, CORE_group_rights ";
	$q.= "WHERE (CORE_group_rights.groupid='$group' OR CORE_group_rights.groupid='0') ";
	$q.= "AND CORE_group_rights.extensionId=CORE_extension_actions.extensionId ";
	$q.= "AND CORE_group_rights.rightId=CORE_extension_actions.rightId ";
	$q.= "AND CORE_extension_actions.extensionId='$extension' ";
	$q.= "AND CORE_extension_actions.action='$action' ORDER BY CORE_group_rights.groupid DESC";

	$r = $connect->execute($q);
	while(list($droit) = $connect->getRow($r)) {
		if($droit == 'o') {
			logAutre("Demande de droit checkGroupRight: $login, $extension, $action reponse: o");
			return 'o';
		}
	}

	logAutre("Demande de droit checkGroupRight: $login, $extension, $action reponse: $droit");

	return ($droit == 'o') ? 'o' : 'n';
}

function checkExtensionRight($group, $extension, $right)
{
	global $connect;

	$q = "SELECT value FROM CORE_group_rights a, CORE_extension_rights b WHERE ";
	$q.= "(a.groupid = $group OR a.groupid='0') AND ";
	$q.= "a.rightId = b.rightId ";
	$q.= "AND b.extensionId = '$extension' ";
	$q.= "AND b.extensionId = a.extensionId ";
	$q.= "AND b.rightId =$right";

	$r = $connect->execute($q);
	while(list($droit) = $connect->getRow($r)) {
		if($droit == 'o') {
			logAutre("Demande de droit checkExtensionRight: $login, $extension, $action reponse: o");
			return 'o';
		}
	}

	logAutre("Demande de droit checkExtensionRight: $login, $extension, $action reponse: $droit");

	return ($droit == 'o') ? 'o' : 'n';
}




//@END@
?>
