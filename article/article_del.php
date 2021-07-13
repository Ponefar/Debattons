<?php 
session_start();
 
require '../inc/bdd.php' ;
require '../inc/function.php';

if(isset($_SESSION['id'])){
    if(!$_SESSION['admin']){
        return header('Location: ../indAex.php');
        echo 'salut';
    }
}else{
    return header('Location: ../index.php');
    echo 'salut';
}

if(isset($_POST['id'])){
    if(empty($_POST['id'])){
        return header('Location: ../index.php');
    }
}else {
    return header('Location: ../index.php');
}


$mareq = $bdd->prepare('SELECT url_image FROM article WHERE id = :getid');
$mareq->execute(array(
    "getid" => $_POST['id']
));
$recupUrlImage = $mareq->fetch();

$dossierImgArticleVerif = '../img_article/imagesArticles/';
$dossierImgArticleSupp = '../img_article/imagesArticlesSupp/';

rename($dossierImgArticleVerif . $recupUrlImage['url_image'], $dossierImgArticleSupp . $recupUrlImage['url_image']);    


$deleteArticle = $bdd->prepare('DELETE FROM article WHERE id = :idArticle');
$deleteArticle->execute(array(
    'idArticle' => $_POST['id']
));

$insert_verif = $bdd->prepare('UPDATE article_save SET heure_deleteAdd = NOW() , mail_deleteAdd = :email, id_deleteAdd = :id_deleteAdd, Etat = :etat WHERE id_article = :id ');
$insert_verif->execute(array(
    "email" =>  $_SESSION['email'],
    "etat" => "Del",
    "id_deleteAdd" => $_SESSION['id'],
    "id" => $_POST['id']
));    


?>