<?php

session_start();

require '../inc/bdd.php' ;
require '../inc/function.php';

$articleVerif = $bdd->query("SELECT * FROM verification ORDER BY id ASC LIMIT ". $_POST['depart'] . "," . $_POST['nbArticle']);

$article = $bdd->query('SELECT * FROM verification');
$compteurArticle = $article->rowCount();


if($compteurArticle <= $_POST['depart']){
    $data = array('stop','Plus d\'articles disponibles !');
    header("Content-Type: application/json"); 
    echo json_encode($data);
} else {

while($a = $articleVerif->fetch()) { 
?> <div class="alladminArticleAffichageDansBoucle">
<div class="classAddDel">
  <a  onclick="if(confirm('Supprimer l\'article ?')){return Del_article(<?php echo $a['id'] ?>)}else{return false}">
    <button class="Supprimer">Supprimer</button>
  </a>

  <a onclick="if(confirm('Ajouter l\'article ?')){return Add_article(<?php echo $a['id'] ?>)}else{return false}">
    <button class="Ajouter">Ajouter</button>
  </a>
</div>

<a id="AclassAddDel" href="./verifIndex.php?id=<?php echo $a['id']?>"> 
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
    echo '<div class="EspaceCommTexte">' . $chaine.'</div><br><br>';           
    echo '<span class="categorie_color">' . Ucfirst($a['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G à %Hh%M", strtotime($a['date_time_publi'])) .  '</span>  &#8226; <span class="EspaceCommTexteVerif">' . UcFirst($a['emailEnvoyeur']) . '</span>';
    ?>

  </div>
</a>
</div>
<?php
}}   
?>
