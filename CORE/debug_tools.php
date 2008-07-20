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


function Array_To_String(&$data,$tab=0)
{
	$pred=str_pad("", $tab, "\t");
	$pred_sup=$pred."\t";
	$result="";
	if (is_array($data))
	{
		foreach($data as $key=>$val)
		{
			$result.="\n".$pred_sup;
			if (is_string($key))
				$result.="'$key'=>";
			$result.=Array_To_String($val,$tab+1);
		}
		$result="array(".$result.")";
	}
	elseif (is_object($data))
	{
		$result.="Object ";
		$result.=get_class($data)."(\n";
		$fields=get_object_vars($data);
		foreach($fields as $fn=>$fv)
		{
			if(strtolower(get_class($data)) != "xmlelement" || $fn != "father")
				$result.=$pred_sup."'$fn'=>".Array_To_String($fv,$tab+1)."\n";
		}
		$result.=$pred.")\n";
		$result=$pred.$result;
	}
	elseif (is_string($data))
	{
		$result.="'$data'";
	}
	elseif (is_bool($data))
	{
		if ($data)
			$result.="true";
		else
			$result.="false";
	}
	else
	{
		$result.=$data;
	}
	return $result;
}


?>
