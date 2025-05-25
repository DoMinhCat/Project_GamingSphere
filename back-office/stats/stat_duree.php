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

$stmt = $bdd->prepare("SELECT sum(visit_duration.duration) as total_site from visit_duration;");
$stmt->execute();
$total_site = $stmt->fetch(PDO::FETCH_ASSOC);
$site = round(intval($total_site['total_site'] / 60), 2);

$stmt = $bdd->prepare("SELECT sum(visit_duration.duration) as page_duree from visit_duration where category=?;");
$stmt->execute([$category]);
$page_duree = $stmt->fetch(PDO::FETCH_ASSOC);
$page_duree_value = $page_duree['page_duree'] ?? 0;
$total = $category . ': ' . round(intval($page_duree_value), 2);

$stmt = $bdd->prepare("SELECT category, (sum(visit_duration.duration)) as sum from visit_duration group by category order by sum desc limit 1;");
$stmt->execute();
$most_visited = $stmt->fetch(PDO::FETCH_ASSOC);
$page_most = $most_visited['category'];

$stmt = $bdd->prepare("SELECT category, (sum(visit_duration.duration)) as sum from visit_duration group by category order by sum asc limit 1;");
$stmt->execute();
$least_visited = $stmt->fetch(PDO::FETCH_ASSOC);
$page_least = $least_visited['category'];

$page = $page_most . '/' . $page_least;


$stmt = $bdd->prepare("SELECT email, sum(visit_duration.duration) as sum from visit_duration join utilisateurs on id_utilisateur=utilisateurs.id_utilisateurs  group by email order by sum asc limit 1;");
$stmt->execute();
$least_user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_least = $least_user['email'];

$stmt = $bdd->prepare("SELECT email, sum(visit_duration.duration) as sum from visit_duration join utilisateurs on id_utilisateur=utilisateurs.id_utilisateurs  group by email order by sum desc limit 1;");
$stmt->execute();
$most_user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_most = $most_user['email'];

$user = $user_most . '/' . $user_least;
echo json_encode([
    'site' => $site,
    'page' => $page,
    'total' => $total,
    'user' => $user
]);
