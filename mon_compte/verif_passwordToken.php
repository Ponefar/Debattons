<?php

session_start();

require '../inc/bdd.php';
require '../inc/function.php';
$error = 0;


if(isset($_POST['password_recup'])){

    if(empty($_POST['password_recup']) || (empty($_POST['password_Confirm']))){
        $error = 1;
        $data = array('errForm', 'Merci de remplir les deux Mots de passe');
    }
    
    if($_POST['password_recup'] !== $_POST['password_Confirm'] && $error === 0){
        $error = 1;
        $data = array('errForm', 'Les deux Mots de passe ne correspondent pas');
    }
    
    if($error === 0){
        $req3 = $bdd->prepare('UPDATE users SET password = :newpass WHERE tokenResetPswd = :tokenResetPswd');
        $req3->execute(array(
            "tokenResetPswd" => $_POST['tokenResetPswd'],
            "newpass" => md5($_POST['password_recup'])
        ));
        $req4 = $bdd->prepare('UPDATE users SET tokenResetPswd = NULL WHERE tokenResetPswd = :tokenResetPswd');
        $req4->execute(array(
            "tokenResetPswd" => $_POST['tokenResetPswd']
            ));

        $data = array('successForm', 'Mot de passe modifié ! ');

    }
}

header("Content-Type: application/json");
echo json_encode($data);

?>
