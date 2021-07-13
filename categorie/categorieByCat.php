<?php require '../inc/header.php'; 

$categorie = $_GET['categorie'];

$numberOfThingByPage = 15;
$maReq = 'SELECT * FROM article WHERE categorie = ?';
$array = [$categorie];
$result_array_function = pageArrayEtCompteur($numberOfThingByPage, $maReq, $array);

?>

<script>
 $("title").text("Liste des articles : <?php echo  $categorie ?> - Debattons.fr")
</script>

<?php

$depart = ($result_array_function[1]-1)*$numberOfThingByPage;

if(isset($_GET['page'])){
   if($_GET['page'] > $result_array_function[0]){
       return header('Location: ./categorieByCat.php?categorie=' . $_GET['categorie']);
   }
}

$article = $bdd->prepare('SELECT * FROM article WHERE categorie = ? ORDER BY id DESC LIMIT '.$depart.','.$numberOfThingByPage);
$article->execute(array($categorie));

$req2 = $bdd->prepare($maReq);
$req2->execute(array($categorie));
$nombreArticles = $req2->rowCount();

$req1 = $bdd->prepare('SELECT * FROM article WHERE categorie = :categorie');
$req1->execute(array(
   "categorie" => $categorie
));
$nbcategorie = $req1->rowCount();

if(isset($categorie)){
   if(empty($categorie) || ($nbcategorie === 0)){
      return header('Location: ./article/article.php');
   }
}else{
   return header('Location: ./article/article.php');
}
?>
<div class="backGroundAdminArticlesById">
    <div class="listeAdminArticlesById">
      <div class="titreMonCompte">
         <?php
         echo ucFirst($categorie) . " (". $nombreArticles . ")";
         ?>
      </div>
      <div class="categrieDehorsBoucle">
         <?php
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
                           <?php echo $compteur_all ; ?> 
                           <i class='fas fa-eye'   style='font-size:24px'></i>
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

         <div class="pagination">
            <?php
            for($i=1;$i<=$result_array_function[0];$i++){
               if($i == $result_array_function[1]) {
                  echo '<span class="ASouligneDeux">' . $i.' </span>';

               }else{
               echo '<span class="pageSouligne"><a class="ASouligne" href="categorieByCat.php?categorie=' .$categorie .'&page='.$i.'">'.$i.'</a> </span>';
               }
            }
            ?>
            
         </div>
      </div>
   </div>
</div>

<?php require '../inc/footer.php' ?>
