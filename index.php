<?php   
    require ".\Bestaetigung.php";

    //define('host', 'localhost');
    //define("user", "AdminReservierung");
    //define("pass","Romania1234");
    //define("db","Sitzordnung");

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
        <script src=".\JQuery.js"></script>
    </head>
    <body>
        
        <form action=Bestaetigung.php method="post">
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

        <!-- Tabelle-->
        <table style="width: 80%;" id="Sitze">
            
        </table>

        <button class="submit" onclick="setSitzreihe()">
            SitzeTest
        </button>

        

    </body>
    <!-- script-->
    <script>
            function setSitzreihe(){
                let Sitzreihen = 2
                let Länge_der_Sitzreihen = 7
                setSitzeDB(Sitzreihen, Länge_der_Sitzreihen);
            }
            function setSitzeDB(Sitzplätze, Länge){
                $.ajax({
                    url: "Sitzplanerstellung.php",
                    type: "POST",
                    data: {Sitzreihe:Sitzplätze,Laenge:Länge},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }
</script>
        </script> 
</html>