<?php

include('Config.php');

$conn = NULL;
$dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_SCHEMA');

try {
    /* Création d'un objet PDO */
    $conn = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'));
    /* Activer les exceptions sur les erreurs */
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie";
} catch (PDOException $e) {
    /* S'il y a une erreur, une exception est levée */
    echo "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}