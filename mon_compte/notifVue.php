<?php 

ob_start();

require '../inc/header.php' ;

if(isset($_GET['id_comm'])){
    if(isset($_GET['redirection']) && isset($_GET['id_article'])){
        $req1 = $bdd->prepare('UPDATE espace_commentaire SET comm_notif = "non" WHERE id_comm = :id');
        $req1->execute(array(
            "id" => $_GET['id_comm'],
        ));
        header('Location: ../article/article.php?id=' . $_GET['id_article']);
        
    }
    else{
        $req1 = $bdd->prepare('UPDATE espace_commentaire SET comm_notif = "non" WHERE id_comm = :id');
        $req1->execute(array(
            "id" => $_GET['id_comm'],
        ));
        header('Location: ./notifications.php');
    }
}

ob_end_flush();

?>

