<?php require '../inc/header.php' ?>

<script>
$("title").text("Mon compte : mes notifications - Debattons.fr")
</script>

<?php

if(!isset($_SESSION["id"])){
    return header('Location: ../index.php');
}
?>

<div class="backgroundMoncompte top">
    <div class="monCompteContainer">
        <div class="titreMonCompte">Nouvelle notification </div>
        <?php 
        $req1 = $bdd->prepare("SELECT * FROM espace_commentaire INNER JOIN article ON espace_commentaire.id_article = article.id WHERE id_envoyeur = :id_session AND id_membres != :id_session AND comm_notif = 'oui' ORDER BY date_time_publi DESC");
        $req1->execute(array(
            "id_session" => $_SESSION['id']
        ));

        $compteur_article_avec_id_session = $req1->rowCount();
        if($compteur_article_avec_id_session > 0){
            while($ab = $req1->fetch()){ 
                ?>
                <div class="moncompteNotifContainer">
                    <a href="./notifVue.php?id_comm=<?= $ab['id_comm'] ?>"><input type="submit" value="Mettre en lu" name="btnlu"></a>
                    <a href="./notifVue.php?id_comm=<?php echo $ab['id_comm']  ?>&id_article=<?php echo $ab['id_article'] ?>&redirection=yes">
                        <div class="moncompteNotifDansBoucle">
                            <?php
                            echo "<div class='titreDansBoucleNotif'>" . Ucfirst($ab['titre']) . "</div>";
                            echo "<div class='EspaceCommTexte bold'>Répondre : " . $ab['commentaire'] . '</div><br />';
                            echo '<span class="categorie_color">' . Ucfirst($ab['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G", strtotime($ab['date_time_publi']))  . " - " . strftime(/*"%A*/ "%H h %M", strtotime($ab['date_time_publi'])) . '</span>';
                            ?>

                        </div>
                    </a>
                </div>
                <?php
            }
        }
        if($compteur_article_avec_id_session == 0){
            echo "<div class='MsgAucuneNotif'>Aucune notification !</div>";
        }
        ?>  
    </div>
</div>


<div class="backgroundMoncompte bottom">
    <div class="monCompteContainer">
        <div class="titreMonCompte">Ancienne notification </div>
        <?php 
            $req1 = $bdd->prepare("SELECT * FROM espace_commentaire INNER JOIN article ON espace_commentaire.id_article = article.id WHERE id_envoyeur = :id_session AND id_membres != :id_session AND comm_notif = 'non' ORDER BY date_time_publi DESC");
            $req1->execute(array(
            "id_session" => $_SESSION['id']
        ));

        $compteur_article_avec_id_session = $req1->rowCount();
        if($compteur_article_avec_id_session > 0){
            while($ab = $req1->fetch()){ 
                ?>
                <div class="moncompteNotifContainer">
                <a href="./notifNonVue.php?id_comm=<?= $ab['id_comm'] ?>"><input type="submit" value="Non lu" name="btnlu"></a>
                    <a href="./notifVue.php?id_comm=<?php echo $ab['id_comm']  ?>&id_article=<?php echo $ab['id_article'] ?>&redirection=yes">
                        <div class="moncompteNotifDansBoucle">
                            <?php
                            echo "<div class='titreDansBoucleNotif'>" . Ucfirst($ab['titre']) . "</div>";
                            echo "<div class='EspaceCommTexte'>Répondre : " . $ab['commentaire'] . '</div><br />';
                            echo '<span class="categorie_color">' . Ucfirst($ab['categorie'])  . "</span> &#8226;  <span class='style_date_time_publi'>" . strftime(/*"%A*/ "%d %B %G", strtotime($ab['date_time_publi']))  . " - " . strftime(/*"%A*/ "%H h %M", strtotime($ab['date_time_publi'])) . '</span>';

                            ?>
                        </div>
                    </a>
                </div>
            <?php
            }
        }

        if($compteur_article_avec_id_session == 0){
            echo "<div class='MsgAucuneNotif'>Aucune notification !</div>";
        }
        ?>  

    </div>
</div>

<?php require '../inc/footer.php' ?>