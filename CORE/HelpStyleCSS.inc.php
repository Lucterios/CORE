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
// --- Last modification: Date 14 August 2008 21:25:56 By  ---

//@BEGIN@
 header('content-type: text/css');
$Color_Error = 'rgb(200, 0, 0)';
//$Color_BackMenu='rgb(202, 202, 254)';
$Color_BackMenu = 'rgb(230, 230, 230)';
$Color_BackCorp = 'white';
$Color_BackHeaderFooter = 'rgb(45, 45, 255)';
$Color_Footer = 'rgb(255, 255, 255)';
$Color_MenuContent = 'black';
$Color_Menutitle = $Color_BackHeaderFooter;
echo"@media screen {
h1 {
    font-family : Helvetica, serif;
    font-size : 20px;
    font-weight : bold;
    text-align : center;
    vertical-align : middle;
  }


h2 {
    font-size : 16px;
    font-style : italic;
    font-weight : bold;
  }


h3 {
    font-size : 12px;
    font-style : italic;
    text-decoration : underline;
  }

TABLE.main {
    height: 98%;
    background-color:$Color_BackMenu;
  }

}

@media print {
h1 {
    font-family : Helvetica, serif;
    font-size : 10mm;
    font-weight : bold;
    text-align : center;
    vertical-align : middle;
  }


h2 {
    font-size : 8mm;
    font-style : italic;
    font-weight : bold;
  }


h3 {
    font-size : 6mm;
    font-style : italic;
    text-decoration : underline;
  }

TABLE.main {
    height: 265mm;
    background-color:$Color_BackMenu;
  }

}

@media all {
BODY {
    background-color: white;
  }

a {
    text-decoration:none;
}

a:hover {
    text-decoration:underline;
}

a:link {
  color:$Color_MenuContent;
  }

a:visited {
  color:$Color_MenuContent;
}

img {
    border-style: none;
  }


/* corps */

TR.corps {
    width: 980px;
  }

TD.menu {
    width: 120px;
    vertical-align: top;
  }

TD.corps {
    width: 860px;
    vertical-align: top;
    background-color:$Color_BackCorp;
  }

/* pied */

TR.pied {
    width: 980px;
    height: 15px;
    font-size : 10pt;
    background-color:$Color_BackHeaderFooter;
  }


TD.pied {
    width: 980px;
    height: 15px;
    color:$Color_Footer;
    font-size: 10px;
    text-align: right;
    font-weight: bold;
  }

/* menucontent */

table.menucontent {
    font-size : 11pt;
    background-color:$Color_BackMenu;
    width: 90%;
  }

tr.menucontent {
    font-size : 10pt;
    width: 100%;
  }

th.menucontent {
    font-size : 11pt;
    width: 100%;
    background-color:$Color_BackMenu;
    text-align: left;
    color: grey;
  }

a.menuhead {
    font-size : 11pt;
    width: 100%;
    background-color:$Color_BackMenu;
    text-align: left;
    color: grey;
  }

td.menucontent {
    font-size : 11pt;
    width: 100%;
    background-color:$Color_BackMenu;
    text-align: left;
    color:$Color_MenuContent;
  }

a.menucontent {
    text-decoration: none;
    color: orange;
  }

a.menuheader {
    text-decoration: none;
    color:$Color_MenuContent;
  }

/* grid */

table.grid {
    border-style: solid;
    border-width: 1px;
  }

th.grid {
    border-style: solid;
    border-width: 1px;
  }


td.grid {
    border-style: solid;
    border-width: 1px;
  }


.go {
    font-family : verdana,helvetica;
    font-size : 10pt;
    font-style : oblique;
    text-decoration : none;
    text-indent : 2cm;
  }

.error {
    color:$Color_Error;
    font-family : Helvetica, serif;
    font-size : 7mm;
    font-weight : bold;
    text-align : center;
    vertical-align : middle;
}

TD {
    font-size: 10pt;
    font-family: verdana,helvetica;
  }


a.invisible {
    color: rgb(18, 0, 180);
    font-size: 0px;
  }


li {
    font-size : 10pt;
    font-weight : bolder;
  }


ul {
    font-size : 10pt;
    font-style : italic;
  }

table.help{
    width: 100%;
}

.title{
    font-family : Helvetica, serif;
    font-size : 9mm;
    font-weight : bold;
    text-align : center;
    vertical-align : middle;
    color:$Color_Menutitle;
}

.content{
    font-family : Helvetica, serif;
    font-size : 10pt;
    text-align : left;
    vertical-align : top;
    color:$Color_MenuContent;
}
}";

//@END@
?>
