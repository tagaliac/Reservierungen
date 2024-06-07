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
        
        <form action=Funktionen\Bestaetigung.php method="post">
            KundenID: <input id="Name" type="text" name="KundenID"><br>
            <button type="Submit">Namen kriegen</button>
        </form>

        <p><?php echo $su['KundenID']; ?></p>
        <label for="getKundenID">KundenID:</label>
        <input type="text" id="getKundenID">
        <button class="submit" onclick="showKundenname(document.getElementById('getKundenID').innerHTML)">
            Der Name
        </button><br><br>
        <p id="output"></p>
        <h1>Sitzplätze</h1>

        <!-- Tabelle-->
        <table style="width: 80%;" id="Sitze">
            
        </table>

        
        <label for="anzahlSitzplätzeReihen">Anzahl der Reihen an Sitzplätzen:</label>
        <input type="number" id="anzahlSitzplätzeReihen" value="3"></br>
        <label for="SitzeProReihe">Anzahl der Sitzplätze pro Reihe:</label>
        <input type="number" id="SitzeProReihe" value="5"></br>
        <button class="submit" onclick="setSitzreihe()">
            setze Sitze
        </button>
        
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
                    data: {Sitzreihe:Sitzplätze,Laenge:Länge},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }
        </script> 

    </body>
        
</html>