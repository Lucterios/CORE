<?php
// Test file write by SDK tool
// --- Last modification: Date 06 November 2008 20:30:49 By  ---


//@TABLES@
//@TABLES@

//@DESC@Test de l'import CSV
//@PARAM@ 

function CORE_Import(&$test)
{
//@CODE_ACTION@
$textCVS="";
$textCVS.="Ordre;Nom;Prenom;Sexe;Adresse;Cdpostal;Ville{[newline]}";
$textCVS.="3;MACHIN;Céline;1;41 rue du Grand;38600;FONTAINE{[newline]}";
$textCVS.="8;BIDULE;Steve;0;21 rue H.;38360;SASSENAGE{[newline]}";
$textCVS.="78;TRUC;Laurent;0;74 rue Charles de GAULLE;38600;FONTAINE{[newline]}";

$rep=$test->CallAction("CORE","importGrid",array("extension"=>"CORE", "action"=>"importGrid", "textCVS"=>$textCVS),"Xfer_Container_Custom");
$test->assertEquals(2,COUNT($rep->m_actions));
$test->assertEquals(new Xfer_Action("Valider", "ok.png", "CORE", "importGrid","0","1"),$rep->m_actions[0]);
$test->assertEquals(new Xfer_Action("Annuler", "cancel.png"),$rep->m_actions[1]);

$test->assertEquals(2,$rep->getComponentCount());
$comp=$rep->getComponents(0);
$test->assertClass("Xfer_Comp_Labelform",$comp);

$comp=$rep->getComponents(1);
$test->assertEquals(0,count($comp->m_actions));

$test->assertClass("Xfer_Comp_Grid",$comp);
$test->assertEquals("gridcvs",$comp->m_name);
$test->assertEquals(7,count($comp->m_headers));
$headers=array_keys($comp->m_headers);
$test->assertEquals('Ordre',$headers[0]);
$test->assertEquals('Ordre',$comp->m_headers['Ordre']->m_descript);
$test->assertEquals('Nom',$headers[1]);
$test->assertEquals('Nom',$comp->m_headers['Nom']->m_descript);
$test->assertEquals('Prenom',$headers[2]);
$test->assertEquals('Prenom',$comp->m_headers['Prenom']->m_descript);
$test->assertEquals('Sexe',$headers[3]);
$test->assertEquals('Sexe',$comp->m_headers['Sexe']->m_descript);
$test->assertEquals('Adresse',$headers[4]);
$test->assertEquals('Adresse',$comp->m_headers['Adresse']->m_descript);
$test->assertEquals('Cdpostal',$headers[5]);
$test->assertEquals('Cdpostal',$comp->m_headers['Cdpostal']->m_descript);
$test->assertEquals('Ville',$headers[6]);
$test->assertEquals('Ville',$comp->m_headers['Ville']->m_descript);
$test->assertEquals(3,count($comp->m_records));
$key=array_keys($comp->m_records);
$test->assertEquals(0,$key[0]);
$test->assertEquals(1,$key[1]);
$test->assertEquals(2,$key[2]);

$test->assertEquals(7,count($comp->m_records[$key[0]]));
$test->assertEquals("3",$comp->m_records[$key[0]]['Ordre']);
$test->assertEquals("MACHIN",$comp->m_records[$key[0]]['Nom']);
$test->assertEquals("1",$comp->m_records[$key[0]]['Sexe']);
$test->assertEquals("41 rue du Grand",$comp->m_records[$key[0]]['Adresse']);
$test->assertEquals("38600",$comp->m_records[$key[0]]['Cdpostal']);
$test->assertEquals("FONTAINE",$comp->m_records[$key[0]]['Ville']);

$test->assertEquals("8",$comp->m_records[$key[1]]['Ordre']);
$test->assertEquals("BIDULE",$comp->m_records[$key[1]]['Nom']);
$test->assertEquals("0",$comp->m_records[$key[1]]['Sexe']);
$test->assertEquals("21 rue H.",$comp->m_records[$key[1]]['Adresse']);
$test->assertEquals("38360",$comp->m_records[$key[1]]['Cdpostal']);
$test->assertEquals("SASSENAGE",$comp->m_records[$key[1]]['Ville']);

$test->assertEquals("78",$comp->m_records[$key[2]]['Ordre']);
$test->assertEquals("TRUC",$comp->m_records[$key[2]]['Nom']);
$test->assertEquals("0",$comp->m_records[$key[2]]['Sexe']);
$test->assertEquals("74 rue Charles de GAULLE",$comp->m_records[$key[2]]['Adresse']);
$test->assertEquals("38600",$comp->m_records[$key[2]]['Cdpostal']);
$test->assertEquals("FONTAINE",$comp->m_records[$key[2]]['Ville']);
//@CODE_ACTION@
}

?>
