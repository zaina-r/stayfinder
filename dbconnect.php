<?php

    $host_name = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "stayfinder";

    $connect = mysqli_connect($host_name,$db_username,$db_password,$db_name);

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }else{
        
    }

?>