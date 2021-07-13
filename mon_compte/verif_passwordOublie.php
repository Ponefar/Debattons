<?php

session_start();

date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

require '../inc/bdd.php';
require '../inc/function.php';
$error = 0;

$date_atm = date("Y-m-d H:i:s");

$tokenResetPswd = genererChaineAleatoire(80, $listeCar);

if(!isset($_POST['mail_recup'])){
    header('Location: ../index.php');
}


if(empty($_POST['mail_recup'])){
    $error = 1;
    $data = array('errForm', 'Email vide');  
}

if(!filter_var($_POST['mail_recup'], FILTER_VALIDATE_EMAIL) && $error === 0 ){
    $error = 1;
    $data = array('errForm', 'Format email non valide');
}

if($error === 0){
    $req1 = $bdd->prepare('SELECT * FROM users WHERE email = ?');
    $req1->execute(array($_POST['mail_recup']));
    $compteurEmail = $req1->rowCount();
    $recupToken = $req1->fetch();

    if($compteurEmail === 0){  // Induire en erreur les potentiels hackeurs qui souhaitent savoir si une adresse mail X est enregistré sur ce site
        $error = 1;
        $data = array('succesForm', 'Email envoyé');
    }
    
    // if(strlen($recupToken['tokenResetPswd']) > 1 && $error === 0){
    if($recupToken['hDebanTokenPswd'] != null && $error === 0){
        if($date_atm < $recupToken['hDebanTokenPswd']){
            $error = 1;
            $data = array('errForm', 'Email déjà envoyé ! Merci de réessayer dans 5 min');
        }
    }
}

if($error === 0){

    $req = $bdd->prepare('UPDATE users SET tokenResetPswd = :tokenResetPswd, hDebanTokenPswd = (NOW() + INTERVAL 5 MINUTE) WHERE email = :email ');
    $req->execute(array(
        "tokenResetPswd" => $tokenResetPswd,
        "email" => $_POST['mail_recup']
    ));

    $req4 = $bdd->prepare('SELECT * FROM users WHERE email = :email');
    $req4->execute(array(
        "email" => $_POST['mail_recup']
    ));
    $compte_mail_mdp_oublie = $req4->rowCount();
    
    if($compte_mail_mdp_oublie === 1){
        $a = $req4->fetch();
        $tokenResetPswd = $a['tokenResetPswd'];
        
        $emailEnvoyer = "Ne-pas-repondre@debattons.fr";
        $to      = $_POST['mail_recup'];
        $subject = 'Recupération Mot de passe';
        $message = '<div style="padding:20px;background-color: rosybrown;text-align: center;">
        <div style="color:white;font-size: 22px;">Merci de cliquer sur le lien suivant pour ré initialiser votre mdp : </div><br /><br />
            <a href="https://montpellier-meilleur-ville.site/Debattons/mon_compte/passwordToken.php?tokenResetPswd='.$tokenResetPswd.'">
            <button style="padding:8px 15px;background-color: white;border: 2px solid white;border-radius: 5px;">Nouveau mot de passe !  ICI </button></a><br /><br />
            <br /></div>';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "Reply-To:" . $emailEnvoyer . "\r\n"; 
        $headers .= "From:" . $emailEnvoyer . "\r\n";
        $headers .= "Organization: Your Email Subject\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

        mail($to, $subject, $message, $headers); 
        $data = array('succesForm', 'Email envoyé');
    }
}            

header("Content-Type: application/json");
echo json_encode($data);

?>