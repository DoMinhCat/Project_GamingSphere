<?php
session_start();
$_SESSION['actif'] = time();
echo json_encode(["status" => "updated"]);
?>