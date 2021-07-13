<?php 

ob_start();

require '../inc/header.php' ?>

<script>
$("title").text("Liste vérification d'articles - Debattons.fr")
</script>

<?php


$erreur = "";
$numberOfThingByPage = 5;

$articleVerif = $bdd->query("SELECT * FROM verification ORDER BY id ASC LIMIT 0 , $numberOfThingByPage");

if(isset($_SESSION['id'])){
  if(!$_SESSION['admin']){
    return header('Location: ../index.php');
  }
}else{
  return header('Location: ../index.php');
}

$article = $bdd->query('SELECT * FROM verification ORDER BY date_time_publi ASC');
$compteur = $article->rowCount();

?>

<div class="backGroundAdminArticlesById">
  <div class="listeAdminArticlesById">
    <div class="titre_verif_article">Vérification articles (<?php echo $compteur ?>)</div>    
    <div class="adminArticleDehorsBoucle">
      <?php 
      if($compteur == 0 ){ 
        $erreur = "Aucun article disponible"
      ?>
      
        <div id="resultHeight" class="erreur"><?php echo $erreur ?></div>
      <?php 

      };
      if($compteur != 0 ){
        while($a = $articleVerif->fetch()) { 
      ?>

          <div class="alladminArticleAffichageDansBoucle">
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
                  $chaine = substr($chaine, 0, $espace)."<span style='color:blue'> ... lire plus</span>";
                }
                echo '<div class="EspaceCommTexte">' . $chaine.'</div><br><br>';           
                echo '<span class="categorie_color">' . Ucfirst($a['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G à %Hh%M", strtotime($a['date_time_publi'])) .  '</span>  &#8226; <span class="EspaceCommTexteVerif">' . UcFirst($a['emailEnvoyeur']) . '</span>';
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
    
function Del_article(param){
  $.ajax({
      url : "../verif_article/verif_article_del.php",
      method : "POST",
      data : {
          id : param,
      },
      beforeSend: function() {            
          $('.classAddDel').each(function(){
              $(this).html('<button class="SuppArticle" ><img src="../img/loading2.svg" /></button>') 
              $('#AclassAddDel').removeAttr('href')
          });
      },
      success : function(data){
        location.reload()
      }
  })
};

function Add_article(param){
  $.ajax({
      url : "../verif_article/verif_article_add.php",
      method : "POST",
      data : {
          id : param,
      },
      beforeSend: function() {            
          $('.classAddDel').each(function(){
              $(this).html('<button class="SuppArticle" ><img src="../img/loading2.svg" /></button>') 
              $('#AclassAddDel').removeAttr('href')
          });
      },
      success : function(data){
        location.reload()
      }
  })
};

<?php

if($compteur > $numberOfThingByPage){

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
        url: './chargement_verification_article.php',
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
