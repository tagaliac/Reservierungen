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
    <body onload="displaySitze(<?php echo $DEBUG_MODUS?>)">
        <!-- übersicht der Sitzplätze-->
        <button class="submit" onclick="displaySitze(<?php echo $DEBUG_MODUS?>)">
            Aufzeigen
        </button>
        <h1>Reservierungen</h1>

        <label for="wahl">Wählen Sie Abholort aus:</label>
        <select name="Auswahl" id="wahl">
            <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
            <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
            <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
        </select>
        <label for="inhalt">Name:</label>
        <input type="text" id="inhalt"></br>
        <button class="submit" onclick="getReservierung()">
            Bestätige Reservierung
        </button>

        <!--Ausgabefeld-->
        <p id="output"></p>
        <p id="übersichtSitze">
            
        </p>
    </body>
    
    
</html>