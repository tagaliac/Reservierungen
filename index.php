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
        <!-- <script type="text/JavaScript" src=".\Funktionen\SitzplanAuswahl.js"></script>-->
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
    <body onload="displaySitze(<?php echo $DEBUG_MODUS?>)">
        <!-- Zum Erstellen aller Sitzplätze-->
        <label for="anzahlTischreihen">Anzahl der Tischreihen:</label>
        <input type="number" id="anzahlTischreihen" value="3"></br>
        <label for="TischeProReihe">Anzahl der Tische pro Reihe:</label>
        <input type="number" id="TischeProReihe" value="5"></br>
        <label for="SitzeProTisch">Anzahl der Sitze pro Tisch:</label>
        <input type="number" id="SitzeProTisch" value="4"></br>
        <button class="submit" onclick="setSitzplätze(document.getElementById('anzahlTischreihen').value,
                                                    document.getElementById('TischeProReihe').value,
                                                    document.getElementById('SitzeProTisch').value)">
            setze Sitze
        </button>

        <!-- übersicht der Sitzplätze-->
        <button class="submit" onclick="displaySitze(<?php echo $DEBUG_MODUS;?>)">
            Aufzeigen
        </button>
        <h1>Reservierungen</h1>

        <table style="width:80%">
			<tr align="center">
				<td style="width:25%">Füge Reservierung hinzu</td>
				<td style="width:25%">Schau Reservierungen</td>
				<td style="width:25%">Lösche Reservierung</td>
                <td style="width:25%">Bezahlort</td>
			</tr>
			<tr style="font-family: cursive; vertical-align:top" align="center">
				<td style="width:25%">
                    <label for="setKundenname">Name des Kunden:</label>
                    <input type="text" id="setKundenname"></br>
                    <a id="setSitze"><a>
                    <button class="submit" onclick="setReservierung()">
                        Bestätige Reservierung
                    </button>
				</td>
				<td style="width:25%">
                    <label for="wahl">Wähle Suchoption:</label>
                    <select name="Auswahl" id="suchOption">
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
                <td style="width:25%">
                    <label for="delete">Lösche Reservierung mit ID:</label>
                    <input type="number" id="delete"></br>
                    <button class="submit" onclick="deleteReservierung()">
                        Lösche Reservierung
                    </button>
				</td>
                <td style="width:25%">
                    <label for="wahl">Wählen Sie Abholort aus:</label>
                    <select name="Auswahl" id="wahl">
                        <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
                        <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
                        <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
                    </select>
				</td>
			</tr>
		</table>
        <!--globale variablen-->
        <p id="speicher" values=""></p>

        <!--Ausgabefeld-->
        <p id="output"></p>
        <canvas id="bild" width="2000px" height = "2000px">

        </canvas>
    </body>
    <script type="text/JavaScript" src="./Funktionen/SitzplanAktuell.js"></script>
</html>