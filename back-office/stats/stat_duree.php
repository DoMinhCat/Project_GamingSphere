<?php
header('Content-Type: application/json');

if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
require_once('../../include/database.php');
$category = $_GET['category'] ?? '';

$page = '';
$user = '';
$total = 0;
$site = 0;

$stmt = $bdd->query("SELECT sum(visit_duration.duration) as total_site from visit_duration;");
$stmt->execute();
$total_site = $stmt->fetch(PDO::FETCH_ASSOC);
$site = $total_site['total_site'];

$stmt = $bdd->query("SELECT sum(visit_duration.duration) as page_duree from visit_duration where category like ?;");
$stmt->execute(['%' . $category . '%']);
$page_duree = $stmt->fetch(PDO::FETCH_ASSOC);
$total = $category . ':' . $page_duree['page_duree'];

$stmt = $bdd->query("SELECT category, (sum(visit_duration.duration)) as sum from visit_duration group by category order by sum desc limit 1;");
$stmt->execute();
$most_visited = $stmt->fetch(PDO::FETCH_ASSOC);
$page_most = $most_visited['category'];

$stmt = $bdd->query("SELECT category, (sum(visit_duration.duration)) as sum from visit_duration group by category order by sum asc limit 1;");
$stmt->execute();
$least_visited = $stmt->fetch(PDO::FETCH_ASSOC);
$page_least = $least_visited['category'];

$page = $page_most . '/' . $page_least;


$stmt = $bdd->query("SELECT email, sum(visit_duration.duration) as sum from visit_duration join utilisateurs on id_utilisateur=utilisateurs.id_utilisateurs  group by email order by sum asc limit 1;");
$stmt->execute();
$least_user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_least = $least_user['email'];

$stmt = $bdd->query("SELECT email, sum(visit_duration.duration) as sum from visit_duration join utilisateurs on id_utilisateur=utilisateurs.id_utilisateurs  group by email order by sum desc limit 1;");
$stmt->execute();
$most_user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_most = $most_user['email'];

$user = $user_most . '/' . $user_least;
echo json_encode([
    'site' => $site, //
    'page' => $page, //
    'total' => $total, //
    'user' => $user
]);
