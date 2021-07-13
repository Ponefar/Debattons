<?php

session_start();

require '../inc/bdd.php';
require '../inc/function.php';


@$Newprenom = $_POST['Newprenom'];
@$Newemail = $_POST['Newemail'];
@$Newpassword1 = $_POST['Newpassword1'];
@$Newpassword2 = $_POST['Newpassword2'];
@$Newsubmit = $_POST['Newsubmit'];
@$contact_email = $_POST['contact_email'];
$erreur = 0 ;

if(!isset($_SESSION['id'])){
    return header('Location: ../index.php');
}
        
if(!isset($Newprenom)){
    return header('Location: ../index.php');
}


$stmt = $bdd->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$Newemail]); 
$user = $stmt->fetch();
$url_image = $user['url_image'];
$nom_ficher = $_FILES["images"]["name"];
$extensions_autorisees_image = array('gif' ,'png' ,'jpg' ,'jpeg' ,'pdf');
$extensions_image = pathinfo($nom_ficher, PATHINFO_EXTENSION);
$images = $_FILES["images"]["tmp_name"];
$dossierImgArticle = '../img_article/imagesUsers/';
$dossierImgUsersSupp = '../img_article/imagesUsersSupp/';
$randomNameImg = genererChaineAleatoire(10, $listeCar);
$nomImageBdd = $randomNameImg .".". $extensions_image;
$req3 = $bdd->prepare('SELECT * FROM users WHERE id =' . $_SESSION['id']);
$req3->execute();
$users = $req3->fetch();



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

if(empty($Newprenom)  && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'Il faut remplir le pseudo');        
}

if(strlen($Newprenom) > 100 && $erreur == 0){
    $erreur = 1;
    $data = array("errForm", "Maximum 100 caracères pour le pseudo");
}

if(empty($Newemail) && $erreur == 0){
    $errorMsg = "Il faut remplir l\'e-mail";
    $erreur = 1 ;
    $data = array('errForm', 'Il faut remplir l\'e-mail');        
}

if(!filter_var($Newemail, FILTER_VALIDATE_EMAIL) && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'Il faut remplir l\'e-mail avec un format valide');        
}

if(strlen($Newemail) > 100 && $erreur == 0){
    $erreur = 1;
    $data = array("errForm", "Maximum 100 caracères pour l'e-mail");
}

if(strlen($Newpassword1) > 100 || strlen($Newpassword2) > 100 && $erreur == 0){
    $erreur = 1;
    $data = array("errForm", "Maximum 100 caracères pour les mots de passe");
}

if(!empty($Newpassword1) && (!empty($Newpassword2) && ($Newpassword1 != $Newpassword2)) && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'Password1 est différent de Password2');        
}

if(($user) && ($Newemail != $users['email']) && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'E-mail déjà utilisé');        
}

if(!empty($Newpassword1) && (empty($Newpassword2)) && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'Il faut remplir les deux MDPs');        
}

if(empty($Newpassword1) && (!empty($Newpassword2)) && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'Il faut remplir les deux MDPs');        
}

if(empty($contact_email) && $erreur == 0){
    $erreur = 1 ;
    $data = array('errForm', 'Il faut cocher le bouton recevoir les articles ou non');        
}

if($erreur == 0){
    if(empty($Newpassword1)){
        $Newpassword1 = $_SESSION['password'];  
    }else{
        $Newpassword1 = md5($Newpassword2);
    }

    if($_FILES['images']['tmp_name'] !== ""){
        
        if($url_image != "users.jpg"){
            rename($dossierImgArticle . $url_image, $dossierImgUsersSupp . "USER-ID-" .$_SESSION['id'] . "-" . $url_image);    
        }

        $req = $bdd->prepare('UPDATE users SET prenom = ?, email = ?, url_image = ?, recevoir_email = ?, password = ? WHERE id= ?');
        $req->execute(array($Newprenom,$Newemail, $nomImageBdd, $contact_email, $Newpassword1, $_SESSION['id']));
        move_uploaded_file($images, $dossierImgArticle . $nomImageBdd);
    }

    if($_FILES['images']['tmp_name'] == ""){
        $req = $bdd->prepare('UPDATE users SET prenom = ?, email = ?, recevoir_email = ?, password = ? WHERE id= ?');
        $req->execute(array($Newprenom, $Newemail, $contact_email, $Newpassword1, $_SESSION['id']));
    }

    $req = $bdd->prepare('SELECT * from users WHERE id = :id');
    $req->execute(array(
        "id" => $_SESSION['id']
    ));

    $recupData = $req->fetch();
    $data = array('successForm','Modifications enregistrés', $recupData['url_image'], $recupData['prenom'], $recupData['email'], $recupData['recevoir_email']);
}

header("Content-Type: application/json");
echo json_encode($data);

?>