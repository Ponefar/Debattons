<?php require '../inc/header.php' ?>

<script>
$("title").text("Mon compte : mes commentaires - Debattons.fr")
</script>

<?php


if(!isset($_SESSION['id'])){
    return header('Location: ../index.php ');
}

$numberOfThingByPage = 10;
$maReq = 'SELECT * FROM espace_commentaire INNER JOIN article ON espace_commentaire.id_article = article.id WHERE id_membres =' . $_SESSION['id'];

$result_array_function = pageEtCompteur($numberOfThingByPage, $maReq);
$depart = ($result_array_function[1]-1)*$numberOfThingByPage;
if(isset($_GET['page'])){
    if($_GET['page'] > $result_array_function[0]){
        header('Location: ./commentaires.php');
    }
}
$likestotales =  $result_array_function[2];
        
$likeTotalesReq = $bdd->prepare('SELECT * FROM espace_commentaire INNER JOIN article ON espace_commentaire.id_article = article.id WHERE id_membres = :id ORDER BY id_article DESC LIMIT '.$depart.','.$numberOfThingByPage);
$likeTotalesReq->execute(array(
    "id" => $_SESSION['id']
));
?>

<div class="backgroundMoncompte">
    <div class="monCompteContainer">
        <div class="titreMonCompte">Tous mes commentaires (<?= $likestotales   ?>)</div>
        <?php
    if($likestotales === 0){
        echo "<div class='erreur'>Aucun commentaire pour le moment ! </div>";
    }
        while($ab = $likeTotalesReq->fetch()){
            ?>
            <a href="../article/article.php?id=<?php echo $ab['id']?>">  
                <div class="dansBoucleMonCompte">
                    <?php
                    echo "<div class='titreDansBoucle'>" . Ucfirst($ab['titre']) . "</div>";
                    echo '<div class="EspaceCommTexte">Mon commentaire : '. Ucfirst($ab['commentaire']) . '</div><br />';
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
                echo '<span class="pageSouligne"><a class="ASouligne" href="./commentaires.php?page='.$i.'">'.$i.'</a> </span>';
                }
            }
            ?>
        </div>
    </div>
</div>

<?php require '../inc/footer.php' ?>
