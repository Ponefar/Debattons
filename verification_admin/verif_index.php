<?php

session_start();

require '../inc/bdd.php';
require '../inc/function.php';
$error = 0;

if(isset($_SESSION['id'])){
    if(!$_SESSION['admin']){ 
        return header('Location: ../index.php');
    }
}else{
    return header('Location: ../index.php');
}

$req = $bdd->prepare('SELECT * FROM verification WHERE id = :id');
$req->execute(array(
    'id' => $_GET['id']
));
$recupData = $req->fetch();
$compteurData = $req->rowCount();


if($compteurData === 0){
    return header('Location: ../index.php');
}

if(isset($_POST['titre'])){
    $erreur = 0 ;
    $extensions_autorisees_image = array('gif' ,'png' ,'jpg' ,'jpeg' ,'pdf');
    $nom_ficher = $_FILES["images"]["name"];
    $extensions_image = pathinfo($nom_ficher, PATHINFO_EXTENSION);

    $titre = htmlspecialchars($_POST['titre']) ;
    $contenu = htmlspecialchars($_POST['contenu']) ;
    $categorie = htmlspecialchars($_POST['categorie']) ;
    $signature_article = htmlspecialchars($_POST['signature_article']) ;
    $image = $_FILES["images"]["tmp_name"];
    $dossierImgArticle = '../img_article/imagesArticlesVerifications/';
    $randomNameImg = genererChaineAleatoire(10, $listeCar);
    $nomImageBdd = $randomNameImg . "." . $extensions_image;

    if(empty($titre) && $erreur == 0){
        $erreur = 1 ;
        $data = array('errForm', 'Titre vide');
    }

    if(strlen($titre) > 150  && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Titre trop long (Max 150 caractères)');
    }

    if(empty($contenu) && $erreur == 0){
        $erreur = 1 ;
        $data = array('errForm', 'Contenu vide');
    }

    if(strlen($contenu) > 40000  && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Contenu trop long (Max 40 000 caractères)');
    }

    if(empty($categorie) && $erreur == 0){
        $erreur = 1 ;
        $data = array('errForm', 'Categorie vide');
    }

    if(empty($signature_article) && $erreur == 0){
        $erreur = 1 ;
        $data = array('errForm', 'Signature article vide');
    }

    if($_FILES['images']['tmp_name'] !== "" && $erreur == 0){

        if(!in_array($extensions_image, $extensions_autorisees_image)){
            $erreur = 1;
            $data = array('errForm', 'Seul les format : gif ,png ,jpg ,jpeg et pdf sont acceptés');
        }
        
        if($_FILES['images']['size'] > 1000000  && $erreur == 0){
            $erreur = 1;
            $data = array('errForm', 'Image trop grande (1 Mo max)');
        }
    }

    if(strlen($categorie) > 100  && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Maximum 100 caracères pour la catégorie');
    }

    if(strlen($signature_article) > 100  && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Maximum 100 caracères pour le pseudo');
    }

    if($erreur === 0){
        if($_FILES['images']['tmp_name'] !== ""){

            $dossierImgArticleVerif = '../img_article/imagesArticlesVerifications/';
            $dossierImgArticleSupp = '../img_article/imagesArticlesSupp/';

            rename($dossierImgArticleVerif . $recupData['url_image'], $dossierImgArticleSupp . $recupData['url_image']);    
            // unlink('../img_article/imagesArticlesVerifications/' . $recupData['url_image']);
            $req1 = $bdd->prepare('UPDATE verification SET titre = ? , contenu = ?, categorie = ?, signature_article = ?, url_image = ? WHERE id =' . $_GET['id']);
            $req1->execute(array($titre, $contenu, $categorie, $signature_article, $nomImageBdd));
            move_uploaded_file($image, $dossierImgArticle . $nomImageBdd);
    
        }else{
            $req1 = $bdd->prepare('UPDATE verification SET titre = ? , contenu = ?, categorie = ?, signature_article = ? WHERE id =' . $_GET['id']);
            $req1->execute(array($titre, $contenu, $categorie, $signature_article));
        }  

        $req = $bdd->prepare('SELECT * FROM verification WHERE id = :id');
        $req->execute(array(
            'id' => $_GET['id']
        ));
        $recupData = $req->fetch();
        $data = array('successForm', 'Modifications effectuées', $recupData['titre'], $recupData['url_image'], $recupData['contenu'], $recupData['categorie'], $recupData['signature_article']);
    }
    header("Content-Type: application/json");
    echo json_encode($data);
}

?>