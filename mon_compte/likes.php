<?php require '../inc/header.php' ?>

<script>
$("title").text("Mon compte : mes likes - Debattons.fr")
</script>

<?php

if(!isset($_SESSION['id'])){
    return header('Location: ../index.php');
}

$numberOfThingByPage = 10;
$maReq = 'SELECT * FROM likes INNER JOIN article ON likes.id_article = article.id WHERE id_membre =' . $_SESSION['id'];
$result_array_function = pageEtCompteur($numberOfThingByPage, $maReq);
$depart = ($result_array_function[1]-1)*$numberOfThingByPage;

if(isset($_GET['page'])){
    if($_GET['page'] > $result_array_function[0]){
        header('Location: ./likes.php');
    }
}

$likestotales =  $result_array_function[2];
        
$likeTotalesReq = $bdd->prepare('SELECT * FROM likes INNER JOIN article ON likes.id_article = article.id WHERE id_membre = :id ORDER BY id_article DESC LIMIT '.$depart.','.$numberOfThingByPage);
$likeTotalesReq->execute(array(
    "id" => $_SESSION['id']
));
    
?>
<div class="backgroundMoncompte">
    <div class="monCompteContainer">
        <div class="titreMonCompte">Mes articles likés (<?= $likestotales   ?>)</div>
        <?php
        if($likestotales === 0){
            echo "<div class='erreur'>Aucun article aimé pour le moment ! </div>";
        }
        while($ab = $likeTotalesReq->fetch()){
            ?>
            <a href="../article/article.php?id=<?php echo $ab['id']?>">  
                <div class="dansBoucleMonCompte">
                    <?php
                    echo "<div class='titreDansBoucle'>" . Ucfirst($ab['titre']) . "</div>";
                    echo '<span class="categorie_color">' . Ucfirst($ab['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G", strtotime($ab['date_time_publi'])) . '</span>';
                    ?>
                </div>
            </a>
           <hr>
        <?php
        }
        ?>

        <div class="pagination">
            <?php
            for($i=1;$i<=$result_array_function[0];$i++){
                if($i == $result_array_function[1]) {
                    echo '<span class="ASouligneDeux">' . $i.' </span>';
                }else{
                echo '<span class="pageSouligne"><a class="ASouligne" href="./likes.php?page='.$i.'">'.$i.'</a> </span>';
                }
            }
            ?>
        </div>
    </div>
</div>
<?php require '../inc/footer.php' ?>