<?php
    // Handling logout
    session_start();
    session_unset();
    session_destroy();

    // Redirect to main page
    header('Location: index.php');
    exit();
