<?php 

require '../inc/bdd.php';

$req2 = $bdd->prepare('SELECT * FROM users WHERE token= :token');
$req2->execute(array(
    "token" => $_GET['token']
));
$idMailDelete = $req2->fetch();
$compteurToken = $req2->rowCount();

if(!isset($_GET["token"])){
    return header('Location: ../index.php');
}

if($compteurToken != 1){
    return header('Location: ../index.php');
}

if($compteurToken === 1){
    $req1 = $bdd->prepare('UPDATE users SET recevoir_email = :non WHERE id = :id');
    $req1->execute(array(
        "non" => "non",
        "id" => $idMailDelete['id']
    ));
    return header('Location: ../liste_article/index.php?stopMail');
}

?>
