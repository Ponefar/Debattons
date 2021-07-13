<?php session_start();

if(!isset($_SESSION['id'])){
    return header('Location: ../index.php');
}

require '../inc/bdd.php';


if(isset($_POST['TexteCommentaire']) || isset($_POST['id'])){
    $error = 0;

    if(empty($_POST['TexteCommentaire'])){
        $error = 1;
        $data = array('errForm', 'Commentaire Vide');
    }
    
    if(strlen($_POST['TexteCommentaire']) >= 1000 && $error == 0){
        $error = 1;
        $data = array("errForm", "Maximum 1000 charactères dans un commentaire");
    }
    
    if($error === 0){           

        $recupCommentaire = $bdd->prepare('INSERT INTO espace_commentaire(commentaire,date_time_publi_commentaire, id_article, id_membres, date_time_comm_unban, comm_notif, adress_ip) VALUES (:commentaire, NOW(), :id_article, :id_membres, NOW() + INTERVAL 5 HOUR_MINUTE, "oui", :adress_ip)');
        $recupCommentaire->execute(array(
            'commentaire' => nl2br(htmlspecialchars($_POST['TexteCommentaire'])),
            'id_article' => $_POST['id'],
            'id_membres' => $_SESSION['id'],
            'adress_ip' => $_POST['adress_ip'],
        ));

        $recupCommentaire = $bdd->prepare('INSERT INTO espace_comm_save(commentaire,date_time_publi_commentaire, id_article, id_membres, date_time_comm_unban, adress_ip) VALUES (:commentaire, NOW(), :id_article, :id_membres, NOW() + INTERVAL 5 HOUR_MINUTE, :adress_ip)');
        $recupCommentaire->execute(array(
            'commentaire' => nl2br(htmlspecialchars($_POST['TexteCommentaire'])),
            'id_article' => $_POST['id'],
            'id_membres' => $_SESSION['id'],
            'adress_ip' => $_POST['adress_ip'],
        ));   

        $req = $bdd->prepare('SELECT * FROM users INNER JOIN espace_commentaire ON users.id = espace_commentaire.id_membres  
        WHERE id_membres = :id_membre AND 
        id_article = :id_article ORDER BY id_comm DESC LIMIT 1
        ');
        $req->execute(array(
            "id_membre" => $_SESSION['id'],
            "id_article" => $_POST['id']
        ));

        $comm = $req->fetch();

        $req1 = $bdd->prepare('SELECT * FROM espace_commentaire WHERE id_article =' .$_POST['id'] );
        $req1->execute();
        $compteur = $req1->rowCount();

        
        if($_SESSION['admin']){
            $result = '<div class="VerifborderAllAdmin">
                <a onclick="if(confirm(`Supprimer le commentaire ?`)){return Del_comm(' . $_POST['id'] . ',' . $comm['id_comm'] . ')}else{return false}">
                    <button class="Supprimer">Supprimer</button>
                </a>

                <div class="divCommentaire">
                    <div><img class="imgUserComm" src="../img_article/imagesUsers/' . $comm['url_image']. '" alt=""></div>
                    <li class="Li_comm" >                                    
                        <div class="EspaceCommTexte alignLeft">' . ucFirst($comm['commentaire']) . '</div><br /> 
                        <span class="EspaceCommUsers">' . ucFirst($comm['prenom']) . '</span> &bull; <span class="EspaceCommDate"> 
                        '. strftime(/*"%A*/ "%d %B %G - %Hh%M", strtotime($comm['date_time_publi_commentaire'])) . '</span>
                    </li>
                </div>
            </div>';
            $data = array("successAdmin", "Commentaire Envoyé", $result, $compteur);

        }
    
        if(!isset($_SESSION['id']) || !$_SESSION['admin']){
            $result = '<div class="divCommentaire">
            <div><img class="imgUserComm" src="../img_article/imagesUsers/' .  $comm["url_image"] . '" alt=""></div>
            <li class="Li_comm" >
                <div class="EspaceCommTexte alignLeft">' . ucFirst($comm["commentaire"]) . '</div><br />
                <span class="EspaceCommUsers">' . ucFirst($comm["prenom"]) . '</span> &bull; <span class="EspaceCommDate">'
                . strftime(/*"%A*/ "%d %B %G - %Hh%M", strtotime($comm["date_time_publi_commentaire"])) . '</span>
            </li>
            <hr class="hr_sous_comm">
        </div>' ;
        $data = array("successUsers", "Commentaire Envoyé", $result, $compteur);

        } 
    }

    header("Content-Type: application/json");
    echo json_encode($data);


}else {
    return header('Location: ../index.php');
}


?>