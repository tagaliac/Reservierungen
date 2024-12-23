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
                            <li><a href="Adminromania.php">Administrativ</a></li>
                            <li><a href="index.php">Kundenplattform</a></li>
                        </ul>
                    </nav>';
            }
        ?>
    </head>
    <body onload="initKundenSprache();LoadSitzplan();">
        <!-- Der Header zur Spracheinstellungen -->
        <header>
            <nav>
                <ul align="right" class="nav_links">
                    <li><a onclick="changeLanguage('Griechisch');initKundenSprache('Griechisch');">ΕΛ</a></li>
                    <li><a onclick="changeLanguage('Deutsch');initKundenSprache('Deutsch');">DE</a></li>
                </ul>
            </nav>
            <hr class="horizontal line">
        </header>

        <!-- übersicht der Sitzplätze-->
        <h1 class="header" align="center" id="TITLE">Reservierungen</h1>

        <table style="width:100%">
            <tr align="center">
                <td style="width:20%">
                    <label for="setKundenname" id="SET_NAME">Geben Sie Ihren Namen an:</label>
                </td>
                <td style="width:20%">
                    <label for="email" id="SET_MAIL">Geben Sie Ihre Email-Adresse an:</label>
                </td>
                <td style="width:20%">
                    <label for="wahl" id="SET_LOC">Wählen Sie Abholort aus:</label>
                </td>
                <td style="width:40%">
                    <a id="SET_SEAT">Wählen Sie Ihre Sitze aus.</a><br>
                </td>
            </tr>
            <tr align="center">
                <td style="width:20%">
                    <input class="input" type="text" id="setKundenname"></br>
                </td>
                <td style="width:20%">
                    <input class="input" type="text" id="email"></br>
                    <label for="BestatigeEmail" id="SET_MAIL_CONF">Bestätigen Sie Ihre Email:</label>
                    <input class="input" type="text" id="BestatigeEmail"></br>
                </td>
                <td style="width:20%">
                    <select class="Auswahl" name="Auswahl" id="wahl">
                        <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
                        <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
                        <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
                    </select><br>
                </td>
                <td style="width:40%">
                    <label for="anzahlSitze" id="AMOUNTH_SEAT">Wie viele Sitze werden automatisch ausgewählt?:</label>
                    <input class="input" type="number" id="anzahlSitze" value="1" min="1"></br>
                    <button class="submit" onclick="SetReservierungAndReload()" id="RES_SUB">
                        Bestätige Reservierung
                    </button>
                </td>
            </tr>
        </table>
        
        <!--globale variablen-->
        <p id="speicher" values=""></p>

        <!--Ausgabefeld-->
        <p align="center" id="setSitze"><p><br>
        <p align="center" id="output" style="width:80%"></p>
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