<?php

require_once '../modules/Wiki.php';
require_once '../modules/Templates.php';

require_once '../modules/Database.php';

//  analyser les paramètres d'entrée
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST') {
    $op = $_POST["op"];
    $file = $_POST["file"];
    echo  "POST: op=$op, file=$file\n";
} else {
    if (array_key_exists("op", $_GET)) $op = $_GET["op"];
    else $op = "read";
    if (array_key_exists("file", $_GET)) $file = $_GET["file"];
    else $file = "PageAccueil";
}

$wiki = new Wiki("../modules/Wk");          // création de l'object Wiki
$title = $file == "PageAccueil" ? "Accueil" : $file;
$page = $wiki->getPage("$file.text");
if ($page->exists()) $page->load();

switch ($op) {
    case 'create':
        $title = "Création de la page $file";
        $content = '';
        include('../templates/page.html');
        break;
    case 'read':
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
    case 'delete':
        echo mainTPL($title, deleteTPL($file), $navlinks);
        break;
    case 'confirm-delete':
        $page->delete();
        echo mainTPL(
            $title,
            viewTPL(
                "PtiWiki - [Page $file détruite!]",
                markDown2HTML($wiki->getPage("PageAccueil.text")->load()->getText())
            ),
            $navlinks
        );
        break;
    case 'save':
        // truc adapté de http://www.tizag.com/phpT/php-magic-quotes.php
        if (get_magic_quotes_gpc())
            $newText = stripslashes($_POST['data']);
        else
            $newText = $_POST['data'];
        $res = savePage($file, $newText, 64); // TODO: change for idUser once auth is implemented
        if ($res) {
            header("Location: ?op=read&file=$file");
        } else {
            echo "Erreur: impossible de sauvegarder la page $file";
        }
        break;
    default:
        echo mainTPL("Erreur", "Opération non implantée:" . $op, "");
        break;
}
