<?php
    //var_dump($_SERVER["REQUEST_METHOD"])
    define('host', 'localhost');
    define("user", "AdminReservierung");
    define("pass","Romania1234");
    define("db","Sitzordnung");

    function connectToDB(){
        $con = mysqli_connect(host, user, pass, db);
        if(!$con){
            echo "keine Verbindung zur Datenbank";
        }
        return $con;
    }

        
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        
        if(connectToDB()){
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

    </body>
</html>