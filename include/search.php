<?php
$this_page = basename($_SERVER['PHP_SELF']);
if ($this_page == 'index.php') {
    include('include/database.php');
} else include('../include/database.php');

$search_results = [];
if (isset($_GET['search'])) {
    $search_term = "%" . $_GET['search'] . "%"; 
    $sql = "SELECT * FROM utilisateurs WHERE pseudo LIKE :search_term";

    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':search_term', $search_term, PDO::PARAM_STR);
    $stmt->execute();
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
