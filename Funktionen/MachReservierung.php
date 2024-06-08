<?php
    require "DatabaseCon.php";

    /*in debugging deactive*/
    error_reporting(E_NOTICE);

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $con=connectToDB();
        $action = $_POST['Action'];
        if($con){
            switch ($action){
                case "set":
                    $Sitz = htmlspecialchars($_POST['Sitz']);
                    $Kundennamen = htmlspecialchars($_POST['Kundenname']);
                    setReservierung($Kundennamen,$Sitz,$con);
                    break;
                case "get":
                    $auswahl = $_POST['Auswahl'];
                    $inhalt = $_POST['Inhalt'];
                    echo getReservierung($auswahl,$inhalt,$con);
                    break;
                case "delete":
                    $inhalt = $_POST['Inhalt'];
                    echo deleteReservierung($inhalt,$con);
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
        
        changeSitzBelegung($Sitz,true,$con);
        $connect = mysqli_query($con, "INSERT INTO reservierung(KundenID,SitzplatzID) VALUES ('$Kunde','$Sitz');");
        if(!$connect){
            echo "Reservierung fehlgeschlagen";
            changeSitzBelegung($Sitz,false,$con);
        }else{
            echo "Reservierung hinzugefügt ";
        }
    }

    function fügeKundenHinzu($Kundennamen,$con){
        $connect = mysqli_query($con, "INSERT INTO kunde(Kundenname) VALUES ('$Kundennamen');");
        if(!$connect){
            echo "Kundenname könnte nicht eingetragen werden";
        }else{
            echo "Kundeneintrag erfolgreich ";
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

    function getReservierung($auswahl,$inhalt,$con){
        switch ($auswahl){
            case "Name":
                    $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzID as Sitzplatz,
                                                reservierung.ReservierungsID from reservierung
                                                join kunde on reservierung.KundenID = kunde.KundenID
                                                join sitzplatz on reservierung.SitzplatzID = sitzplatz.SitzplatzID
                                                WHERE kunde.Kundenname='$inhalt';");
                    if(!$connect){
                        echo "Daten könnten nicht geladen werden";
                        return 0;
                    }else{
                        $result = mysqli_fetch_row($connect);
                        return "Kunde: " . $result[0] . " Sitzplatz: " . $result[1] . " ReservierungsID: " . $result[2];
                    }
                    break;
            case "Sitz":
                $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzID as Sitzplatz,
                                            reservierung.ReservierungsID from reservierung
                                            join kunde on reservierung.KundenID = kunde.KundenID
                                            join sitzplatz on reservierung.SitzplatzID = sitzplatz.SitzplatzID
                                            WHERE sitzplatz.SitzplatzID='$inhalt';");
                if(!$connect){
                    echo "Daten könnten nicht geladen werden";
                    return 0;
                }else{
                    $result = mysqli_fetch_row($connect);
                    return "Kunde: " . $result[0] . " Sitzplatz: " . $result[1] . " ReservierungsID: " . $result[2];
                }
                break;
            default:
            $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzID as Sitzplatz,
                                        reservierung.ReservierungsID from reservierung
                                        join kunde on reservierung.KundenID = kunde.KundenID
                                        join sitzplatz on reservierung.SitzplatzID = sitzplatz.SitzplatzID
                                        WHERE reservierung.ReservierungsID='$inhalt';");
            if(!$connect){
                echo "Daten könnten nicht geladen werden";
                return 0;
            }else{
                $result = mysqli_fetch_row($connect);
                return "Kunde: " . $result[0] . " Sitzplatz: " . $result[1] . " ReservierungsID: " . $result[2];
            }
        }
    }

    function getSitzplatz($Reservierung,$con){
        $connect = mysqli_query($con, "SELECT SitzplatzID FROM reservierung WHERE ReservierungsID='$Reservierung';");
        if(!$connect){
            echo "Sitz könnte nicht ermittelt werden";
            return null;
        }else{
            return mysqli_fetch_array($connect)[0];
        }
    }

    function changeSitzBelegung($SitzplatzID,$neueBelegung,$con){
        if($SitzplatzID!=null){
            $connect = mysqli_query($con, "UPDATE sitzplatz SET Belegt = '$neueBelegung' WHERE SitzplatzID='$SitzplatzID';");
            if(!$connect){
                echo "fehler beim belegen";
            }
        }
        
    }

    function deleteReservierung($inhalt,$con){
        $Sitz = getSitzplatz($inhalt,$con);
        changeSitzBelegung($Sitz,false,$con);
        $connect = mysqli_query($con, "DELETE FROM reservierung WHERE ReservierungsID='$inhalt';");
        if(!$connect){
            echo "Daten könnten nicht gelöscht werden";
            changeSitzBelegung($Sitz,true,$con);
        }else{
            echo "erfolgreich gelöscht";
        }
    }
?>