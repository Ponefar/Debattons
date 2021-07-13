<?php session_start();

require '../inc/bdd.php';
require '../inc/function.php';

date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

$date_atm = date("Y-m-d H:i:s");






if(isset($_SESSION['id'])){

    $date_time_unban = $bdd->prepare('SELECT * FROM verification WHERE emailEnvoyeur = :emailenvoyeur ORDER BY date_time_unban DESC LIMIT 1');
    $date_time_unban->execute(array(
        'emailenvoyeur' => $_SESSION['email']
    ));
    
    $zeubi = $date_time_unban->fetch();  
    $date_bdd = $zeubi['date_time_unban'];

    if(!$_SESSION['admin']){
        if($date_atm < $date_bdd){
            $data = array('delaiPubli','Vous ne pouvez pas publier pour le moment, merci d\'attendre 15 min !');
            header('Content-Type: application/json');
            echo json_encode($data);
            die();
        }
    }
}












if(isset($_POST['article_titre'])){

    function CompteurCaractere($props) {
        return strlen($_POST[$props]);
    }

    $erreur = 0;
    $extensions_autorisees_image = array('gif' ,'png' ,'jpg' ,'jpeg' ,'pdf');
    $nom_ficher = $_FILES['images']['name'];
    $extensions_image = pathinfo($nom_ficher, PATHINFO_EXTENSION);

    if (empty($_POST['article_titre']) && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Merci de remplir le champ Titre');
    }
    
    if (CompteurCaractere("article_titre") > 150 && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Titre trop long (Max 150 caractères)');
    }
    
    if(empty($_POST['article_contenu']) && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Merci de remplir le champ Contenu');
    }

    if (CompteurCaractere("article_contenu") > 40000 && $erreur == 0){
        // $errorMsg = "Contenu trop long (Max 40 000 caractères)";
        $erreur = 1;
        $data = array('errForm', 'Contenu trop long (Max 40 000 caractères)');
    }

    if($_FILES['images']['tmp_name'] == "" && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Merci d\'ajouter une photo dans le champ Image');
    }
    
    if(!in_array($extensions_image, $extensions_autorisees_image) && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Seul les format : gif ,png ,jpg ,jpeg et pdf sont acceptés');
    }
    
    if($_FILES['images']['size'] > 1000000  && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Image trop grande (1 Mo max)');
    }
    
    if(empty($_POST['signature_article']) && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Merci de remplir le champ Pseudo'); 
    }
    
    if (CompteurCaractere("signature_article") > 100 && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Merci de remplir le champ Titre'); 
    }
    
    if(empty($_POST['categorie']) && $erreur == 0){
        $erreur = 1;
        $data = array('errForm', 'Merci de remplir le champ Catégorie'); 
    }

    if($erreur != 1){
        $article_titre = htmlspecialchars($_POST['article_titre']);
        $article_contenu = htmlspecialchars($_POST['article_contenu']);
        $emailEnvoyerInsert = $_SESSION['email'];
        $id_envoyeur = $_SESSION['id'];
        $adress_ip = $_SERVER['REMOTE_ADDR'] ;
        $categorie = htmlspecialchars($_POST['categorie']);
        $signature_article = htmlspecialchars($_POST['signature_article']);
        $image = $_FILES["images"]["tmp_name"];
        $dossierImgArticle = '../img_article/imagesArticlesVerifications/';
        $randomNameImg = genererChaineAleatoire(10, $listeCar);
        $nomImageBdd = $randomNameImg . "." . $extensions_image;
        move_uploaded_file($image, $dossierImgArticle . $nomImageBdd);

        $ins = $bdd->prepare('INSERT INTO verification(titre,contenu, url_image /*, extrait*/, date_time_publi,  date_time_unban, emailEnvoyeur, categorie , signature_article, id_envoyeur) VALUES (?,?,?,/*?,*/NOW(), NOW() + INTERVAL 15 HOUR_MINUTE, ?, ?, ?, ?)');
        $ins->execute(array($article_titre, $article_contenu, $nomImageBdd/*, $extrait*/ , $emailEnvoyerInsert, $categorie, $signature_article, $id_envoyeur ));
        
        $req10 = $bdd->query('SELECT id FROM verification ORDER BY id DESC LIMIT 1');
        $lastArticle = $req10->fetch();

        $insDeux = $bdd->prepare('INSERT INTO article_save(titre,contenu, url_image /*, extrait*/, date_time_publi, emailEnvoyeur, categorie , signature_article, id_envoyeur, ip_envoyeur, id_article) VALUES (?,?,?,/*?,*/NOW(), ?, ?, ?, ?, ?, ?)');
        $insDeux->execute(array($article_titre, $article_contenu, $nomImageBdd/*, $extrait*/ , $emailEnvoyerInsert, $categorie, $signature_article, $id_envoyeur, $adress_ip, $lastArticle['id']));

        $req = $bdd->prepare('INSERT INTO article_save_users(titre,contenu, url_image /*, extrait*/, date_time_publi, emailEnvoyeur, categorie , signature_article, id_envoyeur, ip_envoyeur, id_article) VALUES (?,?,?,/*?,*/NOW(), ?, ?, ?, ?, ?, ?)');
        $req->execute(array($article_titre, $article_contenu, $nomImageBdd/*, $extrait*/ , $emailEnvoyerInsert, $categorie, $signature_article, $id_envoyeur, $adress_ip, $lastArticle['id']));

        $emailEnvoyer = "NouveauArticleVerification@debattons.fr";
        $to      = 'adrienwolf1@gmail.com';
        $subject = 'Nouveau article';
        $message = '<div style="padding:20px;background-color: rosybrown;text-align: left;font-size:24px;color:white">
        Nouveau article dans verification</div>';
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "Reply-To:" . $emailEnvoyer . "\r\n"; 
        $headers .= "From:" . $emailEnvoyer . "\r\n";
        $headers .= "Organization: Your Email Subject\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

        mail($to, $subject, $message, $headers);

        $data = array('successForm', 'Article publié ! '); 
    }

    header('Content-Type: application/json');
    echo json_encode($data);

}else {
    header('Location: ../index.php');
}


?>