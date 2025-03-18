<?php
try {
    $bdd = new PDO("mysql:host=localhost;port=3307;dbname=gamingsphère;charset=utf8", "root", "root");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
