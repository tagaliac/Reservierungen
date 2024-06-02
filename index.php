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

    </head>
    <body>
        <p><?php echo $su['Name']; ?></p>
    </body>
</html>