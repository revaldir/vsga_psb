<?php
    // Defines db connection
    $host = 'localhost';
    $uname = 'root';
    $pass = 'root';
    $db_name = 'vsga_psb';

    // Create new connection
    $conn = mysqli_connect($host, $uname, $pass, $db_name);

    if (!$conn) {
        die('Could not connect to database! Error : ' . mysqli_connect_error());
    }
