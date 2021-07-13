<?php require '../inc/header.php' ?>

<script>
 $("title").text("Liste des articles - Debattons.fr")
</script>

<?php

$erreur = "";
$stopMail = "";
$erreur = "";
$numberOfThingByPage = 5;
$maReq = 'SELECT id FROM article';

$article = $bdd->query('SELECT * FROM article ORDER BY id DESC LIMIT 0,'.$numberOfThingByPage);

$req1 = $bdd->query($maReq);
$nombreArticles = $req1->rowCount();

if(isset($_GET['stopMail'])){
  $stopMail = "Vous ne recevrez plus de mail ! ";
}

?>
<div class="backGroundArticlesById">
  <div class="listeArticlesById">
    <div class="validerNoMargin"><?php echo $stopMail ?></div>
    <div class="titrePageArticleById">LISTES DES ARTICLES (<?=  $nombreArticles ?>)</div>
    <?php
    require '../liste_article_supp_admin/auto_completion.php';
    ?>

    <div class="articleDehorsBoucle">
      <?php 
      if($nombreArticles === 0){
        $erreur = "Aucun article disponible ! ";
      }

      if($nombreArticles != 0){
        while($a = $article->fetch()){  
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
                  if($ArticleVu === 1) {
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
          }
        }
        ?>

    </div>
    <br />
    <div id="resultHeight" class="erreur"><?php echo $erreur ?></div>
  </div>
</div>  
    
<?php require '../inc/footer.php' ?>

<script>
<?php

if($nombreArticles > $numberOfThingByPage){

?>

var depart = 0
var nbArticle = '<?php echo $numberOfThingByPage ?>'
var stop = false

$('#resultHeight').html('Scroll pour faire défiler les articles')

var $window = $(window), scrollLock = true;

$window.scroll(function(){
  if(!stop){
    if(scrollLock){
      if($(window).scrollTop() + $(window).height() > $(document).height() - 300){
        scrollLock = false
        depart = depart + parseInt(nbArticle) 
        InfinitScroll()
      }
    }
  }
})

function InfinitScroll(){        
    $.ajax({
        url: './chargement_article.php',
        method: 'POST',
        data : {
            depart: depart,
            nbArticle: nbArticle 
        },
        beforeSend: function() {
            $('#resultHeight').html('<img src="../img/loading2.svg" />')
        },
        success: function(data){
            if(data[0] === 'stop'){
                $('#resultHeight').html(data[1])
                stop = true
            }else{
                window.setTimeout(() => {
                    $('#resultHeight').html('')
                    $('.articleDehorsBoucle').append(data)
                    scrollLock = true
                }, 500);
            }
        }
    })
}

<?php
}
?>

</script>
