<?php require '../inc/header.php'; 

if(!$_SESSION['admin']){
    return header('Location: ../index.php');
}

if(!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['id_article']) || empty($_POST['id_article'])){
    return header('Location: ../index.php');
}

if((isset($_POST['id'])) && !empty($_POST['id'])){

    $deleteArticle = $bdd->prepare('DELETE FROM espace_commentaire WHERE id_comm = :idArticle');
    $deleteArticle->execute(array(
        'idArticle' => $_POST['id']
    ));
}

?>