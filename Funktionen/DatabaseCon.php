<?php
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
?>