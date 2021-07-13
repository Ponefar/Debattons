<?php 

require '../inc/bdd.php';
require '../inc/function.php';

@$submit = $_POST['submit'];
@$prenom = $_POST['prenom'];
@$email = $_POST['email'];
@$password1 = $_POST['password1'];
@$password2 = $_POST['password2'];
@$contact_email = $_POST['contact_email'];

$token= genererChaineAleatoire(80, $listeCar);

$erreur = 0;

if(isset($prenom)){
    $stmt = $bdd->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]); 
    $user = $stmt->fetch();

    if(empty($prenom)){
        $erreur = 1;
        $data = array("errForm", "Pseudo Vide");

    }if(strlen($prenom) > 100 && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Maximum 100 caracères pour le pseudo");

    }if(empty($email) && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "E-mail vide");

    }if(!filter_var($email, FILTER_VALIDATE_EMAIL) && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "E-mail format non valide");

    }if(strlen($email) > 100 && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Maximum 100 caracères pour l'e-mail");

    }if($user == true && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "E-mail déjà utilisé");

    }if(strlen($password1) > 100 || strlen($password2) > 100 && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Maximum 100 caracères pour les mots de passe");

    }if(empty($password1)&& $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Mot de passe vide");

    }if(empty($password2)&& $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Confirmation Mot de passe vide");

    }if(!empty($password1) && (!empty($password2) && ($password1 != $password2))&& $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Les deux Mots de passe ne correspondent pas");

    }if(empty($contact_email) && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Il faut cocher le bouton recevoir les articles ou non");

    }if(empty($_POST["acceptConditions"]) && $erreur == 0){
        $erreur = 1;
        $data = array("errForm", "Il faut cocher accepter les conditions générales d'utilisation");

    }

    if($erreur == 0){
        $req = $bdd->prepare('INSERT INTO users(prenom, email, url_image, recevoir_email, token, password, admin, verificationEmail) VALUES( :prenom, :email, :url_image,  :recevoir_email, :token, :password, :admin, :verificationEmail)');
        $req->execute(array(
            "prenom" => $prenom,
            "email" => $email,
            "url_image" => 'users.jpg',
            "password" => md5($password1),
            "recevoir_email" => $contact_email,
            "token" => $token,
            "admin" => 0,
            "verificationEmail" => 0
            ));

        $emailEnvoyer = "verification@debattons.fr";
        $to      =  $email;
        $subject = "Verification - Debattons";
        $message = 
            '<div style="padding:20px;background-color: rosybrown;text-align: left;font-size:24px;color:white">
                Cliquez sur le bouton ci-dessous pour accéder à votre compte : <br />
                <a href="https://montpellier-meilleur-ville.site/Debattons/mon_compte/verificationEmail.php?verificationEmail='.$token.'">
                    <button style="padding:8px 15px;background-color: white;border: 2px solid white;border-radius: 5px;">
                    Valider mon compte !  ICI 
                    </button>
                </a><br /><br />
            </div>';
    
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "Reply-To:" . $emailEnvoyer . "\r\n"; 
        $headers .= "From:" . $emailEnvoyer . "\r\n";
        $headers .= "Organization: Your Email Subject\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
    
        mail($to, $subject, $message, $headers);
        
        // $req = $bdd->prepare('SELECT * FROM users WHERE email = :email');
        // $req->execute(array(
        //     'email' => $email
        // ));
        // $resultat = $req->fetch();
        // $_SESSION['id'] = $resultat['id'];
        // $_SESSION['prenom'] = $resultat['prenom'];
        // $_SESSION['nom'] = $resultat['nom'];
        // $_SESSION['email'] = $resultat['email']; 
        // $_SESSION['password'] = $resultat['password'];
        // return header('Location: ./connexion.php?inscription=true');
        $data = array("successForm", "Inscription réussie. Mail envoyé, vérifiez vos spams");

    }

    header("Content-Type: application/json");
    echo json_encode($data);
}

?>

