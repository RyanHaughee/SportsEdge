<?php

try {
    // Database connection settings
    $host = '127.0.0.1';
    $database = 'sportsedge_local';
    $username = 'root'; // Replace with your MySQL username
    $password = 'Kwipped#2014'; // Replace with your MySQL password

    // Create connection
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Successfully connected to the database!";
} catch (PDOException $e) {
    die("Could not connect to the database. Error: " . $e->getMessage());
}
