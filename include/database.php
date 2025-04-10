<?php
try {
    if ($_SERVER['HTTP_HOST'] === 'localhost') {
        // Connexion en local
        $bdd = new PDO("mysql:host=localhost;port=3306;dbname=gamingsphère;charset=utf8", "root", "root");
    } else {
        // Connexion sur le serveur (OVH)
        $bdd = new PDO("mysql:host=localhost;port=3306;dbname=gamingsphere;charset=utf8", "root", "odissey");
    }
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>