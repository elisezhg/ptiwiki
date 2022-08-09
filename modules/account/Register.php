<?php

require_once __DIR__ . '/../database/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST' && $_POST['action'] == 'register') {
  if (isset($_POST['username']) & isset($_POST['password'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
      try {
        $idNewUser = createUser($_POST['username'], $_POST['password']);
        if ($idNewUser) {
          $_SESSION['idUser'] = $idNewUser;
          $_SESSION['username'] = $_POST['username'];
          header('location: ' . getenv('BASE_URL'));
        } else {
          $errorMessage = 'Erreur lors de la création de compte: ' . $e->getMessage();
        }
      } catch (PDOException $e) {
        $errorMessage = 'Erreur lors de la création de compte: ' . $e->getMessage();
      }
    } else {
      $errorMessage = 'Vous devez remplir tous les champs!';
    }
  }
}
