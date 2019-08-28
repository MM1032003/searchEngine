<?php 
    ob_start();
    $dsn        = "mysql:host=localhost;dbname=search";
    $username   = "root";
    $password   = "1032003";
    $options    = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                );
    try {
        $dbcon = new PDO($dsn, $username, $password, $options);
        $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection Failed : " . $e->getMessage();
    }
    ob_end_flush();
?>