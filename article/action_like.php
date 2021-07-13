<?php session_start();

require '../inc/bdd.php';
require '../inc/function.php';

if(!isset($_POST['t'], $_POST['id'], $_SESSION['id']) && empty($_POST['t']) && empty($_POST['id'])){
    return header('Location: ./article/article.php');
}
        
$getid = (int) $_POST['id'];
$gett = (int) $_POST['t'];

$sessionid = $_SESSION['id'];
$array1 = [$getid,$sessionid];

$maReq = 'SELECT id FROM article WHERE id = ?';
$array = [$getid];
$compteur = compteurReqPrepareArray($maReq, $array);


if($compteur != 1){
    return header('Location: ./article/article.php');
}
    
if($gett === 1){
    $maReq = 'SELECT id FROM likes WHERE id_article = ? AND id_membre = ?';
    $compteurLikesBySession = compteurReqPrepareArray($maReq, $array1);
    
    $maReq = 'SELECT id FROM dislikes WHERE id_article = ? AND id_membre = ?';
    $resMaReq = compteurReqPrepareArray($maReq, $array1);

    $maReq = 'DELETE FROM dislikes WHERE id_article = ? AND id_membre = ?';
    $resMaReq = ReqPrepareArray($maReq, $array1);

    if($compteurLikesBySession === 1){
        $maReq = 'DELETE FROM likes WHERE id_article = ? AND id_membre = ?';
        $DeleteLikeBySession = ReqPrepareArray($maReq, $array1);

        $likes = $bdd->prepare('SELECT id FROM likes WHERE id_article = ?');
        $likes->execute(array($_POST['id']));
        $likes = $likes->rowCount();

        $dislikes = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ?');
        $dislikes->execute(array($_POST['id']));
        $dislikes = $dislikes->rowCount();

        $filter = array(
            1, "/" , $likes , "/" , $dislikes
        );    
        $size = sizeof($filter);

        for($i = 0 ; $i < $size ; $i++){
            echo $filter[$i];
        }
    }

    if($compteurLikesBySession != 1){

        $maReq = 'INSERT INTO likes(id_article, id_membre, date_time ) VALUES (?, ? , NOW())';
        $InsertLikeBySession = ReqPrepareArray($maReq, $array1);

        $likes = $bdd->prepare('SELECT id FROM likes WHERE id_article = ?');
        $likes->execute(array($_POST['id']));
        $likes = $likes->rowCount();

        $dislikes = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ?');
        $dislikes->execute(array($_POST['id']));
        $dislikes = $dislikes->rowCount();

        $filter = array(
            2, "/" , $likes , "/" , $dislikes
        );    
        $size = sizeof($filter);

        for($i = 0 ; $i < $size ; $i++){
            echo $filter[$i];
        }

    }
}


if($gett == 2){
    $maReq = 'SELECT id FROM dislikes WHERE id_article = ? AND id_membre = ?';
    $compteurDislikesBySession = compteurReqPrepareArray($maReq, $array1);

    $maReq = 'DELETE FROM likes WHERE id_article = ? AND id_membre = ?';
    $resMaReq = ReqPrepareArray($maReq, $array1);

    if($compteurDislikesBySession === 1){

        $maReq = 'DELETE FROM dislikes WHERE id_article = ? AND id_membre = ?';
        $resMaReq = ReqPrepareArray($maReq, $array1);

        $likes = $bdd->prepare('SELECT id FROM likes WHERE id_article = ?');
        $likes->execute(array($_POST['id']));
        $likes = $likes->rowCount();

        $dislikes = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ?');
        $dislikes->execute(array($_POST['id']));
        $dislikes = $dislikes->rowCount();

        $filter = array(
            3, "/" , $likes , "/" , $dislikes
        );    
        $size = sizeof($filter);

        for($i = 0 ; $i < $size ; $i++){
            echo $filter[$i];
        }
    }
        
    if($compteurDislikesBySession != 1){

        $maReq = 'INSERT INTO dislikes(id_article, id_membre, date_time ) VALUES (?, ?, NOW())';
        $resMaReq = ReqPrepareArray($maReq, $array1);


        $likes = $bdd->prepare('SELECT id FROM likes WHERE id_article = ?');
        $likes->execute(array($_POST['id']));
        $likes = $likes->rowCount();

        $dislikes = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ?');
        $dislikes->execute(array($_POST['id']));
        $dislikes = $dislikes->rowCount();

        $filter = array(
            4, "/" , $likes , "/" , $dislikes
        );    
        $size = sizeof($filter);

        for($i = 0 ; $i < $size ; $i++){
            echo $filter[$i];
        }

    }
    
}



?>
