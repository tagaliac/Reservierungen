<?php   
    /**import global variables */
    $DEBUG_MODUS = json_decode(file_get_contents(".\Globale_Variablen.json"),false)->DEBUG_MODUS;
?>
<html>
    <head>
    <meta charset="UTF-8">
        <title>Reservierungen</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src=".\JQuery.js"></script>
        <nav>
            <ul class="nav_links">
                <li><a href="index.php">Sitzplätze</a></li>
                <li><a href="Reservierungen.php">Reservierungen</a></li>
            </ul>
        </nav>
    </head>
    <body>
        <!-- Zum Erstellen aller Sitzplätze-->
        <label for="anzahlSitzreihen">Anzahl der Reihen an Sitzplätzen:</label>
        <input type="number" id="anzahlSitzreihen" value="3"></br>
        <label for="SitzeProReihe">Anzahl der Sitzplätze pro Reihe:</label>
        <input type="number" id="SitzeProReihe" value="5"></br>
        <button class="submit" onclick="setSitzplätze()">
            setze Sitze
        </button>

        <!-- übersicht der Sitzplätze-->
        <button class="submit" onclick="displaySitze(<?php echo $DEBUG_MODUS?>)">
            Aufzeigen
        </button>
        <p id="übersichtSitze">

        </p>

        <!-- script-->
        <script>
            /**definiert die Anzahl an Sitzplätzen */
            function setSitzplätze(){
                let Sitzreihen = document.getElementById('anzahlSitzreihen').value;
                let LängeDerSitzreihen = document.getElementById('SitzeProReihe').value;
                setSitzeDB(Sitzreihen, LängeDerSitzreihen);
            }
            /**definiert die Anzahl an Sitzplätzenin der Datenbank */
            function setSitzeDB(Sitzplätze, Länge){
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:" ",Sitzreihe:Sitzplätze,Laenge:Länge},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }

            /**Stellt die Sitze und deren Belegungen grafisch dar
             * debug=false:die Plätze werden in Bildern angezeigt
             * debug=true:die Belegungen werden als Text dargestellt
             */
            function displaySitze(debug){
                let Sitzreihen = document.getElementById('anzahlSitzreihen').value;
                let Länge = document.getElementById('SitzeProReihe').value;
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:"display",Sitzreihe:Sitzreihen,Laenge:Länge},
                    success: function(data){
                        console.log("->", data);
                        if(debug){
                            document.getElementById('übersichtSitze').innerHTML = getStringForDisplay(data,Sitzreihen,Länge);
                        }else{
                            document.getElementById('übersichtSitze').innerHTML = zeigeSitze(data,Sitzreihen,Länge);
                        }
                        
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }

            /**Erstellt den HTML String für die geschriebene Platzdarstellung */
            function getStringForDisplay(data,Sitzreihen,Länge){
                result = "";
                arrayInfo = data.split('|');
                console.log(arrayInfo.length);
                if(arrayInfo.length==(Sitzreihen*Länge)+1){
                    for(let i = 0;i<arrayInfo.length-1;i++){
                        if(arrayInfo[i]==='1'){
                            console.log(result);
                            result= result + " Sitzplatz an Reihe " + Math.floor(i/Länge) + " und an Stelle " + i%Länge + " begelgt<br>";
                        }else{
                            result= result + " Sitzplatz an Reihe " + Math.floor(i/Länge) + " und an Stelle " + i%Länge + " nicht begelgt<br>";
                        }
                    }
                    return result;
                }else{
                    return "Array könnte nicht verarbeitet werden<br>"
                }
            }

            /**Erstellt den HTML Code für die  grafische Darstellung der Plätze*/
            function zeigeSitze(data,Sitzreihen,Länge){
                result = "<table style='width:80%;'>";
                arrayInfo = data.split('|');
                OFFSETWIDTH= 5;
                console.log(arrayInfo.length)
                if(arrayInfo.length==(Sitzreihen*Länge)+1){
                    width=Math.floor(100/Länge)-OFFSETWIDTH;
                    for(let i=0;i<Sitzreihen;i++){
                        for(let j=0;j<Länge;j++){
                            if(arrayInfo[i*Länge+j]==='1'){
                                result= result + '<svg style="width:'+width+'%"><rect class="redRectangle" width="100%" height="60"/></svg>';
                            }else{
                                result= result + '<svg style="width:'+width+'%"><rect class="blueRectangle" width="100%" height="60"/></svg>';
                            }
                        }
                        result=result+"<br>";
                    }
                    result = result + "</table>";
                    return result;
                }else{
                    return "Sitze könnten nicht verarbeitet werden<br>"
                }
            }
        </script> 

    </body>
        
</html>