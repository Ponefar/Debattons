<?php require '../inc/header.php'; 

if(isset($_SESSION['id'])){
   
    if (!$_SESSION['admin']){
        return header('Location: ../index.php');
    }

}else{
    return header('Location: ../index.php');
} 


if((!isset($_POST['id'])) || empty($_POST['id'])){
    return header('Location: ../index.php');
}

$recup_article = $bdd->query('SELECT * FROM verification WHERE id =' . $_POST['id']);

while($a = $recup_article->fetch()){

    $titre = $a['titre'];
    $contenu = $a['contenu'];
    $date_time_publi = $a['date_time_publi'];
    $url_image = $a['url_image'];
    $categorie = $a['categorie'];
    $signature_article = $a['signature_article'];
    $id_envoyeur = $a['id_envoyeur'];

    $dossierImgArticleVerif = '../img_article/imagesArticlesVerifications/';
    $dossierImgArticleSupp = '../img_article/imagesArticlesSupp/';

    rename($dossierImgArticleVerif . $url_image, $dossierImgArticleSupp . $url_image);    


    $deleteArticle = $bdd->prepare('DELETE FROM verification WHERE id = :idArticle');
    $deleteArticle->execute(array(
        'idArticle' => $_POST['id']
    ));


    $insert_verif = $bdd->prepare('UPDATE article_save SET titre = :titre, contenu = :contenu, url_image = :url_image, categorie = :categorie
    ,signature_article = :signature_article, heure_deleteAdd = NOW() , mail_deleteAdd = :email, id_deleteAdd = :id_deleteAdd, Etat = :etat WHERE id_article = :id ');
    $insert_verif->execute(array(
        "titre" => $titre,
        "contenu" => nl2br($contenu),
        "url_image" => $url_image,
        "categorie" => $categorie,
        "signature_article" => $signature_article,
        "email" =>  $_SESSION['email'],
        "id_deleteAdd" =>  $_SESSION['id'],
        "id" => $_POST['id'],
        "etat" => "Del"
    )); 
}

echo $_POST['id'] ;

?>
