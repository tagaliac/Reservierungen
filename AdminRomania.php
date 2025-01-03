<html>
    <head>
        <meta charset="UTF-8">
        <title>Reservierungen</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <link rel="icon" type="image/png" href="img/Pontos_pouli.png">
        <script src=".\JQuery.js"></script>
        <script type="text/JavaScript" src=".\Funktionen\Oberfläche.js"></script>
        <script type="text/JavaScript" src=".\Funktionen\Reservierungsscript.js"></script>
        <?php 
            /**import global variables */
            $DEBUG_MODUS = json_decode(file_get_contents(".\Globale_Variablen.json"),false)->DEBUG_MODUS;

            if($DEBUG_MODUS){
                echo '<nav>
                        <ul class="nav_links">
                            <li><a href="AdminRomania.php">Administrativ</a></li>
                            <li><a href="index.php">Kundenplattform</a></li>
                        </ul>
                    </nav>';
            }
        ?>
    </head>
    <body onload="initSprache();LoadSitzplan();">
        <!-- Der Header zur Spracheinstellungen -->
        <header>
            <nav>
                <ul align="right" class="nav_links">
                    <li><a onclick="changeLanguage('Griechisch');initSprache('Griechisch');">ΕΛ</a></li>
                    <li><a onclick="changeLanguage('Deutsch');initSprache('Deutsch');">DE</a></li>
                </ul>
            </nav>
            <hr class="horizontal line">
        </header>
            
        <!-- übersicht der Sitzplätze-->
        <h1 class="header" align="center" id="TITLE">Reservierungen</h1>

        <table style="width:100%">
			<tr align="center">
				<td style="width:25%" id="ADD_RES_TITLE">Füge Reservierung hinzu</td>
				<td style="width:25%" id="ADD_GET_TITLE">Schau Reservierungen</td>
				<td style="width:25%" id="ADD_DEL_TITLE">Lösche Reservierung</td>
                <td style="width:25%" id="ADD_PAY_TITLE">Ist schon bezahlt</td>
			</tr>
			<tr align="center">
				<td style="width:25%">
                    <label for="setKundenname" id="RES_NAME">Name des Kunden:</label>
                    <input class="input" type="text" id="setKundenname"></br>
                    <label for="email" id="RES_EMAIL">Email:</label>
                    <input class="input" type="text" id="email"></br>
                    <label for="BestatigeEmail" id="RES_CONF_EMAIL">Email bestätigen:</label>
                    <input class="input" type="text" id="BestatigeEmail"></br>
                    <label for="Telefon" id="RES_TEL">Telefonnummer:</label>
                    <input class="input" type="text" id="telefon"></br>
                    <label for="wahl" id="RES_LOC">Wählen Sie Abholort aus:</label>
                    <select class="Auswahl" name="Auswahl" id="wahl">
                        <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
                        <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
                        <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
                    </select><br>
                    <label for="anzahlSitze", id="RES_AUTO_SEAT">Sitze automatisch gewählt?:</label>
                    <input class="input" type="number" id="anzahlSitze" value="1" min="1"></br></br>
                    <button class="submit" id="RES_SUMMIT" onclick="SetReservierungAndReload()">
                        Bestätige Reservierung
                    </button>
                    <br><a id="setSitze"><a>
				</td>
				<td style="width:25%">
                    <label for="wahl" id="GET_LOC">Wähle Suchoption:</label>
                    <select class="Auswahl" name="Auswahl" id="suchOption">
                        <option value="Name" id="GET_LOC_NAME">Name des Kunden</option>
                        <option value="Sitz" id="GET_LOC_SEAT">Sitzplatz</option>
                        <option value="Reservierung" id="GET_LOC_RES">ReservierungsID</option>
                    </select>
                    <label for="inhalt" id="GET_VAL">Inhalt:</label>
                    <input class="input" type="text" id="inhalt"></br>
                    <button class="submit" id="GET_SUMMIT" onclick="GetReservierung()">
                        Suche
                    </button>
				</td>
                <td style="width:25%">
                    <label for="delete" id="DEL_RES">Lösche Reservierung mit ID:</label>
                    <input class="input" type="number" id="delete"></br>
                    <button class="submit" id="DEL_SUMMIT"
                        onclick="DeleteReservierung_passwort(document.getElementById('delete').value,true,true)">
                        Lösche Reservierung
                    </button>
				</td>
                <td style="width:25%">
                    <label for="bezahltKundenname" id="PAY_NAME">Name des Kunden:</label>
                    <input class="input" type="text" id="bezahltKundenname"></br>
                    <label for="bezahlt" id="PAY_VAL">Ist schon Bezahlt:</label>
                    <select class="Auswahl" name="Auswahl" id="bezahlt">
                        <option value="ja" id="PAY_VAL_YES">Ja</option>
                        <option value="nein" id="PAY_VAL_NO">Nein</option>
                    </select><br>
                    <button class="submit" id="PAY_SUMMIT"
                         onclick="SetBezahlung_passwort(document.getElementById('bezahltKundenname').value,document.getElementById('bezahlt').value)">
                        Setzt Bezahlung
                    </button>
				</td>
			</tr>
		</table>
        <p id="passwort">
            <label for="pass" id="PASS_DES">Passwort:</label>
            <input class="input" type="text" id="pass"></br>
        </p>
        <!--globale variablen-->
        <p id="speicher" values=""></p>

        <!--Ausgabefeld-->
        <p align="center" id="output" style="width:80%"></p>
        <canvas id="bild" width="2000px" height = "2000px">

        </canvas>

        <footer>
            <hr class="horizontal line">
            <div align="center">
                <!-- Kontaktdaten eingeben!!! -->
                <p s>Σύλλογος Ρομανία</p>
            </div>
        </footer>
    </body>
    <script type="text/JavaScript" src="./Funktionen/SitzplanAktuell.js"></script>
</html>