<html>
    <head>
        <meta charset="UTF-8">
        <title>Reservierungen</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src=".\JQuery.js"></script>
        <!-- <script type="text/JavaScript" src=".\Funktionen\SitzplanAuswahl.js"></script>-->
        <script type="text/JavaScript" src=".\Funktionen\Reservierungsscript.js"></script>
        <?php 
           /**import global variables */
            $DEBUG_MODUS = json_decode(file_get_contents(".\Globale_Variablen.json"),false)->DEBUG_MODUS;

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

        <!-- übersicht der Sitzplätze-->
        <h1 class="header" align="center">Reservierungen</h1>

        <table style="width:100%">
			<tr align="center">
				<td style="width:25%">Füge Reservierung hinzu</td>
				<td style="width:25%">Schau Reservierungen</td>
				<td style="width:25%">Lösche Reservierung</td>
                <td style="width:25%">Ist schon bezahlt</td>
			</tr>
			<tr align="center">
				<td style="width:25%">
                    <label for="setKundenname">Name des Kunden:</label>
                    <input class="input" type="text" id="setKundenname"></br>
                    <label for="email">Email:</label>
                    <input class="input" type="text" id="email"></br>
                    <label for="wahl">Wählen Sie Abholort aus:</label>
                    <select class="Auswahl" name="Auswahl" id="wahl">
                        <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
                        <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
                        <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
                    </select><br>
                    <a id="setSitze"><a>
                    <button class="submit" onclick="setReservierung()">
                        Bestätige Reservierung
                    </button>
				</td>
				<td style="width:25%">
                    <label for="wahl">Wähle Suchoption:</label>
                    <select class="Auswahl" name="Auswahl" id="suchOption">
                        <option value="Name">Name des Kunden</option>
                        <option value="Sitz">Sitzplatz</option>
                        <option value="Reservierung">ReservierungsID</option>
                    </select>
                    <label for="inhalt">Inhalt:</label>
                    <input class="input" type="text" id="inhalt"></br>
                    <button class="submit" onclick="getReservierung()">
                        Suche
                    </button>
				</td>
                <td style="width:25%">
                    <label for="delete">Lösche Reservierung mit ID:</label>
                    <input class="input" type="number" id="delete"></br>
                    <button class="submit" onclick="deleteReservierung(document.getElementById('delete').value)">
                        Lösche Reservierung
                    </button>
				</td>
                <td style="width:25%">
                    <label for="bezahltKundenname">Name des Kunden:</label>
                    <input class="input" type="text" id="bezahltKundenname"></br>
                    <label for="bezahlt">Ist schon Bezahlt:</label>
                    <select class="Auswahl" name="Auswahl" id="bezahlt">
                        <option value="ja">Ja</option>
                        <option value="nein">Nein</option>
                    </select><br>
                    <button class="submit" onclick="setBezahlung(document.getElementById('bezahltKundenname').value,document.getElementById('bezahlt').value)">
                        Setzt Bezahlung
                    </button>
				</td>
			</tr>
		</table>
        <!--globale variablen-->
        <p id="speicher" values=""></p>

        <!--Ausgabefeld-->
        <p id="output" style="width:100%" align="center"></p>
        <canvas id="bild" width="2000px" height = "2000px">

        </canvas>

        <footer>
            <hr class="horizontal line">
            <div align="center">
                <p s>Σύλλογος Ρομανία</p>
            </div>
        </footer>
    </body>
    <script type="text/JavaScript" src="./Funktionen/SitzplanAktuell.js"></script>
</html>