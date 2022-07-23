<?php

/**
 * Création en PHP d'un système simple de Wiki
 *  inspiré très fortement par la structuration proposée dans  
 *        James Payne, Beginning Python, Wiley, 2010, p 430-431
 *  Les pages sont conservées dans un répertoire ouvert en écriture pour tous...
 *  Il serait préférable  d'utiliser une BD avec une meilleure gestion des usagers.
 */

require_once 'Wiki.php';
require_once 'Templates.php';

//  analyser les paramètres d'entrée
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST') {
    $op = $_POST["op"];
    $file = $_POST["file"];
} else {
    if (array_key_exists("op", $_GET)) $op = $_GET["op"];
    else $op = "view";
    if (array_key_exists("file", $_GET)) $file = $_GET["file"];
    else $file = "PageAccueil";
}


$wiki = new Wiki("Wk");          // création de l'object Wiki
$title = "PtiWiki - $file";
$page = $wiki->getPage("$file.text");
if ($page->exists()) $page->load();
$navlinks = viewLinkTPL("PageAccueil", "Accueil") . " " . editLinkTPL($file, "Éditer");
if ($file != "PageAccueil") $navlinks = $navlinks . " " . deleteLinkTPL($file, "Détruire");

switch ($op) {
    case 'create':
        echo mainTPL(
            $title,
            editTPL(
                bannerTPL("Création de $file"),
                $file,
                ""
            ),
            $navlinks
        );
        break;
    case 'read':
        echo mainTPL(
            $title,
            viewTPL(
                bannerTPL($title),
                markDown2HTML($page->getText())
            ),
            $navlinks
        );
        break;
    case 'update':
        echo mainTPL(
            $title,
            editTPL(
                bannerTPL($title),
                $file,
                $page->getText()
            ),
            $navlinks
        );
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
        $page->setText($newText)->save();
        echo mainTPL(
            $title,
            viewTPL(
                bannerTPL($title),
                markDown2HTML($newText)
            ),
            $navlinks
        );
        break;
    default:
        echo mainTPL("Erreur", "Opération non implantée:" . $op, "");
        break;
}
