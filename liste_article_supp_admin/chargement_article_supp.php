<?php

require '../inc/bdd.php' ;
require '../inc/function.php';

$article = $bdd->query("SELECT * FROM article_save WHERE etat = 'del' ORDER BY id DESC LIMIT ". $_POST['depart'] . "," . $_POST['nbArticle']);

$req = $bdd->query('SELECT * FROM article_save WHERE etat = "del"');
$compteurArticle = $req->rowCount();

if($compteurArticle <= $_POST['depart']){
    $data = array('stop','Plus d\'articles disponibles !');
    header("Content-Type: application/json"); 
    echo json_encode($data);
} 

while($a = $article->fetch()) { 
?>
<div class="alladminArticleAffichageDansBoucle">
    <a href="./article.php?id=<?php echo $a['id']?>"> 
        <div class="adminArticleAffichageDansBoucle">
        <?php
        echo '<div class="titreDansBoucle">' .$a['titre'] .'</div>';
        $chaine = $a['contenu'];
        $max= 420;
        $chaine = strip_tags($chaine);
        if(strlen($chaine) >= $max){
            $chaine = substr($chaine, 0, $max);
            $espace = strrpos($chaine, " ");
            $chaine = substr($chaine, 0, $espace)." ...";
        }
        echo "<span class='style_date_time_publi font_style_None'>Publié le " . strftime(/*"%A*/ "%d %B %G à %Hh%M", strtotime($a['date_time_publi'])) .  '  &#8226; ' . UcFirst($a['emailEnvoyeur']) . '</span><br /><br />';
        echo "<span class='style_date_time_delete font_style_None'>Supprimé le " . strftime(/*"%A*/ "%d %B %G à %Hh%M", strtotime($a['heure_deleteAdd'])) .  '  &#8226; ' . UcFirst($a['mail_deleteAdd']) . '</span>';
        ?>

        </div>
    </a>
</div>
<?php
};       
?>
