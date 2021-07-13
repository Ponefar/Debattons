<?php

session_start();

require '../inc/bdd.php' ;
require '../inc/function.php';

$article = $bdd->query("SELECT * FROM article ORDER BY id DESC LIMIT ". $_POST['depart'] . "," . $_POST['nbArticle']);

$req = $bdd->query('SELECT * FROM article');
$compteurArticle = $req->rowCount();

if($compteurArticle <= $_POST['depart']){
    $data = array('stop','Plus d\'articles disponibles !');
    header("Content-Type: application/json"); 
    echo json_encode($data);
} 

while($a = $article->fetch()) { 
?>
    <a href="../article/article.php?id=<?php echo $a['id']?>">            
        <div class="articleAffichageDansBoucle">
            <?php
            $id_article = $a['id'] ;
            $MaReq = 'SELECT /* DISTINCT adress_ip*/ * FROM vue_article WHERE id_article = :id_article'; // le disctinct -> 1 seule vue pour 1 adresse ip , sans le dictint, chaque compte est indépendant de son adress ip

            $array = ["id_article" => $id_article];
            $compteur_all = compteurReqPrepareArray($MaReq, $array);
            ?>
            <div class="NombreVuEtIcon">
                <?php echo $compteur_all ?> 
                <i class='fas fa-eye' style='font-size:24px'></i>
            </div>
            <?php
            if(isset($_SESSION['id'])){
                $req = $bdd->prepare('SELECT * FROM vue_article WHERE id_article = :id_article AND email_membre = :session_email');
                $req->execute(array(
                    'id_article' => $id_article,
                    'session_email' => $_SESSION['email'],
                ));
                $ArticleVu = $req->rowCount();
                if($ArticleVu >= 1) {
                    ?>
                    <div class="ArticleVu">
                        <div><img src="../img/articleVu.svg" alt="" width="15px" /> Déjà Lu</div>
                    </div>
                    <?php
                }
            }              
             
            echo '<div class="titreDansBoucle">' . $a['titre'] . '</div>'; 
            $chaine = $a['contenu'];
            $max= 420;
            $chaine = strip_tags($chaine);
            if(strlen($chaine) >= $max){
                    $chaine = substr($chaine, 0, $max);
                    $espace = strrpos($chaine, " ");
                    $chaine = substr($chaine, 0, $espace)."<span style='color:blue'> ... lire plus</span>";
            }
            echo '<div class="EspaceCommTexte">' . $chaine.'</div><br><br>';  
            echo '<span class="categorie_color">' . Ucfirst($a['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G", strtotime($a['date_time_publi'])) . '</span> &#8226;  <span class="style_signature">' . ucFirst($a['signature_article']) . '</span>';
        ?>
        </div>
    </a> 
    <hr>
<?php
};       
?>
