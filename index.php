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
        <script type="text/JavaScript" src=".\Funktionen\SitzplanAuswahl.js"></script>
        <script type="text/JavaScript" src=".\Funktionen\Reservierungsscript.js"></script>
        <?php 
            if($DEBUG_MODUS){
                echo '<nav>
                        <ul class="nav_links">
                            <li><a href="index.php">Administrativ</a></li>
                            <li><a href="KundenIndex.php">Kundenplattform</a></li>
                        </ul>
                    </nav>';
            }
        ?>
    </head>
    <body>
        <!-- Zum Erstellen aller Sitzplätze-->
        <label for="anzahlSitzreihen">Anzahl der Reihen an Sitzplätzen:</label>
        <input type="number" id="anzahlSitzreihen" value="3"></br>
        <label for="SitzeProReihe">Anzahl der Sitzplätze pro Reihe:</label>
        <input type="number" id="SitzeProReihe" value="5"></br>
        <button class="submit" onclick="setSitzplätze(document.getElementById('anzahlSitzreihen').value,document.getElementById('SitzeProReihe').value)">
            setze Sitze
        </button>

        <!-- übersicht der Sitzplätze-->
        <button class="submit" onclick="displaySitze(<?php echo $DEBUG_MODUS?>,
                                                    document.getElementById('anzahlSitzreihen').value,
                                                    document.getElementById('SitzeProReihe').value)">
            Aufzeigen
        </button>
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
        <!--Ausgabefeld-->
        <p id="output"></p>
        <p id="übersichtSitze">

        </p>
    </body>
</html>