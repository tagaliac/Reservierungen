<?php   
    define('host', 'localhost');
    define("user", "AdminReservierung");
    define("pass","Romania1234");
    define("db","Sitzordnung");

    $con = mysqli_connect(host, user, pass, db);
    if(!$con){
        echo "keine Verbindung zur Datenbank";
    }else{
        $suchet = mysqli_query($con, "SELECT * FROM kunde");
        if(!$suchet){
            echo "nichts laden";
        }
        else{
            $su = mysqli_fetch_array($suchet);
        }
    }

?>
<html>
    <head>
    <meta charset="UTF-8">
        <title>Reservierungen</title>
        <!-- style-->
        <!--<link rel="stylesheet" type="text/css" href="style.css">-->
    </head>
    <body>
        

        <form action=Bestätigung.php method="post">
            KundenID: <input id="Name" type="text" name="KundenID"><br>
            <button type="Submit">Namen kriegen</button>
        </form>

        <p><?php echo $su['KundenID']; ?></p>
        <label for="getKundenID">KundenID:</label>
        <input type="text" id="getKundenID">
        <button class="submit" onclick="showKundenname(document.getElementById('getKundenID').innerHTML)">
            Der Name
        </button><br><br>
        <p id="output"></p>
        <h1>Sitzplätze</h1>
        <table style="width: 80%;" id="Sitze">
        
        </table>

        
    </body>
</html>