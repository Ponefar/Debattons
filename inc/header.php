<?php
session_start();

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
{
    $url = "https";
}
else
{
    $url = "http"; 
}  
$url .= "://"; 
$url .= $_SERVER['HTTP_HOST']; 

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Debattons - Media interactif</title>
  <!-- <link rel="stylesheet" href="./css/contact.css"> -->
  <link rel="stylesheet" href="<?php echo $url ?>/Debattons/css/css.css">
  <link rel="stylesheet" href="<?php echo $url ?>/Debattons/css/cssFont.css">
  <link rel="stylesheet" href="<?php echo $url ?>/Debattons/css/msc-style.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="icon" href="../img/d.ico" />
  <script src="<?php echo $url ?>/Debattons/js/msc-script.js"></script>
  <script src="<?php echo $url ?>/Debattons/js/fontawesome.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"
     integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
     crossorigin="anonymous">
  </script>    
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<?php 


date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

require dirname(__FILE__).'/bdd.php';

$date_atm = date("Y-m-d H:i:s");

$req = $bdd->query('SELECT * FROM users');
while($a = $req->fetch()){
  if($a['hDebanTokenPswd'] != ""){
    $date_atm_more_one_day = date('Y-m-d H:i:s', strtotime($a['hDebanTokenPswd'].' + 1 DAY'));

    if($date_atm > $date_atm_more_one_day){
      $req = $bdd->prepare('UPDATE users SET tokenResetPswd = :tokenResetPswd, hDebanTokenPswd = :hDebanTokenPswd WHERE id = :id ');
      $req->execute(array(
          "tokenResetPswd" => null,
          "hDebanTokenPswd" => null,
          "id" => $a['id']
      ));
    }
  }
}

?>

<div id="haut"></div>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v9.0" nonce="zK0dgAeb"></script>

<?php 

require dirname(__FILE__).'/function.php';

?>

<script>
  
window.addEventListener('scroll', hauteur_scrool);
function hauteur_scrool(){
    const hauteur_screen = window.scrollY
    const rien = document.querySelector('.remonter')

    if(hauteur_screen >= 150){
        rien.style.marginRight = "0px"
    }else{
        rien.style.marginRight = "-66px"
    }
}

</script>

<a class="buttonHaut" href="#haut">
  <img class="remonter" src="https://www.johanncorbelconsulting.com/13-large_default/retour-en-haut.jpg" width="50px" alt="">       
</a>

<header>

<?php
if((isset($_SESSION['id'])) && ($_SESSION['admin'])){
  ?>

  <ul class="UlClasse"><div id="adrien"><a href="<?php echo $url ?>/Debattons/index.php"><span class="digital"> Déba</span><span class="space">ttons </span> </a></div><div id="MenuBurgerCroix"><img id="burgerMenu" class="nieke" src="../img/rieeene.png" alt="" onclick="menuDeroulant()"></div>
<?php }else{
?>

<ul class="UlClasse"><div id="adrien"><a href="<?php echo $url ?>/Debattons/index.php"><span class="digital"> Déba</span><span class="space">ttons </span> </a></div><div id="MenuBurgerCroix"><img id="burgerMenu" class="nieke" src="../img/rieeene.png" alt="" onclick="menuDeroulant()"></div>


<?php
}
?>
    <div>
     <a href=""><!-- <img id="logo" src="img/moi.png" alt="zbeub">--></a>
    </div>
    <div id="noneTel">
      <?php
        if((isset($_SESSION['id'])) && ($_SESSION['admin']))
        {
      ?> 
      <a href="<?php echo $url ?>/Debattons/verification_admin/adminIndex.php"><li class="LiHeader">Articles</li></a>
      <?php
        }
        else if(isset($_SESSION['id']))
        { 
      ?>
      <a href="<?php echo $url ?>/Debattons/liste_article/index.php"><li class="LiHeader" >Articles</li></a>
      <?php
      }else { ?>
      <a href="<?php echo $url ?>/Debattons/liste_article/index.php"><li class="LiHeader">Articles</li></a>

       <?php } ?>
     
    <a href="<?php echo $url ?>/Debattons/categorie/categories.php"><li class="LiHeader">Catégories</li></a>
    <a href="<?php echo $url ?>/Debattons/publication/publier.php"><li class="LiHeader">Publier</li></a>
    <a href="<?php echo $url ?>/Debattons/contact/contact.php"><li class="LiHeader">Contact</li></a> 
    <?php if(isset($_SESSION['id'])){ ?>
      
        <li class="LiHeader">
          Mon compte  <i class='fas fa-angle-down' style='font-size:18px'></i><span class="rien moncompte"></span>
          <ul class="sous_menu">
            <a href="<?php echo $url ?>/Debattons/mon_compte/connexion.php">
              <li>
                Modifier mon compte
              </li>
            </a>

            <a href="<?php echo $url ?>/Debattons/mon_compte/notifications.php">
              <li>
                Notifications <span class="rien"></span>
              </li>
            </a>

            <a href="<?php echo $url ?>/Debattons/mon_compte/likes.php">
              <li>
                Mes likes
              </li>
            </a>

            <a href="<?php echo $url ?>/Debattons/mon_compte/commentaires.php">
              <li>
                Mes commentaires
              </li>
            </a>
            
            <a href="<?php echo $url ?>/Debattons/mon_compte/articles.php">
              <li>
                Mes articles
              </li>
            </a>

          </ul>
        </li>

      <div class="deconnection"><li class="LiHeader">Deconnexion</li></div>
      <?php if($_SESSION['admin']){ 
        
        $req = $bdd->query('SELECT id FROM verification');
        $nbArticleVerif = $req->rowCount();     
        ?>
      
        <a href="<?php echo $url ?>/Debattons/verification_admin/adminVerificationArticles.php">
          <li style="color:green" class="LiHeader">Verif articles <span class="moncompte">(<?php echo $nbArticleVerif ?>)</span></li>
        </a>
        <a href="<?php echo $url ?>/Debattons/liste_article_supp_admin/liste_des_articles.php"><li style="color:red" class="LiHeader">Articles Supp</li></a>
        <a href="<?php echo $url ?>/Debattons/admin"><li style="color:gray" class="LiHeader">Add/Del Admin</li></a>

  <?php    }
         }else{ ?>
    <a href="<?php echo $url ?>/Debattons/mon_compte/connexion.php"><li class="LiHeader">Connexion</li></a>
    <a href="<?php echo $url ?>/Debattons/mon_compte/inscription.php"><li class="LiHeader">Inscription</li></a>
    <?php }; ?>
  </div>
  </ul>
</header>
<?php if(isset($_SESSION['id'])){?>
<script>

var demobtn = document.querySelector(".deconnection");
demobtn.addEventListener("click", function() {
  mscConfirm("Se déconnecter ?",function(){
    document.location.href="<?php echo $url ?>/Debattons/mon_compte/deconnexion.php" ;
  });
})

</script>

<?php
};

$req1 = $bdd->prepare("SELECT * FROM espace_commentaire WHERE id_membres = :id_session GROUP BY id_article");
            if(isset($_SESSION['id'])){
                $compteur1 = 0 ; 
                
                $req1->execute(array(
                    "id_session" => $_SESSION['id']
                ));
                $compteur_article_avec_id_session = $req1->rowCount();
                // echo $compteur_article_avec_id_session . "<br>" ; 
                $req1 = $bdd->prepare("SELECT * FROM espace_commentaire INNER JOIN article ON espace_commentaire.id_article = article.id WHERE id_envoyeur = :id_session AND id_membres != :id_session AND comm_notif = 'oui'");
                $req1->execute(array(
                    "id_session" => $_SESSION['id']
                ));
                $compteur_article_avec_id_session = $req1->rowCount();     
                if ($compteur_article_avec_id_session != 0){
                  ?>
                  <script>
                        var recupClassRien = document.getElementsByClassName('rien');
                        for(i = 0 ; i < recupClassRien.length ; i++){
                          recupClassRien[i].innerHTML = "(<?php echo $compteur_article_avec_id_session ?>)";
                        }
                  </script>       
                  <?php
                } 
              }
                ?>