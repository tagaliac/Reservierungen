<?php
    /**import Database information*/
    require "DatabaseCon.php";

    /**import global variables */
    $DEBUG_MODUS = json_decode(file_get_contents("..\Globale_Variablen.json"),false)->DEBUG_MODUS;

    /**in debugging deactive*/
    if(!$DEBUG_MODUS){
        error_reporting(E_NOTICE);
    }else{
        echo "Debug Modus aktiv\n";
    }

    /**coding */
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $con=connectToDB();
        $action = $_POST['Action'];
        if($con){
            /**Wählt Aktion */
            switch ($action){
                case "set":
                    $Sitz = htmlspecialchars($_POST['Sitz']);
                    $Kundennamen = htmlspecialchars($_POST['Kundenname']);
                    $Bezahlort = htmlspecialchars($_POST['Bezahlort']);
                    $Email = htmlspecialchars($_POST['Email']);
                    setReservierung($Kundennamen,$Sitz,$Bezahlort,$Email,$con);
                    break;
                case "get":
                    $auswahl = $_POST['Auswahl'];
                    $inhalt = htmlspecialchars($_POST['Inhalt']);
                    echo getReservierung($auswahl,$inhalt,$con);
                    break;
                case "getFreienSitz":
                    return getFreiePlätze($con);
                    break;
                case "delete":
                    echo deleteReservierung($_POST['Inhalt'],$con);
                    break;
                case "Bezahlung":
                    echo setBezahlung($_POST["Kundenname"],$_POST['Inhalt'],$con);
                    break;
                default:
                    $connect = mysqli_query($con, $action);
                    if(!$connect){
                        throw "Befehl könnte nicht verarbeitet werden";
                    }else{
                        echo mysqli_fetch_array($connect)[0];
                    }
            }
        }
    }

    /**fügt einen Reservierungseintrag hinzu.
     * Wenn der Kunde nicht existiert wird dieser auch erstellt.
     */
    function setReservierung($Kundennamen, $Sitz, $Bezahlort,$Email, $con){
        /**Vorbedienungen */
        if($Kundennamen==null){
            echo "Kundenname fehlt\n";
            return;
        }
        if($Bezahlort==null){
            echo "Bezahlort fehlt\n";
            return;
        }
        if($Sitz==null||$Sitz===""){
            $Sitz = getFreiePlätze($con);
        }
        if(isBelegt($Sitz,$con)){
            echo "Sitz schon belegt\n";
            return;
        }

        /**Kunden finden */
        if(getKundenID($Kundennamen,$con)==0){
            fügeKundenHinzu($Kundennamen,$Bezahlort,$Email,$con);
        }
        $Kunde = getKundenID($Kundennamen,$con);
        
        /**Reservierung erstellt */
        changeSitzBelegung($Sitz,true,$con);
        $connect = mysqli_query($con, "INSERT INTO reservierung(KundenID,SitzplatzLabel) VALUES ('$Kunde','$Sitz');");
        if(!$connect){
            echo "Reservierung fehlgeschlagen\n";
            changeSitzBelegung($Sitz,false,$con);
        }else{
            echo "Reservierung hinzugefügt\n ";
        }
    }

    /**fügt Kundeneintrag hinzu */
    function fügeKundenHinzu($Kundennamen,$Bezahlort,$Email,$con){
        $connect = mysqli_query($con, "INSERT INTO kunde(Kundenname,Bezahlort,Email) VALUES ('$Kundennamen','$Bezahlort','$Email');");
        if(!$connect){
            echo "Kundenname könnte nicht eingetragen werden\n";
        }else{
            echo "Kundeneintrag erfolgreich\n";
        }
    }

    /**gibt die KundenID zurück, welcher den Namen $Kundennamen hat */
    function getKundenID($Kundennamen,$con){
        $connect = mysqli_query($con, "SELECT KundenID FROM kunde WHERE Kundenname='$Kundennamen';");
        if(!$connect){
            echo "Kundenname könnte nicht eingetragen werden\n";
            return 0;
        }else{
            return mysqli_fetch_array($connect)[0];
        }
    }

    /**gibt basierend auf die jeweiligen Suchdaten alle Reservierungsdaten zurück */
    function getReservierung($auswahl,$inhalt,$con){
        switch ($auswahl){
            case "Name":
                $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzLabel as Sitzplatz,
                                            reservierung.ReservierungsID, kunde.Bezahlort, kunde.Gezahlt from reservierung
                                            join kunde on reservierung.KundenID = kunde.KundenID
                                            join sitzplatz on reservierung.SitzplatzLabel = sitzplatz.SitzplatzLabel
                                            WHERE kunde.Kundenname='$inhalt';");
                if(!$connect){
                    return "Daten könnten nicht geladen werden\n";
                }else{
                    return getStringFromReservierungsdaten($connect);
                }
                break;
            case "Sitz":
                $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzLabel as Sitzplatz,
                                            reservierung.ReservierungsID, kunde.Bezahlort, kunde.Gezahlt from reservierung
                                            join kunde on reservierung.KundenID = kunde.KundenID
                                            join sitzplatz on reservierung.SitzplatzLabel = sitzplatz.SitzplatzLabel
                                            WHERE sitzplatz.SitzplatzLabel='$inhalt';");
                if(!$connect){
                    return "Daten könnten nicht geladen werden\n";
                }else{
                    return getStringFromReservierungsdaten($connect);
                }
                break;
            default:
                $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzLabel as Sitzplatz,
                                        reservierung.ReservierungsID, kunde.Bezahlort, kunde.Gezahlt from reservierung
                                        join kunde on reservierung.KundenID = kunde.KundenID
                                        join sitzplatz on reservierung.SitzplatzLabel = sitzplatz.SitzplatzLabel
                                        WHERE reservierung.ReservierungsLabel='$inhalt';");
                if(!$connect){
                    return "Daten könnten nicht geladen werden\n";
                }else{
                    return getStringFromReservierungsdaten($connect);
                }
        }
    }
    /**verarbeitet die gesuchten Reservierungsdaten in einem Text 
    */
    function getStringFromReservierungsdaten($connect){
        $result='';
        $rows = mysqli_fetch_all($connect, MYSQLI_ASSOC);
        foreach ($rows as $row){
            $result .= $row["Sitzplatz"] . ";";
        }
        foreach($rows as $row){
            $name = $row["Kundennamen"];
            $Reservierung = $row["ReservierungsID"];
            $Bezahlort = $row["Bezahlort"];
            $gezahlt = $row["Gezahlt"]?"Ja":"Nein";
            break;
        }
        return "Kunde: " . $name . "| Sitzplatz: " . $result . "| ReservierungsID: " . $Reservierung . "| Bezahlort: " . $Bezahlort . "| Gezahlt: " . $gezahlt . "\n";
    }

    /**erhalte den Sitzplatz mit der ReservierungsID */
    function getSitzplatz($Reservierung,$con){
        $connect = mysqli_query($con, "SELECT SitzplatzLabel FROM reservierung WHERE ReservierungsID='$Reservierung';");
        if(!$connect){
            echo "Sitz könnte nicht ermittelt werden\n";
            return null;
        }else{
            return mysqli_fetch_array($connect)[0];
        }
    }

    /**wechselt die Sitzbelegung */
    function changeSitzBelegung($SitzplatzID,$neueBelegung,$con){
        if($SitzplatzID!=null){
            $connect = mysqli_query($con, "UPDATE sitzplatz SET Belegt = '$neueBelegung' WHERE SitzplatzLabel='$SitzplatzID';");
            if(!$connect){
                echo "Fehler beim belegen\n";
            }
        }
        
    }

    /**löscht die Reservierung am Sitzplatz $SitzplatzID */
    function deleteReservierung($ReservierungsID,$con){
        $Sitz = getSitzplatz($ReservierungsID,$con);
        changeSitzBelegung($Sitz,false,$con);
        $connect = mysqli_query($con, "DELETE FROM reservierung WHERE ReservierungsID='$ReservierungsID';");
        if(!$connect){
            echo "Daten könnten nicht gelöscht werden\n";
            changeSitzBelegung($Sitz,true,$con);
        }else{
            echo "erfolgreich gelöscht\n";
        }
    }

    function getFreiePlätze($con){
        $connect = mysqli_query($con, "Select SitzplatzLabel FROM sitzplatz WHERE Belegt=false ORDER BY left(SitzplatzLabel,1), length(SitzplatzLabel), SitzplatzLabel;");
        if($connect){
            $rows = mysqli_fetch_all($connect, MYSQLI_ASSOC);
            foreach ($rows as $row){
                return $row["SitzplatzLabel"];
            }
        }else{
            return 0;
        }
    }

    function isBelegt($Sitz,$con){
        $connect = mysqli_query($con, "Select Belegt FROM sitzplatz WHERE SitzplatzLabel='$Sitz';");
        if(!$connect){
            echo "Belegt kann nicht überprüft werden";
        }else{
            return (mysqli_fetch_array($connect)[0]==1);
        }
    }

    function setBezahlung($Kunde,$value,$con){
        if(getKundenID($Kunde,$con)==0){
            return "Kundenname könnte nicht gefunden werden";
        }

        $connect = mysqli_query($con, "UPDATE kunde SET Gezahlt = $value WHERE Kundenname = '$Kunde';");
        if(!$connect){
            echo "Bazahlung kann nicht geupdated werden";
        }else{
            return "Bezahlung wurde geupdated";
        }
    }
?>