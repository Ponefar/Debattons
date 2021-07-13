<?php session_start();

require '../inc/bdd.php';

date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 


$date_atm = date("Y-m-d H:i:s");
$ip_ban = 25;
$error = 0;

$mon_ip = $_SERVER['REMOTE_ADDR'];

$req = $bdd->prepare('SELECT * FROM users WHERE email = :email');
$req->execute(array(
    'email' => $_POST['email']
));
$resultat = $req->fetch();

if(isset($_POST['email'])){

    if(empty($_POST['g-recaptcha-response']) && $error === 0){
        $error = 1;
        $data = array('errForm' , 'Merci de cocher le reCaptcha');
    }
    
    if(!empty($_POST['g-recaptcha-response']) && $error === 0){
        $secret_key = '6LdoIawaAAAAAI-pvHXPQQsGXAGtsGrb50SkJQ5S';

        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);

        $response_data = json_decode($response);

        if(!$response_data->success && $error === 0){
            $error = 1;
            $data = array('errForm' , 'La vérification du captcha a échoué');
        }

        if(empty($_POST['password1']) || empty($_POST['email']) && $error === 0){
                $error = 1;
                $data = array('errForm' , 'Email ou Mot de passe vide');
        }

        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['email']) && $error === 0){
            $error = 1;
            $data = array('errForm' , 'Mauvais format adresse mail');
        }
    }
        
    if(!$resultat && $error === 0){
        $recupdonnee = $bdd->prepare('INSERT INTO ip_ban(adresse_ip, date_actu, date_unban) VALUES (:adresse_ip , NOW(), NOW() + INTERVAL 24 HOUR)');
        $recupdonnee->execute(array(
            'adresse_ip' => $_SERVER['REMOTE_ADDR']
        ));

        $recupdonnee = $bdd->prepare('INSERT INTO ip_ban_save(adresse_ip, date_actu, date_unban, adresse_email) VALUES (:adresse_ip , NOW(), NOW() + INTERVAL 24 HOUR, :adresse_email)');
        $recupdonnee->execute(array(
            'adresse_ip' => $_SERVER['REMOTE_ADDR'],
            'adresse_email' => $_POST['email']
            ));
        $data = array('errForm' , 'Mauvais identifiant ou mot de passe');
    }

    if($resultat){
        $_POST['password1'] = md5($_POST['password1']);
    }

    if(($_POST['password1'] == $resultat['password']) && $error === 0){

        if($resultat['verificationEmail'] == 0){
            $error = 1;
            $data = array('errForm' , 'Compte non vérifié, merci de regarder vos emails');
        }else {
            $_SESSION['id'] = $resultat['id'];
            $_SESSION['prenom'] = $resultat['prenom'];
            $_SESSION['email'] = $resultat['email']; 
            $_SESSION['password'] = $resultat['password'];
            ($resultat['admin'] == 0) ? $_SESSION['admin'] = false : $_SESSION['admin'] = true;
            $data = array('successForm');
        }
    }
    
    if(($_POST['password1'] != $resultat['password']) && $error === 0){
        $recupdonnee = $bdd->prepare('INSERT INTO ip_ban(adresse_ip, date_actu, date_unban) VALUES (:adresse_ip , NOW(), NOW() + INTERVAL 24 HOUR)');
        $recupdonnee->execute(array(
            'adresse_ip' => $_SERVER['REMOTE_ADDR']
        ));

        $recupdonnee = $bdd->prepare('INSERT INTO ip_ban_save(adresse_ip, date_actu, date_unban, adresse_email) VALUES (:adresse_ip , NOW(), NOW() + INTERVAL 24 HOUR, :adresse_email)');
        $recupdonnee->execute(array(
            'adresse_ip' => $_SERVER['REMOTE_ADDR'],
            'adresse_email' => $_POST['email']

        ));
        $data = array('errForm' , 'Mauvais identifiant ou mot de passe');
    }

    $count_ip_ban = $bdd->prepare('SELECT * FROM ip_ban WHERE adresse_ip = :mon_ip ');
    $count_ip_ban->execute(array(
        'mon_ip' => $mon_ip
    ));
    $nombre_ip_ban = $count_ip_ban->rowCount();

    $time_unban = $bdd->prepare('SELECT * FROM ip_ban WHERE adresse_ip = :mon_ip ORDER BY date_unban DESC LIMIT 1');
    $time_unban->execute(array(
        'mon_ip' => $mon_ip
    ));

    while($recup_unban = $time_unban->fetch()){
        $date_unban = $recup_unban['date_unban'];
    }

    if(($nombre_ip_ban >= $ip_ban)) {
        if($date_unban > $date_atm){
            $data = array('ipBan');
        }
    } 

    header("Content-Type: application/json");
    echo json_encode($data);

}else {
    $data = array('redirection');
    header("Content-Type: application/json");
    echo json_encode($data);
}
