<?php require '../inc/header.php' ?>

<script>
 $("title").text("Catégories d'article - Debattons.fr")
</script>

<div class="liste_article">
    <div class="categorieAllDiv">
        <h1 class="titre_page_art">CATÉGORIES D'ARTICLES</h1> 
        <?php
        $article = $bdd->query('SELECT DISTINCT categorie FROM article GROUP BY date_time_publi ORDER BY categorie ASC');
        $compteur_article = $article->rowCount();
        ?>  

        <div class="beforeCategorie">
            <?php
            $urlRedirect = "./categorieByCat.php";
            while($a = $article->fetch()) { 
                if(isset($_SESSION['id'])){
                    if($_SESSION['admin']){
                        $urlRedirect = "./adminCategorieByCat";
                    }
                } 
                ?>
                
                <a href="<?php echo $urlRedirect ?>?categorie=<?php echo $a['categorie']?>"> 
                    <div class="LiCategorie">
                        <?php
                        $rreq_count_article_cate = $bdd->prepare('SELECT * FROM article WHERE categorie = :categorie_atm');
                        $rreq_count_article_cate->execute(array(
                            "categorie_atm" =>$a['categorie']
                        ));
                        $count_article_cate = $rreq_count_article_cate->rowCount();
                        echo '<h2>'.htmlspecialchars($a['categorie']). " (" . $count_article_cate .')</h2>';
                        ?>
                    </div> 
                </a>
            <hr class="hr_categorie">
            <?php
            }
            ?> 
        </div>
        <br />
        <?php
        if($compteur_article === 0){
        ?>
            <div class="erreur">Aucun article disponible !</div>
        <?php
        }
        ?>
    </div> 
</div>

<?php require '../inc/footer.php' ?>
