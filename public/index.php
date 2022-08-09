<?php

require_once '../modules/account/Login.php';
require_once '../modules/account/Logout.php';
require_once '../modules/account/Register.php';
require_once '../modules/database/Database.php';
require_once '../modules/utils/MarkDown.php';
require_once '../modules/utils/UrlManipulation.php';

// State
$showActions = false;
$isAdmin = false;
$isLoggedIn = false;

if (!empty($_SESSION['username']) && !empty($_SESSION['idUser'])) {
    $isLoggedIn = true;
    $showActions = true;
    $isAdmin = isAdmin($_SESSION['username']);
}

$method = $_SERVER['REQUEST_METHOD'];

// Process account actions first (login, register, logout)
if (!empty($_GET["action"])) {
    processAction($_GET['action']);

    // Process database operations second (create, read, update, delete, ...)
} elseif ($method == 'POST') {
    $op = $_POST["cancel"] ? "read" : $_POST["op"];
    $file = $_POST["file"];
    processDatabaseOperation($op, $file, null);
} else {
    if (array_key_exists("op", $_GET)) $op = $_GET["op"];
    else $op = "read";
    if (array_key_exists("file", $_GET)) $file = $_GET["file"];
    else $file = "PageAccueil";
    if (array_key_exists("user", $_GET)) $user = $_GET["user"];
    processDatabaseOperation($op, $file, $user);
}

function processDatabaseOperation($op, $file, $user)
{
    global $showActions, $isAdmin, $isLoggedIn, $errorMessage;

    // Block non-logged in users from editing pages
    if ($op != 'read' && empty($_SESSION['username'])) {
        $errorMessage = 'Vous devez être connecté pour pouvoir contribuer';
        include('../templates/error.html');
        return;
    }

    // Specific operations
    if ($user) {
        showUsers($op, $user);
        return;
    } elseif ($file == "all") {
        showAllPages();
        return;
    } elseif ($file == "random") {
        showRandomPage();
        return;
    }

    // Process generic operations
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
}

function showRandomPage()
{
    global $showActions, $isAdmin, $isLoggedIn, $errorMessage;

    $file = "random";
    $randomPageTitle = getRandomPageTitle();
    header('location: ?op=read&file=' . $randomPageTitle);
    return;
}

function showAllPages()
{
    global $showActions, $isAdmin, $isLoggedIn, $errorMessage;

    $showActions = false;
    $file = "all";
    $title = 'Toutes les pages';
    $items = getAllPages();
    include('../templates/page.html');
    return;
}

function showUsers($op, $user)
{
    global $showActions, $isAdmin, $isLoggedIn, $errorMessage;

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
}

function processAction($action)
{
    global $errorMessage;
    switch ($action) {
        case 'login':
        case 'register':
            include '../templates/authentication.html';
            break;
        case 'logout':
            logout();
            break;
        default:
            $errorMessage = 'Action non implémentée: ' . $action;
            include('../templates/error.html');
    }
    return;
}
