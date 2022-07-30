<?php

require_once '../modules/account/Login.php';
require_once '../modules/account/Logout.php';
require_once '../modules/account/Register.php';
require_once '../modules/database/Database.php';
require_once '../modules/utils/MarkDown.php';
require_once '../modules/utils/UrlManipulation.php';

$showActions = false;
$isAdmin = false;
$isLoggedIn = false;

if (!empty($_SESSION['username']) && !empty($_SESSION['idUser'])) {
    $isLoggedIn = true;
    $showActions = true;
    $isAdmin = isAdmin($_SESSION['username']);
}

//  analyser les paramètres d'entrée
$method = $_SERVER['REQUEST_METHOD'];

if (!empty($_GET["action"])) {
    $action = $_GET['action'];
    if ($action == 'logout') {
        logout();
    } elseif ($action == 'login' || $action == 'register') {
        include '../templates/authentication.html';
    } else {
        $errorMessage = 'Action non implémentée: ' . $action;
        include('../templates/error.html');
    }
    return;
} elseif ($method == 'POST') {
    if ($_POST["cancel"]) {
        $op = "read";
    } else {
        $op = $_POST["op"];
    }
    $file = $_POST["file"];
} else {
    if (array_key_exists("op", $_GET)) $op = $_GET["op"];
    else $op = "read";
    if (array_key_exists("file", $_GET)) $file = $_GET["file"];
    else $file = "PageAccueil";
    if (array_key_exists("user", $_GET)) $user = $_GET["user"];
}

if ($op != 'read' && empty($_SESSION['username'])) {
    $errorMessage = 'Vous devez être connecté pour pouvoir contribuer';
    include('../templates/error.html');
    return;
}


// Files
if ($file == 'all') {
    $showActions = false;
    $title = 'Toutes les pages';
    $items = getAllPages();
    include('../templates/page.html');
    return;
} elseif ($file == 'random') {
    $randomPageTitle = getRandomPageTitle();
    header('location: ?op=read&file=' . $randomPageTitle);
    return;
}

// Users
if ($isAdmin && $user == 'all' && $op == 'read') {
    $showActions = false;
    $title = 'Liste des utilisateurs';
    $items = getAllUsers();
    include('../templates/page.html');
    return;
} elseif ($isAdmin && $user != null && $op == 'delete') {
    $showActions = false;
    deleteUser($user);
    header('location: ?op=read&user=all');
    return;
}

$title = $file == "PageAccueil" ? "Accueil" : $file;

switch ($op) {
    case 'create':
        $showActions = false;
        $title = "Création de la page $file";
        include('../templates/page.html');
        break;
    case 'read':
    case 'delete':
        $page = getPage($file);
        if ($page == null) header('location: ?op=create&file=' . $file);
        $datetime = explode(" ", $page['lastModifiedDateTime']);
        $date = $datetime[0];
        $time = $datetime[1];
        $author = $page['username'] ? $page['username'] : "[supprimé]";
        $content = markDown2HTML($page['content']);
        include('../templates/page.html');
        break;
    case 'update':
        $page = getPage($file);
        $content = $page['content'];
        include('../templates/page.html');
        break;
    case 'confirm-delete':
        if (deletePage($file)) {
            header("Location: ?op=read&file=PageAccueil");
        } else {
            $errorMessage = "Impossible de supprimer la page $file";
            include('../templates/error.html');
        }
        break;
    case 'save':
        $newText = $_POST['data'];
        if (savePage($file, $newText, $_SESSION['idUser'])) {
            header("Location: ?op=read&file=$file");
        } else {
            $errorMessage = "Impossible de sauvegarder la page $file";
            include('../templates/error.html');
        }
        break;
    default:
        $errorMessage = 'Opération non implémentée: ' . $op;
        include('../templates/error.html');
}
