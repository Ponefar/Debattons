<?php ob_start();

require '../inc/header.php';

if(isset($_SESSION['id'])){

    if(!$_SESSION['admin']){
        return header('Location: ../index.php');
    }

}else{
    return header('Location: ../index.php');
}

if((!isset($_POST['id'])) || empty($_POST['id'])){
    return header('Location: ../index.php');
}

$recup_article = $bdd->query('SELECT * FROM verification WHERE id =' . $_POST['id']);

while($a = $recup_article->fetch()){
    $titre = $a['titre'];
    $contenu = $a['contenu'];
    $date_time_publi = $a['date_time_publi'];
    $url_image = $a['url_image'];
    $categorie = $a['categorie'];
    $signature_article = $a['signature_article'];
    $id_envoyeur = $a['id_envoyeur'];

};

$dossierImgArticleVerif = '../img_article/imagesArticlesVerifications/';
$dossierImgArticles = '../img_article/imagesArticles/';

rename ($dossierImgArticleVerif . $url_image, $dossierImgArticles . $url_image);    

$insert = $bdd->prepare('INSERT INTO article(titre, contenu, date_time_publi, url_image, categorie, signature_article, id_envoyeur) VALUES (?,?,NOW(),?,?,?, ?) ');
$insert->execute(array(
    $titre,
    nl2br($contenu),
    $url_image,
    $categorie,
    $signature_article,
    $id_envoyeur
));

$req = $bdd->query('SELECT * FROM article ORDER BY id DESC LIMIT 1');
$req1 = $req->fetch();
$lastArticle = $req1['id'];


$insert_verif = $bdd->prepare('UPDATE article_save SET titre = :titre, contenu = :contenu, url_image = :url_image, categorie = :categorie
,signature_article = :signature_article, heure_deleteAdd = NOW() , mail_deleteAdd = :email, id_deleteAdd = :id_deleteAdd, Etat = :etat, id_article = :idlast, modification = :modification WHERE id_article = :id ');
$insert_verif->execute(array(
    "titre" => $titre,
    "contenu" => nl2br($contenu),
    "url_image" => $url_image,
    "categorie" => $categorie,
    "signature_article" => $signature_article,
    "email" => $_SESSION['email'],
    "id_deleteAdd" => $_SESSION['id'],
    "etat" => "Add",
    "idlast" => $lastArticle,
    "id" => $_POST['id'],
    "modification" => $_SESSION['email'],
)); 

$deleteArticle = $bdd->prepare('DELETE FROM verification WHERE id = :idArticle');
$deleteArticle->execute(array(
    'idArticle' => $_POST['id']
));

$req1 = $bdd->query('SELECT * FROM users WHERE recevoir_email = "oui"');
// $a = $req1->fetch();
$req2 = $bdd->query('SELECT * FROM article ORDER BY id DESC LIMIT 1');
$b = $req2->fetch();

while($a = $req1->fetch()){

    $emailEnvoyer = "NoReply@debattons.fr";
    $envoyeA = $a['email'];

    $subject = 'Nouvel Article';

    $message = '<div style="padding:20px ;background-color: rosybrown;text-align: center;">
                    <div style="color:white;font-size: 26px;">Debattons.fr</div><br />
                    <div style="color:white;font-size: 22px;width:60%;margin:0 auto">
                        Un nouvel article a été publié : <br />
                    <a href="https://montpellier-meilleur-ville.site/Debattons/article/article.php?id=' . $b['id'] . '">
                        <button style="padding:8px 15px;background-color: #fff;border: 2px solid white;border-radius: 5px;">Lien de l\'article ICI </button>
                    </a><br>
                    <a href = "https://www.montpellier-meilleur-ville.site/Debattons/mon_compte/stopMail?token=' . $a['token'] . '">
                <button style="padding:8px 15px;background-color: white;border: 2px solid white;border-radius: 5px;">Desabonnement de la NewLetter</button></a> <br /><br>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "Reply-To:" . $emailEnvoyer . "\r\n"; 
    $headers .= "From:" . $emailEnvoyer . "\r\n";
    $headers .= "Organization: Your Email Subject\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

    // $recupResMailOui = "";
                
    // while($abc = $req1->fetch()){
    //     $recupResMailOui.= $abc['email'] . ", ";
    // }
    // $headers .= "BCC: $recupResMailOui \r\n";
                
    mail($envoyeA, $subject, $message, $headers);

    ob_end_flush();

}

?>