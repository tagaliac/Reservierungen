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
        <!-- übersicht der Sitzplätze-->
        <h1 class="header" align="center">Reservierungen</h1>

        <table style="width:100%">
            <tr align="center">
                <td style="width:25%">
                    <label for="setKundenname">Geben Sie Ihren Namen an:</label>
                </td>
                <td style="width:25%">
                    <label for="email">Geben Sie Ihre Email-Adresse an:</label>
                </td>
                <td style="width:25%">
                    <label for="wahl">Wählen Sie Abholort aus:</label>
                </td>
                <td style="width:25%">
                    <a id="setSitze">Wählen Sie Ihre Sitze aus. Wenn kein Sitz gewählt </a><br>
                </td>
            </tr>
            <tr align="center">
                <td style="width:25%">
                    <input class="input" type="text" id="setKundenname"></br>
                </td>
                <td style="width:25%">
                    <input class="input" type="text" id="email"></br>
                </td>
                <td style="width:25%">
                    <select class="Auswahl" name="Auswahl" id="wahl">
                        <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
                        <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
                        <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
                    </select><br>
                </td>
                <td style="width:25%">
                    <button class="submit" onclick="setReservierung()">
                        Bestätige Reservierung
                    </button>
                </td>
            </tr>
        </table>
 
        <!--globale variablen-->
        <p id="speicher" values=""></p>

        <!--Ausgabefeld-->
        <p id="output" style="width:80%"></p>
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