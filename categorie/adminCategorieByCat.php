<?php 

ob_start();

require '../inc/header.php'; 

if(isset($_SESSION['id'])){
   
   if (!$_SESSION['admin']){
      return header('Location: ../index.php');
   }

}else{
   return header('Location: ../index.php');

}

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

if(isset($_GET['page'])){

   if($_GET['page'] > $result_array_function[0]){
      return header('Location: ./adminCategorieByCat.php?categorie=' . $_GET['categorie']);
   }

}

$depart = ($result_array_function[1]-1)*$numberOfThingByPage;

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
      return header('Location: ../categorie/categories.php');
   }
}else{
   return header('Location: ../categorie/categories.php');
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
         while($a = $article->fetch()) {  
         ?>

            <div class="alladminArticleAffichageDansBoucle">

               <a class="classDelete" onclick="if(confirm('Supprimer l\'article ?')){return Del_article(<?php echo $a['id']; ?>)}else{return false}">
                  <button class="Supprimer">Supprimer</button>
               </a>

               <a href="../article/article.php?id=<?php echo $a['id']?>"> 
                  <div class="adminArticleAffichageDansBoucle">

                     <?php
                     $id_article = $a['id'] ;
                     $MaReq = 'SELECT /* DISTINCT adress_ip*/ * FROM vue_article WHERE id_article = :id_article'; // le disctinct -> 1 seule vue pour 1 adresse ip , sans le dictint, chaque compte est indépendant de son adress ip

                     $array = ["id_article" => $id_article];
                     $compteur_all = compteurReqPrepareArray($MaReq, $array);
                     ?>

                     <div class="NombreVuEtIcon">
                        <?php echo $compteur_all ; ?> 
                        <i class='fas fa-eye' style='font-size:24px'></i>
                     </div>
                     <div class="ArticleVu">
                        <div><img src="../img/articleVu.svg" alt="" width="15px" /> Déjà Lu</div>
                    </div> 

                     <?php 
                     echo '<div class="titreDansBoucle">' .$a['titre'] . '</div>'; 
                     $chaine = $a['contenu'];
                     $max= 420;
                     $chaine = strip_tags($chaine);
            
                     if (strlen($chaine) >= $max){
                     $chaine = substr($chaine, 0, $max);
                     $espace = strrpos($chaine, " ");
                     $chaine = substr($chaine, 0, $espace)."<span style='color:blue'> ... lire plus</span>";
                     }
               
                     echo '<div class="EspaceCommTexte">' . $chaine.'</div><br><br>';           
                     echo '<span class="categorie_color">' . Ucfirst($a['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G", strtotime($a['date_time_publi'])) . '</span> &#8226;  <span class="style_signature">' . ucFirst($a['signature_article']) . '</span>';
                        ?>
                  </div>
               </a>
            </div>
         <?php
         };       
         ?>
         <div class="pagination">
            <?php
            for($i=1;$i<=$result_array_function[0];$i++){
               if($i == $result_array_function[1]) {
                  echo '<span class="ASouligneDeux">' . $i.' </span>';

               }else{
               echo '<span class="pageSouligne"><a class="ASouligne" href="adminCategorieByCat.php?categorie=' .$categorie .'&page='.$i.'">'.$i.'</a> </span>';
               }
            }
            ?>
         </div>
      </div>
   </div>
</div>

<script>
    
  function Del_article(param){
    $.ajax({
        url : "../article/article_del.php",
        method : "POST",
        data : {
            id : param,
        },
        beforeSend: function() {            
            $('.classDelete').each(function(){
                $(this).html('<button class="SuppArticle" disabled="disabled"><img src="../img/loading2.svg" /></button>') 
            });
        },
        success : function(data){
          location.reload()
        }
    })
  };

</script>

<?php require '../inc/footer.php'; 
ob_end_flush();
?>
