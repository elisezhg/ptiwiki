<?php

require_once 'Database.php';

// création de tags HTML
function tag($tag, $body, $attrs = "")
{
    $out = "<$tag";
    if ($attrs) $out += " $attrs";
    return "$out>$body</$tag>";
}

$pages = getPages();

// transformer le simili MarkDown en HTML
function markDown2HTML($texte)
{
    // montrer tout le HTML...
    $texte = preg_replace("/</", "&lt;", $texte);
    // remplacer les **xxx** par  <b>xxx</b>
    $texte = preg_replace("/\*\*(.*?)\*\*/", "<b>$1</b>", $texte);
    // remplacer les *xxx*   par  <em>xxx</em>
    $texte = preg_replace("/\*(.*?)\*/", "<em>$1</em>", $texte);
    // remplacer une série de deux lignes ou plus débutant par un tiret par une liste non numéroté
    $nb = preg_match_all("/((\r?\n-)(.*)){2,}/", $texte, $listes);
    for ($i = 0; $i < $nb; $i++) {
        $lis = preg_replace("/(\r?\n)- *(.*)/", "<li>$2</li>", $listes[0][$i]);
        $texte = str_replace($listes[0][$i], "<ul>$lis\n</ul>", $texte);
    }
    // remplacer les # par des titres
    $re_titre = "/^(#+) *(.*)/m";
    $nb = preg_match_all($re_titre, $texte, $titres);
    for ($i = 0; $i < $nb; $i++) {
        $niveau = strlen(preg_replace($re_titre, "$1", $titres[0][$i]));
        $titre = preg_replace($re_titre, "$2", $titres[0][$i]);
        $texte = str_replace($titres[0][$i], "\n<h$niveau>$titre</h$niveau>", $texte);
    }
    // remplacer les références de la forme [lien](url) par <a href="url">lien</a>
    $texte = preg_replace("/\[(.*?)\]\((.*?)\)/", "<a href='$2'>$1</a>", $texte);
    // remplacer les WikiWord par <a href="PtiWiki.php?op='view'&file='WikiWord'>WikiWord</a>"
    $texte = preg_replace_callback(
        "/(\b[A-Z]\w*?[A-Z]\w*?\b)/",
        "viewLinkCallback",
        $texte
    );
    // remplacer les "double newline" par </p><p>
    $texte = preg_replace("/(\r?\n){2}/", "</p>\n<p>", $texte);
    // entourer le tout de <p></p>
    return "<div>" . $texte . "</div>";
}

// call back utilisé pour la génération des liens vers les pages Wiki indentifiées par les WikiWords
function viewLinkCallback($matches)
{
    global $pages;
    if (in_array($matches[0], $pages)) {
        $op = "read";
        $style = "";
    } else { // new file, make the link in red and set op to create
        $op = "create";
        $style = " style='color:red'";
    }
    return "<a href='?op=$op&file=$matches[0]'$style>$matches[1]</a>";
}
