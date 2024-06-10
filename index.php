<?php   
    

?>
<html>
    <head>
    <meta charset="UTF-8">
        <title>Reservierungen</title>
        <!-- style-->
        <!--<link rel="stylesheet" type="text/css" href="style.css">-->
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
        <label for="anzahlSitzplätzeReihen">Anzahl der Reihen an Sitzplätzen:</label>
        <input type="number" id="anzahlSitzplätzeReihen" value="3"></br>
        <label for="SitzeProReihe">Anzahl der Sitzplätze pro Reihe:</label>
        <input type="number" id="SitzeProReihe" value="5"></br>
        <button class="submit" onclick="setSitzreihe()">
            setze Sitze
        </button>

        <!-- übersicht der Sitzplätze-->
        <button class="submit" onclick="displaySitze()">
            Aufzeigen
        </button>
        <p id="übersichtSitze">

        </p>
        
        <!-- script-->
        <script>
            function setSitzreihe(){
                let Sitzreihen = document.getElementById('anzahlSitzplätzeReihen').value;
                let Länge_der_Sitzreihen = document.getElementById('SitzeProReihe').value;
                setSitzeDB(Sitzreihen, Länge_der_Sitzreihen);
            }
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
            function displaySitze(){
                let Sitzreihen = document.getElementById('anzahlSitzplätzeReihen').value;
                let Länge = document.getElementById('SitzeProReihe').value;
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:"display",Sitzreihe:Sitzreihen,Laenge:Länge},
                    success: function(data){
                        console.log("->", data);
                        document.getElementById('übersichtSitze').innerHTML = getStringForDisplay(data,Sitzreihen,Länge);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }

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
                    return "Array könnte nicht verarbeitet werden"
                }
            }
        </script> 

    </body>
        
</html>