<?php require '../inc/header.php' ?>


<script>
$("title").text("Liste des articles supprimés - Debattons.fr")
</script>

<?php

$numberOfThingByPage = 5 ;

if(isset($_SESSION['id'])){
  if(!$_SESSION['admin']){
    return header('Location: ../index.php');
  }
}else{
  return header('Location: ../index.php');
}
$listeArticleSupp = $bdd->query("SELECT * FROM article_save WHERE Etat='del' ORDER BY heure_DeleteAdd DESC LIMIT 0 , $numberOfThingByPage");

$article = $bdd->query('SELECT * FROM article_save WHERE Etat="del"');
$compteur = $article->rowCount();

?>

<div class="backGroundAdminArticlesById">
  <div class="listeAdminArticlesById">
    <div class="titre_verif_article" style="color:red">Liste des articles supprimés (<?php echo $compteur ?>)</div>   
    <?php
    require './auto_completion.php';
    ?> 
    <div class="adminArticleDehorsBoucle">
      <?php 
      if($compteur == 0 ){ 
      ?>
      
        <div class="erreur">Aucun article dans la liste !</div><br /> 

      <?php 
      };

      if($compteur != 0 ){
        while($a = $listeArticleSupp->fetch()) { 
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
      };
      ?>

    </div>
    <div id="resultHeight" class="erreur"></div>
  </div>
</div>

<?php require '../inc/footer.php' ?>

<script>

<?php

if($compteur > $numberOfThingByPage){

?>

var depart = 0
var nbArticle = '<?php echo $numberOfThingByPage ?>'
var stop = false

$('#resultHeight').html('Scroll pour faire défiler les articles')

var $window = $(window), scrollLock = true
;
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
        url: './chargement_article_supp.php',
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
                    $('.adminArticleDehorsBoucle').append(data)
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