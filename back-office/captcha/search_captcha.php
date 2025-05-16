<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
  http_response_code(403);
  exit('Accès non-autorisé');
}
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

try {
  if (!empty($search)) {
    $stmt = $bdd->prepare("SELECT id_captcha, question, answer, status, id_auteur, email
FROM captcha join utilisateurs on id_auteur=utilisateurs.id_utilisateurs
WHERE (question LIKE :search OR answer LIKE :search) ORDER BY id_captcha;");
    $stmt->execute(['search' => '%' . $search . '%']);
  } else {
    $stmt = $bdd->query("SELECT id_captcha, question,answer,status,id_auteur,email FROM captcha join utilisateurs on id_utilisateurs=captcha.id_auteur ORDER BY id_captcha");
  }

  $captchas = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $filtered = [];

  foreach ($captchas as $captcha) {
    if ($statusFilter === '' || $statusFilter === 'Tout' || (int)$statusFilter === (int)$captcha['status']) {
      $filtered[] = $captcha;
    }
  }
  if (count($filtered) > 0) {
    foreach ($filtered as $captcha) {
      echo '<tr>
        <td class="align-middle">' . htmlspecialchars($captcha['id_captcha']) . '</td>
        <td class="align-middle">' . htmlspecialchars($captcha['question']) . '</td>
        <td class="align-middle">' . htmlspecialchars($captcha['answer']) . '</td>
        <td class="align-middle">' . htmlspecialchars($captcha['email']) . '</td>
        <td class="align-middle" ' . ($captcha['status'] == 1 ? 'style="color:green"' : 'style="color:red"') . '>
            ' . ($captcha['status'] == 1 ? 'Actif' : 'Inactif') . '
        </td>
        <td>
            <a href="' . captcha_edit_back . '?id=' . $captcha['id_captcha'] . '" class="btn btn-sm btn-warning my-1 me-1">Modifier</a>
            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $captcha['id_captcha'] . '">Supprimer</button>
        </td>
    </tr>
    <div class="modal fade" id="modal' . $captcha['id_captcha'] . '" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Confirmation</h1>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cette question captcha ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="' . captcha_back . '?delete_id=' . $captcha['id_captcha'] . '" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>';
    }
  } else {
    echo "<tr><td colspan='12'>Aucun question trouvée.</td></tr>";
  }
} catch (PDOException $e) {
  echo "<tr><td colspan='12'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
