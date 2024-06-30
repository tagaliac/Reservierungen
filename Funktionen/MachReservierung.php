<?php
    /**import Database information*/
    require "DatabaseCon.php";

    /**Konstantemn */
    $GLOBAL_VALIABLE_FILE = "..\Globale_Variablen.json";

    /**import global variables */
    $GLOBAL_VALIABLE = json_decode(file_get_contents($GLOBAL_VALIABLE_FILE),false);
    $DEBUG_MODUS = $GLOBAL_VALIABLE->DEBUG_MODUS;
    $AusgewählteSprachen = $GLOBAL_VALIABLE->Sprache;

    /**import language settings */
    $DEUTSCH = json_decode(file_get_contents("..\Sprachen\DeutschAusgabe.json"),false);
    $GRIECHISCH = json_decode(file_get_contents("..\Sprachen\GriechischAusgabe.json"),false);

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
                    echo setReservierung($Kundennamen,$Sitz,$Bezahlort,$Email,$con);
                    break;
                case "get":
                    $auswahl = $_POST['Auswahl'];
                    $inhalt = htmlspecialchars($_POST['Inhalt']);
                    echo getReservierung($auswahl,$inhalt,$con);
                    break;
                case "getFreienSitz":
                    $anzahl = $_POST['anzahl'];
                    echo getFreiePlätze($con,$anzahl);
                    break;
                case "delete":
                    echo deleteReservierung($_POST['Inhalt'],$con);
                    break;
                case "Bezahlung":
                    echo setBezahlung($_POST["Kundenname"],$_POST['Inhalt'],$con);
                    break;
                case "Sprache":
                    $GLOBAL_VALIABLE->Sprache=$_POST["newLanguage"];
                    $newJsonString = json_encode($GLOBAL_VALIABLE);
                    file_put_contents($GLOBAL_VALIABLE_FILE, $newJsonString);
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

    function translate($word){
        global $DEUTSCH, $GRIECHISCH;
        switch($GLOBALS['AusgewählteSprachen']){
            case "Deutsch":
                return $DEUTSCH->$word;
                break;
            case "Griechisch":
                return $GRIECHISCH->$word;
                break;
            default:
                return "no translation";
        }
    }

    /**fügt einen Reservierungseintrag hinzu.
     * Wenn der Kunde nicht existiert wird dieser auch erstellt.
     */
    function setReservierung($Kundennamen, $Sitz, $Bezahlort,$Email, $con){
        /**Vorbedienungen */
        if($Kundennamen==null){
            return translate("NAME_MISS");
        }
        if($Bezahlort==null){
            return translate("LOC_MISS");
        }
        if($Sitz==null||$Sitz===""){
            $Sitz = getFreiePlätze($con);
        }
        if(isBelegt($Sitz,$con)){
            return translate("SEAT_NOTFREE");
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
            return translate("RES_FAIL");
            changeSitzBelegung($Sitz,false,$con);
        }else{
            return translate("RES_SUC");
        }
    }

    /**fügt Kundeneintrag hinzu */
    function fügeKundenHinzu($Kundennamen,$Bezahlort,$Email,$con){
        $connect = mysqli_query($con, "INSERT INTO kunde(Kundenname,Bezahlort,Email) VALUES ('$Kundennamen','$Bezahlort','$Email');");
        if(!$connect){
            echo "Kundenname könnte nicht eingetragen werden\n";
        }else{
            translate("CLI_SUC");
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
                    return translate("GET_FAIL");
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
                    return translate("GET_FAIL");
                }else{
                    return getStringFromReservierungsdaten($connect);
                }
                break;
            default:
                $connect = mysqli_query($con, "select kunde.Kundenname as Kundennamen, sitzplatz.SitzplatzLabel as Sitzplatz,
                                        reservierung.ReservierungsID, kunde.Bezahlort, kunde.Gezahlt from reservierung
                                        join kunde on reservierung.KundenID = kunde.KundenID
                                        join sitzplatz on reservierung.SitzplatzLabel = sitzplatz.SitzplatzLabel
                                        WHERE reservierung.ReservierungsID='$inhalt';");
                if(!$connect){
                    return translate("GET_FAIL");
                }else{
                    return getStringFromReservierungsdaten($connect);
                }
        }
    }
    /**verarbeitet die gesuchten Reservierungsdaten in einem Text 
    */
    function getStringFromReservierungsdaten($connect){
        $Sitzplatz='';
        $Reservierung='';
        $rows = mysqli_fetch_all($connect, MYSQLI_ASSOC);
        if(count($rows)==0){
            return translate('GET_FAIL');
        }
        foreach ($rows as $row){
            $Sitzplatz .= $row["Sitzplatz"] . ";";
            $Reservierung .= $row["ReservierungsID"] . ";";
        }
        foreach($rows as $row){
            $name = $row["Kundennamen"];
            $Bezahlort = $row["Bezahlort"];
            $gezahlt = $row["Gezahlt"]?translate("YES"):translate("NO");
            break;
        }
        $cli=translate("CLI");
        $seat = translate("SEAT");
        $res = translate("RES");
        $loc = translate("LOC");
        $pay = translate("PAY");
        return $cli . $name . "| " . $seat . $Sitzplatz . "| " . $res . $Reservierung . "| " . $loc . $Bezahlort . "| " . $pay . $gezahlt . "\n"; 
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
            return translate("DEL_SUC");
        }
    }

    /**gibt den nächsten Freien Sitzplatz zurück */
    function getFreiePlätze($con,$anzahl){
        $connect = mysqli_query($con, "Select SitzplatzLabel FROM sitzplatz WHERE Belegt=false ORDER BY left(SitzplatzLabel,1), length(SitzplatzLabel), SitzplatzLabel LIMIT $anzahl;");
        $result = "";
        if($connect){
            $rows = mysqli_fetch_all($connect, MYSQLI_ASSOC);
            foreach ($rows as $row){
                if($result===""){
                    $result .= $row["SitzplatzLabel"];
                }else{
                    $result .= "|" . $row["SitzplatzLabel"];
                }
            }
        }else{
            return 0;
        }
        return $result;
    }

    /**Überprüft ob der Sitz belegt ist */
    function isBelegt($Sitz,$con){
        $connect = mysqli_query($con, "Select Belegt FROM sitzplatz WHERE SitzplatzLabel='$Sitz';");
        if(!$connect){
            echo "Belegt kann nicht überprüft werden";
        }else{
            return (mysqli_fetch_array($connect)[0]==1);
        }
    }

    /**Setzt den Bazahlwert bei Kunden */
    function setBezahlung($Kunde,$value,$con){
        if(getKundenID($Kunde,$con)==0){
            return translate("PAY_MISS");
        }

        $connect = mysqli_query($con, "UPDATE kunde SET Gezahlt = $value WHERE Kundenname = '$Kunde';");
        if(!$connect){
            return translate("PAY_FAIL");
        }else{
            return translate("PAY_SUC");
        }
    }
?>