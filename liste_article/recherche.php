<?php session_start();
require '../inc/bdd.php';

if (!isset($_GET['activite_rechercher']) || empty(trim($_GET['activite_rechercher']))) {
  return 0;
}

$chaine = $_GET['activite_rechercher'];
$chaine = "%" . $chaine . "%";
$req = "SELECT * FROM article WHERE titre LIKE :titre OR signature_article LIKE :titre";
$sth = $bdd->prepare($req);
$sth->execute(array(
  "titre" => $chaine));  

while($a = $sth->fetch()){
  ?>

  <div class="alladminArticleAffichageDansRecherche">
    <?php
    if((isset($_SESSION['id'])) && ($_SESSION['admin'])){ ?>
    
      <a class="Supprimer SupprimerAutocmpletion" href="./article_auto_completion_del.php?id=<?php echo $a['id']  ?>" onclick="if(confirm('Supprimer l\'article ?')){}else{return false;}">
        Supprimer
      </a>
    <?php 
    }; 
    ?>

    <a href="../article/article.php?id=<?php echo $a['id'];?>">

    <div class="adminArticleAffichageDansRecherche">
      <?php
      $id_article = $a['id'] ;

      $req = $bdd->prepare('SELECT DISTINCT adress_ip FROM vue_article WHERE id_article = :id_article');
      $req->execute(array(
        'id_article' => $a['id']
      ));

      $compteur_all = $req->rowCount();    
      ?>
      <div class="NombreVuEtIcon">
          <?php echo $compteur_all ; ?> 
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
      echo '<span class="categorie_color">' . Ucfirst($a['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G", strtotime($a['date_time_publi'])) . '</span> &#8226;  <span class="style_signature">' . ucFirst($a['signature_article']) . '</span>';
        ?>
    </div>
  </a>
</div>

<hr>

<?php  
}
?>