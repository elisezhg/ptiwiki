<?php

require_once __DIR__ . '/../utils/Config.php';

$conn = getConnection();

function getConnection()
{
    try {
        $url = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_SCHEMA') . ';charset=utf8';
        $conn = new PDO($url, getenv('DB_USER'), getenv('DB_PASSWORD'));
        /* Activer les exceptions sur les erreurs */
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        $errorMessage = 'Erreur lors de la connexion à la base de données: ' . $e->getMessage();
        include('../../templates/error.html');
    }
}

function getAllPages()
{
    global $conn;
    $resultat = $conn->prepare("SELECT title FROM Page");
    $resultat->execute();
    $pages = $resultat->fetchAll(PDO::FETCH_COLUMN, 0);
    return $pages;
}

function getPage($title)
{
    global $conn;
    $resultat = $conn->prepare("
        SELECT content, username, lastModifiedDateTime
        FROM Page LEFT JOIN User
        ON Page.lastModifiedIdUser = User.idUser
        WHERE title = :title
    ");
    $resultat->setFetchMode(PDO::FETCH_ASSOC);
    $resultat->bindParam(':title', $title);
    $resultat->execute();
    $tab = $resultat->fetchAll();
    return $tab[0];
}

function getRandomPageTitle()
{
    global $conn;
    $resultat = $conn->prepare("
        SELECT title
        FROM Page
        WHERE title != 'PageAccueil'
        ORDER BY RAND()
        LIMIT 1
    ");
    $resultat->execute();
    $value = $resultat->fetchColumn();
    return $value;
}

function savePage($title, $text, $idUser)
{
    global $conn;
    $resultat = $conn->prepare("
        REPLACE INTO
            Page (title, content, lastModifiedIdUser)
        VALUES
            (:title, :text, :idUser)
    ");
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

function getUser($username)
{
    global $conn;
    $resultat = $conn->prepare("SELECT * FROM User WHERE username = :username");
    $resultat->bindParam(':username', $username);
    $resultat->execute();
    $tab = $resultat->fetchAll();
    return $tab[0];
}

function getAllUsers()
{
    global $conn;
    $resultat = $conn->prepare("
        SELECT username
        FROM User
        WHERE idRole != (
            SELECT idRole
            FROM Role
            WHERE name = 'Admin'
        )
    ");
    $resultat->execute();
    $users = $resultat->fetchAll(PDO::FETCH_COLUMN, 0);
    return $users;
}

function createUser($username, $password)
{
    global $conn;
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $resultat = $conn->prepare("
        INSERT INTO
            User (username, passwordHash, idRole)
        VALUES
            (:username, :passwordHash, (SELECT idRole FROM Role WHERE name = 'User'))
    ");
    $resultat->bindParam(':username', $username);
    $resultat->bindParam(':passwordHash', $passwordHash);
    $resultat->execute();
    return $resultat ? $conn->lastInsertId() : false;
}

function deleteUser($username)
{
    global $conn;
    $resultat = $conn->prepare("DELETE FROM User WHERE username = :username");
    $resultat->bindParam(':username', $username);
    $resultat->execute();
    return $resultat;
}

function isAdmin($username)
{
    global $conn;
    $resultat = $conn->prepare("
        SELECT idUser
        FROM User
        INNER JOIN Role
        ON User.idRole = Role.idRole
        WHERE username = :username
        AND Role.name = 'Admin'
        LIMIT 1
    ");
    $resultat->setFetchMode(PDO::FETCH_ASSOC);
    $resultat->bindParam(':username', $username);
    $resultat->execute();
    $val = $resultat->fetchColumn();
    return $val != null;
}
