<?php
    require "DatabaseCon.php";

    /*in debugging deactive*/
    error_reporting(E_NOTICE);

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $con = connectToDB();
        $action = $_POST['Action'];
        if($con){
            switch ($action){
                case "display":
                    echo display($con);
                    break;
                default:
                    $Sitzreihen = intval(htmlspecialchars($_POST['Sitzreihe']));
                    $Länge = intval(htmlspecialchars($_POST['Laenge']));
                    //echo $Sitzreihen*$Länge;
                    //echo ' und ';
                    $connect = mysqli_query($con, "SELECT COUNT(*) FROM sitzplatz;");
                    if(!$connect){
                        echo "Anzahl der Sitzplätze könnte nicht ermittelt werden";
                    }
                    else{
                        $counter = mysqli_fetch_array($connect)[0];
                        //echo $counter;
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

    function addSitzplatz($ID){
        $con = connectToDB();
        if($con){
            $connect = mysqli_query($con, "INSERT INTO sitzplatz(SitzplatzID) VALUES ('$ID');");
            if(!$connect){
                echo "Sitzplatz könnte nicht erstellt werden";
            }
        }
    }

    function deleteSitzplätze($übrigeSitze){
        $con = connectToDB();
        if($con){
            $connect = mysqli_query($con, "DELETE FROM sitzplatz WHERE SitzplatzID > '$übrigeSitze';");
            if(!$connect){
                echo "Sitzplatz könnte nicht gelöscht werden";
            }
        }
    }

    function display($con){
        /*$sql = "SELECT Lastname, Age FROM Persons ORDER BY Lastname";

        if ($result = mysqli_query($con, $sql)) {
            // Get field information for all fields
            while ($fieldinfo = mysqli_fetch_field($result)) {
                printf("Name: %s\n", $fieldinfo -> name);
                printf("Table: %s\n", $fieldinfo -> table);
                printf("max. Len: %d\n", $fieldinfo -> max_length);
            }
            mysqli_free_result($result);
        }*/

        $connect = mysqli_query($con, "SELECT Belegt FROM sitzplatz ORDER BY SitzplatzID;");
        if(!$connect){
            echo "Sitzplätze könnte nicht geladen werden";
            return null;
        }else{
            return mysqli_fetch_array($connect);
        }
    }
?>