<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "bjprojekat";

    $conn = new mysqli($servername, $username, $password, $database);
    if($conn->connect_error)
    {
        die("Neuspesna konekcija: " . $conn->connect_error);
    }
?>
