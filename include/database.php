<?php
try {
    if ($_SERVER['HTTP_HOST'] === 'localhost') {
        $bdd = new PDO("mysql:host=localhost;port=3306;dbname=gamingsphere;charset=utf8", "root", "root");
    } else {
        $bdd = new PDO("mysql:host=localhost;port=3306;dbname=gamingsphere;charset=utf8", "root", "odissey");
    }
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
