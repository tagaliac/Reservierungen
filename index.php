<?php   
    define('host', 'localhost');
    define("user", "AdminReservierung");
    define("pass","Romania1234");
    define("db","sitzordnung");

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
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        
        <script>
            function showKundenname(KundenID){
                let request = new XMLHttpRequest();
                request.open('post', 'index.php', true);
                request.send(KundenID)
                document.getElementById('output').innerHTML = <?php echo getKundenname($_POST['KundenID']) ?>
            }
        </script>
        

        <form action="getKundennamen.php" method="post">
            Name: <input type="text" name="name" /><br />
            <input type="Submit" value="Absenden" />
        </form>

        <p><?php echo $su['Name']; ?></p>
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