<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
  http_response_code(403);
  exit('Accès non-autorisé');
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
$search = trim($_GET['search'] ?? '');

try {
  if (!empty($search)) {
    $stmt = $bdd->prepare("SELECT n.id_news, n.titre, n.date_article, n.category, u.email
FROM news n
JOIN utilisateurs u ON n.auteur = u.id_utilisateurs
WHERE n.titre LIKE :search OR u.email LIKE :search ORDER BY n.date_article DESC;");
    $stmt->execute(['search' => '%' . $search . '%']);
  } else {
    $stmt = $bdd->query("SELECT n.id_news, n.titre, n.date_article, n.category, u.email
FROM news n JOIN utilisateurs u ON n.auteur = u.id_utilisateurs ORDER BY n.date_article DESC;");
  }

  $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($articles) > 0) {
    foreach ($articles as $article) {
      echo '<tr>
                        <td class="align-middle">' . htmlspecialchars($article['id_news']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['titre']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['category']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['date_article']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($article['email']) . '</td>
                        <td>
                            <a href=' . article_edit_back . '?id=' . $article['id_news'] . ' class="btn btn-sm btn-warning my-1 me-1">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $article['id_news'] . '">Supprimer</button>';
      echo '<div class="modal fade" id="modal' . $article['id_news'] . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5">Confirmation</h1>
                                </div>
                                <div class="modal-body">
                                  Êtes-vous sûr de vouloir supprimer cet article ?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                  <a href="' . article_back . '?delete_id=' . $article['id_news'] . '" class="btn btn-danger">Supprimer</a>
                                </div>
                              </div>
                            </div>
                          </div>';
      echo '</td>
                    </tr>';
    }
  } else {
    echo "<tr><td colspan='6'>Aucun article trouvé.</td></tr>";
  }
} catch (PDOException $e) {
  echo "<tr><td colspan='6'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
