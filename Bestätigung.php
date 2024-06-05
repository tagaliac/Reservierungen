<?php
    //var_dump($_SERVER["REQUEST_METHOD"])
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        define('host', 'localhost');
        define("user", "AdminReservierung");
        define("pass","Romania1234");
        define("db","Sitzordnung");

        $con = mysqli_connect(host, user, pass, db);
        if(!$con){
            echo "keine Verbindung zur Datenbank";
        }else{
            $KundenID = htmlspecialchars($_POST['KundenID']);
            $suchet = mysqli_query($con, "SELECT * FROM kunde WHERE KundenID = '$KundenID';");
            if(!$suchet){
                echo "Name könnte nicht gefunden werden";
            }
            else{
                $su = mysqli_fetch_array($suchet);
            }
        }
        
    }
?>

<html>
    <head>

    </head>
    <body>
            <?php echo $su['Kundenname'] ?>
    </body>
</html>