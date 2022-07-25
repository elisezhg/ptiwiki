<?php

require_once '../modules/database/Database.php';
require_once '../modules/utils/MarkDown.php';

//  analyser les paramètres d'entrée
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST') {
    if ($_POST["cancel"]) {
        $op = "read";
    } else {
        $op = $_POST["op"];
    }
    $file = $_POST["file"];
    // echo  "POST: op=$op, file=$file\n";
} else {
    if (array_key_exists("op", $_GET)) $op = $_GET["op"];
    else $op = "read";
    if (array_key_exists("file", $_GET)) $file = $_GET["file"];
    else $file = "PageAccueil";
}

$title = $file == "PageAccueil" ? "Accueil" : $file;

switch ($op) {
    case 'create':
        $title = "Création de la page $file";
        $content = '';
        include('../templates/page.html');
        break;
    case 'read':
    case 'delete':
        $page = getPage($file);
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
            echo "Erreur: impossible de supprimer la page $file";
        }
        break;
    case 'save':
        $newText = $_POST['data'];

        // TODO: change for idUser once auth is implemented
        if (savePage($file, $newText, 64)) {
            header("Location: ?op=read&file=$file");
        } else {
            echo "Erreur: impossible de sauvegarder la page $file";
        }
        break;
    default:
        include('../templates/error.html');
}
