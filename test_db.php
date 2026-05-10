<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    echo "Connected successfully";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS travel_haji");
    echo "\nDatabase 'travel_haji' verified/created";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
