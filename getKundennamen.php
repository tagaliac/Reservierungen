<html>
    <body>
                document.getElementById('output').innerHTML = <?php 
                    $result = mysqli_query($con, 'SELECT * FROM kunde WHERE KundenID = '+$_Post['KundenID']);
                    if(!$result){
                        echo "nichts laden";
                    }
                    else{
                        return mysqli_fetch_array($result);
                    }
                ?>
    </body>
</html>