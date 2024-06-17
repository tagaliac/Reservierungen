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
        <h1>Reservierungen</h1>

        <label for="wahl">Wählen Sie Abholort aus:</label>
        <select name="Auswahl" id="wahl">
            <option value="Cannstatt">Mellyriton Bad Cannstatt</option>
            <option value="Waiblingen">Boulevard Café Bar Waiblingen</option>
            <option value="Filderstadt">Hotel Sielminger Hof, Filderstadt</option>
        </select><br>
        <label for="setKundenname">Name:</label>
        <input type="text" id="setKundenname"></br>
        <a id="setSitze"></a><br>
        <button class="submit" onclick="setReservierung()">
            Bestätige Reservierung
        </button>
        <!--globale variablen-->
        <p id="speicher" values=""></p>

        <!--Ausgabefeld-->
        <p id="output"></p>
        <canvas id="bild" width="2000px" height = "2000px">

        </canvas>
    </body>
    <script type="text/JavaScript" src="./Funktionen/SitzplanAktuell.js"></script>
    
</html>