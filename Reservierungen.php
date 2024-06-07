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
				</td>
                <td style="width:33%">
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
                        console.log("aaa->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }
    </script>
</html>