<?php
    define('host', 'localhost');
    define("user", "AdminReservierung");
    define("pass","Romania1234");
    define("db","Sitzordnung");

    function connectToDB(){
        $con = mysqli_connect(host, user, pass, db);
        if(!$con){
            echo "keine Verbindung zur Datenbank";
        }
        return $con;
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $con = connectToDB();
        if($con){
            $Sitzreihen = htmlspecialchars($_POST['Sitzreihe']);
            $Länge = htmlspecialchars($_POST['Laenge']);
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
?>