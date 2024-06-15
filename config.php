<?php
// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_stock';
$username = 'root';
$password = ''; 

// Créer une connexion à la base de données
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
