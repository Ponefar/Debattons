<?php

session_start();

require '../inc/bdd.php';

if(!isset($_SESSION['id'])){
    return header('Location: ../index.php');
}

if(!$_SESSION['admin']){
    return header('Location: ../index.php');
}

if(!isset($_GET['id'])){
    return header('Location: ../index.php');
}

if(empty($_GET['id'])){
    return header('Location: ../index.php');
}

$req = $bdd->prepare('DELETE FROM article WHERE id = ?');
$req->execute(array($_GET['id']));

return header('Location: ./adminIndex.php');

?>
