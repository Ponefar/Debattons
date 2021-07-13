<?php session_start();
require '../inc/bdd.php';

if (!isset($_GET['activite_rechercher']) || empty(trim($_GET['activite_rechercher']))) {
  return 0;
}

$chaine = $_GET['activite_rechercher'];
$chaine = "%" . $chaine . "%";
$req = "SELECT * FROM article_save WHERE Etat = 'del' AND (mail_deleteAdd LIKE :titre OR emailEnvoyeur LIKE :titre OR titre LIKE :titre)
ORDER BY heure_DeleteAdd DESC";
$sth = $bdd->prepare($req);
$sth->execute(array(
  "titre" => $chaine,
));


while($a = $sth->fetch()){
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
}
?>