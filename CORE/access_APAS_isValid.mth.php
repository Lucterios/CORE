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


require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/access.tbl.php');
//@TABLES@

//@DESC@Verifier la validé un acces
//@PARAM@ 

function access_APAS_isValid(&$self)
{
//@CODE_ACTION@
$mask=32;
$self->inetAddr=trim($self->inetAddr);
$inetAddr=trim($self->inetAddr);
if ($inetAddr=="")
     return "classes=0 []";
$pos=strpos($self->inetAddr,"/");
if ($pos!==FALSE)
{
   $mask=trim(substr($inetAddr,$pos+1));
   $inetAddr=trim(substr($inetAddr,0,$pos));
   if (!is_numeric($mask))
     return "masque='$mask'";
   $mask=(int)$mask;
   if ($mask<1)
     return "masque='$mask'";
   if ($mask>32)
     return "masque='$mask'";
}

$cls=split("\\.",$inetAddr);
$Addr_txt="";
foreach($cls as $cl)
	$Addr_txt.="$cl,";
if ($Addr_txt!="")
	$Addr_txt=substr($Addr_txt,0,-1);

if ((count($cls)==1) && (trim($cls[0])==''))
     return "classes=0 [$Addr_txt]";
if (count($cls)!=4)
     return "classes=".count($cls)." [$Addr_txt]";

for($i=0;$i<=3;$i++)
{
   $cls[$i]=trim($cls[$i]);
   if (!is_numeric($cls[$i]))
     return "classes=".($i+1)." [$Addr_txt]";
   $cls[$i]=(int)$cls[$i];
   if (($cls[$i]<0) || ($cls[$i]>255))
     return "classes=".($i+1)." [$Addr_txt]";
}

if ($cls[0]==0)
  return "classes=1 [$Addr_txt]";

$self->inetAddr=$cls[0].'.'.$cls[1].'.'.$cls[2].'.'.$cls[3].'/'.$mask;

return "";
//@CODE_ACTION@
}

?>
