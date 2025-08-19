<?php
$host = "localhost";
$dbname = "db8n6nmto4i7oj";
$username = "ux7oqwxcx8vsf";
$password = "v3hxvatbehaf";
 
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
