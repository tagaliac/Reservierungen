<?php
    /**import Database information*/
    require "DatabaseCon.php";

    /**import global variables */
    $path = "..\Globale_Variablen.json";
    $DEBUG_MODUS = json_decode(file_get_contents($path),false)->DEBUG_MODUS;

    /**in debugging deactive*/
    if(!$DEBUG_MODUS){
        error_reporting(E_NOTICE);
    }else{
        echo "Debug Modus aktiv\n";
    }

    /**coding */
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $con = connectToDB();
        $action = $_POST['Action'];
        if($con){
            /**Wählt Aktion */
            switch ($action){
                case "display":
                    echo display($con);
                    break;
                case "get":
                    $Sitzreihen = json_decode(file_get_contents($path),false)->Sitzreihen;
                    $SitzeProReihe = json_decode(file_get_contents($path),false)->SitzeProReihe;
                    echo $Sitzreihen . "|" . $SitzeProReihe;
                    break;
                case "set":
                    $jsonData=[
                        "DEBUG_MODUS" => $DEBUG_MODUS,
                        "Sitzreihen" => intval(htmlspecialchars($_POST['Sitzreihe'])),
                        "SitzeProReihe" => intval(htmlspecialchars($_POST['Laenge']))
                    ];
                    $jsonString = json_encode($jsonData, JSON_PRETTY_PRINT);
                    $fp = fopen($path, 'w');
                    fwrite($fp, $jsonString);
                    fclose($fp);
                    echo "erfolgreich in JSON gespeichert";
                    break;
                default:
                    $Sitzreihen = intval(htmlspecialchars($_POST['Sitzreihe']));
                    $Länge = intval(htmlspecialchars($_POST['Laenge']));
                    $connect = mysqli_query($con, "SELECT COUNT(*) FROM sitzplatz;");
                    if(!$connect){
                        echo "Anzahl der Sitzplätze könnte nicht ermittelt werden";
                    }
                    else{
                        $counter = mysqli_fetch_array($connect)[0];
                        if($counter<($Sitzreihen*$Länge)){
                            while($counter<($Sitzreihen*$Länge)){
                                $counter=$counter+1;
                                addSitzplatz($counter);
                            }
                        }else{
                            deleteSitzplätze($Sitzreihen*$Länge);
                        }
                        echo "Erfolgreich erstellt";
                    }
            }
        }
    }

    /**Fügt Sitzplatz hinzu mit der SitzID $ID */
    function addSitzplatz($ID){
        $con = connectToDB();
        if($con){
            $connect = mysqli_query($con, "INSERT INTO sitzplatz(SitzplatzID) VALUES ('$ID');");
            if(!$connect){
                echo "Sitzplatz könnte nicht erstellt werden";
            }
        }
    }

    /**Löscht die Sitzplätze bis auf die Anzahl $übrigeSitze */
    function deleteSitzplätze($übrigeSitze){
        $con = connectToDB();
        if($con){
            $connect = mysqli_query($con, "DELETE FROM sitzplatz WHERE SitzplatzID > '$übrigeSitze';");
            if(!$connect){
                echo "Sitzplatz könnte nicht gelöscht werden";
            }
        }
    }

    /**gibt ein String zurück, welcher ausdrückt, ob die jeweiligen Sitze belegt sind.
     * z.B 0|1|0| drückt aus dass der erste und der dritte Sitzplatz unbelegt sind und der zweite belegt
     */
    function display($con){
        $connect = mysqli_query($con, "SELECT Belegt FROM sitzplatz ORDER BY SitzplatzID;");
        if(!$connect){
            echo "Sitzplätze könnte nicht geladen werden";
            return null;
        }else{
            return convertDataToString($connect);
        }
    }

    /**macht aus den Belegungsdaten einen String (siehe display($con)) */
    function convertDataToString($data){
        $result = "";
        $rows = mysqli_fetch_all($data, MYSQLI_ASSOC);
        foreach ($rows as $row){
            $result = $result . $row["Belegt"] . "|";
        }
        return $result;
    }
?>