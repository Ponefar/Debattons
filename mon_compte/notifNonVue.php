<?php 

ob_start();

require '../inc/header.php' ;

if(isset($_GET['id_comm'])){
    $req1 = $bdd->prepare('UPDATE espace_commentaire SET comm_notif = "oui" WHERE id_comm = :id');
    $req1->execute(array(
        "id" => $_GET['id_comm'],
    ));
    return header('Location: ./notifications.php');
}

ob_end_flush();

?>