<?php
    require "DatabaseCon.php";

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $con=connectToDB();
        $action = $_POST['Action'];
        $Kundennamen = htmlspecialchars($_POST['Kundenname']);
        //echo $Kundennamen;
        $Sitz = htmlspecialchars($_POST['Sitz']);
        //echo $Sitz;
        if($con){
            switch ($action){
                case "set":
                    setReservierung($Kundennamen,$Sitz,$con);
                    break;
                default:
                    echo "keine Aktion ausgewählt";
            }
        }
    }

    function setReservierung($Kundennamen, $Sitz, $con){
        if($Kundennamen==null){
            echo "Kundenname fehlt";
            return;
        }
        if($Sitz==null){
            echo "Sitz fehlt";
            return;
        }
        $connect = mysqli_query($con, "SELECT COUNT(*) FROM sitzplatz;");
        if($connect&& ($Sitz>mysqli_fetch_array($connect)[0])){
            echo "Sitz nicht vorhanden";
            return;
        }

        if(getKundenID($Kundennamen,$con)==0){
            fügeKundenHinzu($Kundennamen,$con);
        }

        $Kunde = getKundenID($Kundennamen,$con);
        
        $connect = mysqli_query($con, "INSERT INTO reservierung(KundenID,SitzplatzID) VALUES ('$Kunde','$Sitz');");
        if(!$connect){
            echo "Kundenname könnte nicht gefunden werden";
        }else{
            echo "Reservierung hinzugefügt";
        }
    }

    function fügeKundenHinzu($Kundennamen,$con){
        $connect = mysqli_query($con, "INSERT INTO kunde(Kundenname) VALUES ('$Kundennamen');");
        if(!$connect){
            echo "Kundenname könnte nicht eingetragen werden";
        }else{
            echo "Kundeneintrag erfolgreich";
        }
    }

    function getKundenID($Kundennamen,$con){
        $connect = mysqli_query($con, "SELECT KundenID FROM kunde WHERE Kundenname='$Kundennamen';");
        if(!$connect){
            echo "Kundenname könnte nicht eingetragen werden";
            return 0;
        }else{
            return mysqli_fetch_array($connect)[0];
        }
    }
?>