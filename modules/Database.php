<?php

include('Config.php');

function getConnection()
{
    try {
        $url = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_SCHEMA') . ';charset=utf8';
        $conn = new PDO($url, getenv('DB_USER'), getenv('DB_PASSWORD'));
        /* Activer les exceptions sur les erreurs */
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        /* S'il y a une erreur, une exception est levée */
        echo "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
}

function getPages()
{
    $conn = getConnection();
    $resultat = $conn->prepare("SELECT title FROM Page");
    $resultat->execute();
    $pages = $resultat->fetchAll(PDO::FETCH_COLUMN, 0);
    return $pages;
}

function getPage($title)
{
    $conn = getConnection();
    $resultat = $conn->prepare("
        SELECT content, username, lastModifiedDateTime
        FROM Page LEFT JOIN User
        ON Page.lastModifiedIdUser = User.idUser
        AND title = :title
    ");
    $resultat->setFetchMode(PDO::FETCH_ASSOC);
    $resultat->bindParam(':title', $title);
    $resultat->execute();
    $tab = $resultat->fetchAll();
    return $tab[0];
}

function savePage($title, $text, $idUser)
{
    $conn = getConnection();
    $resultat = $conn->prepare("
        REPLACE INTO
            Page (title, content, lastModifiedIdUser, lastModifiedDateTime)
        VALUES
            (:title, :text, :idUser, CONVERT_TZ(NOW(),'SYSTEM','America/Montreal'))
    ");
    echo "saving";
    $resultat->bindParam(':title', $title);
    $resultat->bindParam(':text', $text);
    $resultat->bindParam(':idUser', $idUser);
    $resultat->execute();
    return $resultat;
}

function deletePage($title)
{
    $conn = getConnection();
    $resultat = $conn->prepare("DELETE FROM Page WHERE title = :title");
    $resultat->bindParam(':title', $title);
    $resultat->execute();
    return $resultat;
}

function getUser($id)
{
    $conn = getConnection();
    $resultat = $conn->prepare("SELECT * FROM User WHERE idUser = :id");
    $resultat->bindParam(':id', $id);
    $resultat->execute();
    $tab = $resultat->fetchAll();
    return $tab[0];
}

function deleteUser($id)
{
    $conn = getConnection();
    $resultat = $conn->prepare("DELETE FROM User WHERE idUser = :id");
    $resultat->bindParam(':id', $id);
    $resultat->execute();
    return $resultat;
}
