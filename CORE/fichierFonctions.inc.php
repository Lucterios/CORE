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
// --- Last modification: Date 15 October 2009 23:00:07 By  ---

//@BEGIN@
function return_bytes($val) {
	$val = trim($val);
	$unite = strtolower($val{strlen($val)-1});
	switch($unite)
	{
		case 'g':
		$val *= 1024;
		case 'm':
		$val *= 1024;
		case 'k':
		$val *= 1024;
	}
	return $val;
}

function getRemainingStorageSize() {
	$file_remaining="conf/Remaining.Size";
	if (is_file($file_remaining))
		return 0+implode('', file($file_remaining));
	else
		return -1;
}

function taille_max_dl_fichier() {
	$marge = 1024*1024;
    	$post_max_size = @ini_get('post_max_size');
    	if(empty($post_max_size)) {
      	$post_max_size = @get_cfg_var('post_max_size');
		if(empty($post_max_size))
			$post_max_size = '6M';
	}
	$memory_limit = @ini_get('memory_limit');
	if(empty($memory_limit)) {
		$memory_limit = @get_cfg_var('memory_limit');
		if(empty($memory_limit))
			$memory_limit = '6M';
	}
	$upload_max_filesize = @ini_get('upload_max_filesize');
	if(empty($upload_max_filesize)) {
		$upload_max_filesize = @get_cfg_var('upload_max_filesize');
		if(empty($upload_max_filesize))
			$upload_max_filesize = '2M';
	}

	$post_max_size = return_bytes($post_max_size);
	$memory_limit = return_bytes($memory_limit);
	$upload_max_filesize = return_bytes($upload_max_filesize);

	if($memory_limit < $post_max_size)
		$post_max_size = $memory_limit;

    $taille_max_dl = $post_max_size - $marge;
    if($taille_max_dl > $upload_max_filesize)
      $taille_max_dl = $upload_max_filesize;

    $remaining_size=getRemainingStorageSize();
    if (($remaining_size>=0) && ($remaining_size<$taille_max_dl))
    		return $remaining_size;
    else
    		return $taille_max_dl;
}

function convert_taille($taille_octet) {
	if($taille_octet < 1024)
		return $taille_octet.' Oc.';
	elseif($taille_octet >= 1024 && $taille_octet < 1048576)
		return round($taille_octet/1024,2).' Ko.';
	elseif($taille_octet >= 1048576 && $taille_octet < 1073741824)
		return round(($taille_octet/1024)/1024,2).' Mo.';
	elseif($taille_octet >= 1073741824 && $taille_octet < 1099511627776)
		return round((($taille_octet/1024)/1024)/1024,2).' Go.';
	else
		return round(((($taille_octet/1024)/1024)/1024)/1024,2).' To.';
}
//@END@
?>
