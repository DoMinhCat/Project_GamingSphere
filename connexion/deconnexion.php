<?php
session_start();
require_once __DIR__ . '/../path.php';
session_destroy();
setcookie('email', '', time() - 60);
header('Location: ../' . index_front);
exit;
