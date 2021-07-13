<?php 

@$mail = nl2br(htmlspecialchars($_POST['mail']));
@$objet = nl2br(htmlspecialchars($_POST['objet']));
@$message = nl2br(htmlspecialchars($_POST['message']));

$error = 0 ;

if(!isset($_POST['mail'])){
    return header('Location: ../../index.php');
}

if(empty($mail) || empty($objet) || empty($message)){
    $error = 1;
    $data = array('errForm', 'Merci de remplir tous les champs');
}

if(!preg_match("#^[a-z0-9_-]+((\.[a-z0-9_-]+){1,})?@[a-z0-9_-]+((\.[a-z0-9_-]+){1,})?\.[a-z]{2,}$#i",$mail) && $error == 0){
    $error = 1;
    $data = array('errForm', 'E-mail format non valide');
} 

if(empty($_POST['g-recaptcha-response']) && $error == 0){
    $error = 1;
    $data = array('errForm', 'Merci de cocher le reCaptcha');
}

if(!empty($_POST['g-recaptcha-response'])){
    $secret_key = '6LdoIawaAAAAAI-pvHXPQQsGXAGtsGrb50SkJQ5S';

    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);

    $response_data = json_decode($response);

    if(!$response_data->success && $error == 0){

        $error = 1;
        $data = array('errForm', 'La vérification du captcha a échoué');
    }
}

if($error === 0){
    $emailEnvoyer = "contact@debattons.fr";
    $to      = "adrienwolf1@gmail.com";
    $subject = "Contact - Debattons";
    $message = 
        '<div style="padding:20px;background-color: rosybrown;text-align: left;font-size:24px;color:white">
        Email envoyeur : ' . $mail . "<br /><br />" 
        ."Sujet : " . $objet . '<br /><br />'
        ."Message : " .  $message . '<br />
        </div>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "Reply-To:" . $emailEnvoyer . "\r\n"; 
    $headers .= "From:" . $emailEnvoyer . "\r\n";
    $headers .= "Organization: Your Email Subject\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

    mail($to, $subject, $message, $headers);
    $data = array('successForm', 'Mail envoyé');

}

header('Content-Type: application/json'); 
echo json_encode($data);

?>