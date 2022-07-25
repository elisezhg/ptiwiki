<?php
require_once 'MarkDown.php';

// templates transposés de 
// James Payne, Beginning Python, Wiley, 2010, p 435-436

function mainTPL($title, $body, $navlinks)
{
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
        <title>$title</title>
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
    </head>
    <body>
        $body
        <hr/>
        $navlinks
    </body>
</html>
HTML;
}

function viewTPL($banner, $processedText)
{
    return <<<VIEW
    $banner
    $processedText
    VIEW;
}

function editTPL($banner, $pageURL, $text)
{
    return <<<WRITE
    $banner
    <form method="POST" action="/">
        <input type="hidden" name="op" value="save"></input>
        <input type="hidden" name="file" value="$pageURL"></input>
        <textarea rows="15" cols="80" name="data">$text</textarea>
        <br></br>
        <input type="submit" value="sauver"></input>
    </form>
    WRITE;
}

function errorTPL($error)
{
    return "<h1>Erreur: $error</h1>";
}

function bannerTPL($banner)
{
    return "<p style='color:green'>$banner</p><hr></hr>";
}

function editLinkTPL($file, $name)
{
    return "<a href='?op=update&file=$file'>$name</a>";
}

function deleteLinkTPL($file, $name)
{
    return "<a href='?op=delete&file=$file'>$name</a>";
}

function deleteTPL($pageURL)
{
    return <<<DELETE
    <p>Êtes-vous certain de vouloir détruire la page "$pageURL"</p>
    <form method="GET action="/">
        <input type="hidden" name="op" value="confirm-delete"></input>
        <input type="hidden" name="file" value="$pageURL"></input>
        <input type="submit" value="Détruire $pageURL!"></input>
    </form>    
    DELETE;
}
