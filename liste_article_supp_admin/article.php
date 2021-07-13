<?php 

ob_start();

require '../inc/header.php'; 

$success = "";
$errorMsg = "";

$get_id = htmlspecialchars($_GET['id']);
$article = $bdd->prepare('SELECT * FROM article_save WHERE id = ?');
$article->execute(array($get_id));

if(isset($_SESSION['id'])){
    if(!$_SESSION['admin']){ 
        return header('Location: ../index.php');
    }
}else {
    return header('Location: ../index.php');
}

if($article->rowCount() == 0){
    return header('Location: ./liste_des_articles.php');
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    return header('Location: ../index.php');
}

if($article->rowCount() == 1){
    $article = $article->fetch();
    $titre = $article['titre'];
    $contenu = $article['contenu'];
    $url_image = $article['url_image'];
    $emailEnvoyeur = $article['emailEnvoyeur'];
    $categorie = $article['categorie'];
    $signature_article = $article['signature_article'];
    $id = $article['id_envoyeur'];
}
?>

<script>
 $("title").text("Article supprimé - Debattons.fr")
</script>

<div class="liste_article">
    <div class="index_php_admin">
        <div class="center title">Article publié</div>
        <h1><?php echo '<div class="titre_page_index">' .  $titre . '</div>' ; ?></h1>
        <?php 
        echo '<img src = "../img_article/imagesArticlesSupp/' . $url_image . '" width = "200px" height = "200px"/><br><br> ';
        ?>

        <?php
        echo '<div class="EspaceCommTexte contenu">' .  $contenu . '</div><br><br>' ;
        ?>
        <br />
        <hr>
        <br />
        <table>
            <tr><td class="bold">Catégorie : </td> <td><?php    echo $categorie ?> </td></tr>
            <tr><td class="bold">Signature : </td> <td><?php    echo $signature_article ?> </td></tr>
            <tr><td class="bold">Date : </td> <td><?php    echo strftime(/*"%A*/ "%d/%m/%Y à %Hh%M", strtotime($article['date_time_publi']))  ;?>  </td></tr>
            <tr><td class="bold">Email envoyeur : </td> <td><?php    echo $emailEnvoyeur. '<br>';  ?> </td></tr>
            <!-- <tr><td class="bold">Id envoyeur  : </td> <td><?php    echo $id. '<br>';  ?> </td></tr> -->
            <tr style="color:red"><td class="bold">Supprimé le : </td> <td><?php    echo strftime(/*"%A*/ "%d/%m/%Y à %Hh%M", strtotime($article['heure_deleteAdd']))  ;?>  </td></tr>
            <tr style="color:red"><td class="bold">Par : </td> <td><?php    echo $article['mail_deleteAdd']?>  </td></tr>
            <!-- <tr style="color:red"><td class="bold">Par id numéro : </td> <td><?php    echo $article['id_deleteAdd']?>  </td></tr> -->
            <tr style="color:green"><td class="bold">Ajouté par : </td> <td><?php    echo $article['modification']?>  </td></tr>
        
        </table>
    </div>
</div>

<?php

$req = $bdd->prepare('SELECT * FROM article_save_users WHERE id_article  = :id_article');
$req->execute(array(
    "id_article" => $article['id_article']
));

$articleUsers = $req->fetch();

?>

<div class="liste_article">
    <div class="index_php_admin">
            <div class="center title">Article original</div>
            <h1><?php echo '<div class="titre_page_index">' .  $articleUsers['titre'] . '</div>' ; ?></h1>
            <?php 
            echo '<img src = "../img_article/imagesArticlesSupp/' . $articleUsers['url_image'] . '" width = "200px" height = "200px"/><br><br> ';
            ?>

            <?php
            echo '<div class="EspaceCommTexte contenu">' .  $articleUsers['contenu'] . '</div><br><br>' ;
            ?>
            <br />
            <hr>
            <br />
            <table>
                <tr><td class="bold">Catégorie : </td> <td><?php    echo $articleUsers['categorie'] ?> </td></tr>
                <tr><td class="bold">Signature : </td> <td><?php    echo $articleUsers['signature_article'] ?> </td></tr>
            </table>
    </div>
</div>


<?php require '../inc/footer.php' ?>
