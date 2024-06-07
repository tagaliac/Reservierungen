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
        <h1>Reservierungen</h1>

        <table style="width:80%">
			<tr align="center">
				<td style="width:33%">Füge Reservierung hinzu</td>
				<td style="width:33%">Schau Reservierungen</td>
				<td style="width:33%">Lösche Reservierung</td>
			</tr>
			<tr style="font-family: cursive; vertical-align:top" align="center">
				<td style="width:33%">
                    <label for="setKundenname">Name des Kunden:</label>
                    <input type="text" id="setKundenname"></br>
                    <label for="setSitz">Sitznummer:</label>
                    <input type="number" id="setSitz"></br>
                    <button class="submit" onclick="setReservierung()">
                        Bestätige Reservierung
                    </button>
				</td>
				<td style="width:33%">
                    <label for="wahl">Wähle Suchoption:</label>
                    <select name="Auswahl" id="wahl">
                        <option value="Name">Name des Kunden</option>
                        <option value="Sitz">Sitzplatz</option>
                        <option value="Reservierung">ReservierungsID</option>
                    </select>
                    <label for="inhalt">Inhalt:</label>
                    <input type="text" id="inhalt"></br>
                    <button class="submit" onclick="getReservierung()">
                        Suche
                    </button>
				</td>
                <td style="width:33%">
                <label for="delete">Lösche Reservierung mit ID:</label>
                    <input type="number" id="delete"></br>
                    <button class="submit" onclick="deleteReservierung()">
                        Lösche Reservierung
                    </button>
				</td>
			</tr>
		</table>

    </body>
    
    <!-- script-->
    <script>
            function setReservierung(){
                let Kundenname = document.getElementById('setKundenname').value;
                let Sitz = document.getElementById('setSitz').value;
                setReservierungDB(Kundenname, Sitz);
            }
            function setReservierungDB(Kundenname, Sitz){
                $.ajax({
                    url: "Funktionen/MachReservierung.php",
                    type: "POST",
                    data: {Action:"set",Kundenname:Kundenname,Sitz:Sitz},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }

            function getReservierung(){
                let Auswahl = document.getElementById('wahl').value;
                let Inhalt = document.getElementById('inhalt').value;
                getReservierungDB(Auswahl,Inhalt);
            }
            function getReservierungDB(Auswahl, Inhalt){
                $.ajax({
                    url: "Funktionen/MachReservierung.php",
                    type: "POST",
                    data: {Action:"get",Auswahl:Auswahl,Inhalt:Inhalt},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }

            function deleteReservierung(){
                $.ajax({
                    url: "Funktionen/MachReservierung.php",
                    type: "POST",
                    data: {Action:"delete",Inhalt:document.getElementById('delete').value},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }
    </script>
</html>