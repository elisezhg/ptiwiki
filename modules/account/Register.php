<?php

require_once __DIR__ . '/../database/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST' && $_POST['action'] == 'register') {
  if (isset($_POST['username']) & isset($_POST['password'])) {
    try {
      $res = createUser($_POST['username'], $_POST['password']);
      header('location: /');
    } catch (PDOException $e) {
      $errorMessage = 'Erreur lors de la création de compte: ' . $e->getMessage();
      include('../../templates/error.html');
    }
  } else {
    echo "";
  }
}
