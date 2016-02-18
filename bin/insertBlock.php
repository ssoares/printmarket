<?php
// Création -> Sacha Vincent ::: 2012/01/25 
// Modification -> Sacha Vincent ::: 2012/03/20 

// Suivre les étapes ci-dessous
// 1- Modifier les paramètres de connexion à la base de données
// 2- Trouver le ID minimum et maximum des pages qui doivent recevoir un block texte et les mettre dans les variables -> $PageID_Min_Inclu et $PageID_Max_Inclu
// 3- Trouver le dernier numéro auto ID dans la table block, faire +1 et l'Ajouter à la varibale $cptDepart
// 4- Ajuster les langues et le texte temporaire à inclure pour chaque langue (s'il y a lieu) (Mettre en commentaire les langues qui ne s'appliquent pas)
// 5- Lancer cette page (script) sur un serveur PHP
// 6- Copier-coller le code dans PHPMyAdmin



/*************************/
// Étape 1 :: Connexion
$host = "209.222.235.12";
$username = "root";
$password = "_nucleos33";
$dbname = "rees";

$link = mysql_connect($host, $username, $password) or die(mysql_error());

mysql_select_db($dbname) or die(mysql_error());      


/*************************/
// Étape 2 ::: Page ID
$PageID_Min_Inclu = 8008; // Le page id de la table Pages ou PagesIndex
$PageID_Max_Inclu = 999999;     // Mettre un gros chiffre ex:999999 pur tout les page jusqu'au maximu,


/*************************/
// Étape 3 ::: Block ID
$cptDepart = 1;    // Auto ID de départ dans la table block -> prendre le dernier qui existe dans la base et faire +1
              

/*************************/
// Étape 4 ::: Table de langue (Mettre en commentaire si la langues ne s'applique)
$arrayLangue[] = array("index" => 1, "suffixe" => "fr", "TexteReplacement" => "<h2>Texte temporaire en français</h2><h3>Texte temporaire en français</h3><p>Eius feugiat oportere dúo tuvo, Probo dicit no sensibus mei, TIENE veniam gubergren pulgadas Hinc dolor scribentur ut eos, se sientan dissentias eleifend tenía. Mel y principios tritani efficiantur, no privado percipit sapientem pertinacia, clita fastidii EAM ea. Paulo cotidieque adversarium en Mel, de sumo por phaedrum ción. Ex asumiendo mentitum liberalización sentarse.</p><h4>Une liste</h4><ul><li>Elément 1</li><li>Elément 2</li><li>Elément 3</li></ul>");
$arrayLangue[] = array("index" => 2, "suffixe" => "en", "TexteReplacement" => "<h2>Temporary text in English</h2><h3>Temporary text in English</h3><p>Eius feugiat oportere dúo tuvo, Probo dicit no sensibus mei, TIENE veniam gubergren pulgadas Hinc dolor scribentur ut eos, se sientan dissentias eleifend tenía. Mel y principios tritani efficiantur, no privado percipit sapientem pertinacia, clita fastidii EAM ea. Paulo cotidieque adversarium en Mel, de sumo por phaedrum ción. Ex asumiendo mentitum liberalización sentarse.</p><h4>A list</h4><ul><li>Element 1</li><li>Element 2</li><li>Element 3</li></ul>");
//$arrayLangue[] = array("index" => 3, "suffixe" => "sp", "TexteReplacement" => "<h2>Temporal de texto en español</h2><h3>Temporal de texto en español</h3><p>Eius feugiat oportere dúo tuvo, Probo dicit no sensibus mei, TIENE veniam gubergren pulgadas Hinc dolor scribentur ut eos, se sientan dissentias eleifend tenía. Mel y principios tritani efficiantur, no privado percipit sapientem pertinacia, clita fastidii EAM ea. Paulo cotidieque adversarium en Mel, de sumo por phaedrum ción. Ex asumiendo mentitum liberalización sentarse.</p><h4>Una lista</h4><ul><li>Elemento 1</li><li>Elemento 2</li><li>Elemento 3</li></ul>");
        


/*************************/
// Normalement il n'y a plus rien à modifier sous cette ligne
$query = "Select PI_PageID , PI_PageTitle FROM PagesIndex WHERE PI_PageID >= " . $PageID_Min_Inclu . " AND PI_PageID <= " . $PageID_Max_Inclu ;
//$query = "Select PI_PageID , PI_PageTitle FROM PagesIndex WHERE PI_PageID > 4 AND PI_PageID < 9000";

$result = mysql_query($query) or die(mysql_error());  

$row = mysql_fetch_array( $result );


while($row = mysql_fetch_array($result))
{
    $arrayPageID[] = array("ID" => $row["PI_PageID"], "Nom" => $row["PI_PageTitle"]);
}

$SqlBlock = "INSERT INTO `Blocks` (`B_ID`, `B_PageID`, `B_ModuleID`, `B_ZoneID`, `B_Position`, `B_ShowHeader`, `B_Draft`, `B_Online`, `B_Secured`, `B_LastModified`) VALUES ";
    
$SqlBlockIndex = "INSERT INTO `BlocksIndex` (`BI_BlockID`, `BI_LanguageID`, `BI_BlockTitle`) VALUES ";
    
$SqlBlockParam = "INSERT INTO `Parameters` (`P_BlockID`, `P_Number`, `P_Value`) VALUES ";    

$SqlBlockTextData = "INSERT INTO `TextData` (`TD_BlockID`, `TD_LanguageID`, `TD_OnlineTitle`, `TD_OnlineText`, `TD_DraftTitle`, `TD_DraftText`, `TD_ToApprove`) VALUES ";

$ct = 0;     
foreach($arrayPageID as $PageID)
{       
    if($ct < (count($arrayPageID) -1))        
        $EndLine = ", ";
    else
        $EndLine = ";";
            
    $SqlBlock .= "(" . $cptDepart . "," . $PageID["ID"] . ", 1, 1, 1, 0, 0, 1, 0, NOW())" . $EndLine . "<br />";
    
    $i = 0;
    foreach($arrayLangue as $rowLangue)
    {        
        if($ct < (count($arrayPageID) -1))        
            $EndLine2 = ", ";
        else if($i < (count($arrayLangue)-1))
            $EndLine2 = ", ";            
        else
            $EndLine2 = ";";            
    
        $blockName = str_replace("'", "''", $PageID["Nom"] . ($rowLangue['index'] != 1 ? '_' . $rowLangue['suffixe'] : ''));                                
        $SqlBlockIndex .= "(" . $cptDepart . "," . $rowLangue['index'] . ", '" . $blockName . "')" . $EndLine2 . "<br />";
        
        $SqlBlockTextData .= "(" . $cptDepart . ", " . $rowLangue['index'] . ", NULL, '" . str_replace("'", "''", $rowLangue['TexteReplacement']) . "', NULL, '" . str_replace("'", "''", $rowLangue['TexteReplacement']) . "', 0)" . $EndLine2 . "<br />";
        
        $i++;
    }   
         
    $P_Value = 'index';
    $SqlBlockParam .= "(" . $cptDepart . ", 999, '" . $P_Value . "')" . $EndLine . "<br />";

    $cptDepart++;
    $ct++;
}


echo $SqlBlock;

echo "<br />";

echo $SqlBlockIndex;

echo "<br />";

echo $SqlBlockParam;

echo "<br />";

echo $SqlBlockTextData;

mysql_close($link);

?>