<?php

require_once __DIR__ . '/../database/Database.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST' && $_POST['action'] == 'login') {
  if (isset($_POST['username']) & isset($_POST['password'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
      try {
        $user = getUser($_POST['username']);
        $hash = $user['passwordHash'];

        if (password_verify($_POST['password'], $hash)) {
          $_SESSION['idUser'] = $user['idUser'];
          $_SESSION['username'] = $user['username'];
          header('location: ' . getenv('BASE_URL'));
        } else {
          $errorMessage = "L'identifiant ou le mot de passe est incorrect";
        }
      } catch (PDOException $e) {
        $errorMessage = 'Erreur lors du login: ' . $e->getMessage();
      }
    } else {
      $errorMessage = 'Vous devez remplir tous les champs!';
    }
  }
}
