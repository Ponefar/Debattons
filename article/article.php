<?php 
ob_start();

require '../inc/header.php'; 

if(!isset($_GET['id']) || empty($_GET['id'])){
    return header('Location: ../index.php');
}

$erreur = "";
$success = "";
$btnSubmit = "";
$color_gray = "rgb(160, 160, 160)";


$id_article = $_GET['id'] ;
$adress_ip = $_SERVER['REMOTE_ADDR'];

if(isset($_SESSION['id'])){
    $email_membre = $_SESSION['email'];
}else{
    $email_membre = "Non connecte";
}

$req1 = $bdd->prepare('SELECT * FROM espace_commentaire WHERE id_article =' .$_GET['id'] );
$req1->execute();
$compteur = $req1->rowCount();
if(isset($_SESSION['id'])){
    $MaReq = 'SELECT * FROM vue_article WHERE  id_article = :id_article AND email_membre = :session_email';
    $array = ["id_article" => $id_article , "session_email" => $_SESSION['email']];
    $adress_ip = 'IP : ' . $adress_ip;
}else{
    $MaReq = 'SELECT * FROM vue_article WHERE adress_ip = :adress_ip AND id_article = :id_article';
    $array = ["adress_ip" => $adress_ip , "id_article" => $id_article];
}

$compteur_ip = compteurReqPrepareArray($MaReq, $array);

if($compteur_ip == 0){
    $maReq = 'INSERT INTO vue_article(id_article, email_membre, adress_ip) VALUES (? , ? , ? )';
    $array = [$id_article, $email_membre, $adress_ip];
    ReqPrepareArray($maReq, $array);
}


$MaReq = 'SELECT /* DISTINCT adress_ip*/ * FROM vue_article WHERE id_article = :id_article'; // le disctinct -> 1 seule vue pour 1 adresse ip , sans le dictint, chaque compte est indépendant de son adress ip
$array = ["id_article" => $id_article];
$compteur_all = compteurReqPrepareArray($MaReq, $array);

if(isset($_SESSION['id'])){
    $date_atm = date("Y-m-d H:i:s");

    $date_time_unban = $bdd->prepare('SELECT * FROM espace_commentaire WHERE id_membres = :id_membres ORDER BY date_time_comm_unban DESC LIMIT 1');
    $date_time_unban->execute(array(
        'id_membres' => $_SESSION['id']
    ));  
    $zeubi = $date_time_unban->fetch();  
    $date_bdd = $zeubi['date_time_comm_unban'];

}
?>

<div class="liste_articleIndex">
    <div class="flex_article_widgets">
        <div class="index_php">
            <?php
            $id = htmlspecialchars($_GET['id']);
            $article = $bdd->prepare('SELECT * FROM article WHERE id = ?');
            $article->execute(array($id));

            if($article->rowCount() === 0){
                return header('Location: ../verification_admin/adminIndex.php');
            }

            if($article->rowCount() === 1){
                $article = $article->fetch();
                $titre = $article['titre'];
                $contenu = $article['contenu'];
                $url_image = $article['url_image'];
                $signature_article = $article['signature_article'];
                
                $likes = $bdd->prepare('SELECT id FROM likes WHERE id_article = ?');
                $likes->execute(array($id));
                $likes = $likes->rowCount();

                $dislikes = $bdd->prepare('SELECT id FROM dislikes WHERE id_article = ?');
                $dislikes->execute(array($id));
                $dislikes = $dislikes->rowCount();

                ?>

                <script>
                $("title").text("<?php echo $titre ?> - Debattons.fr")
                </script>

                <?php

                if(isset($_SESSION['id'])){  
                    if($_SESSION['admin']){ 
                ?>
                    
                        <a class="classDelete" onclick="if(confirm('Supprimer l\'article ?')){return Del_article(<?php echo $article['id'] ?>)}else{return false}">
                            <button class="Supprimer">Supprimer</button>
                        </a>
                <?php
                    }
                } 
                ?>
                <h1 class="titre_page_index"> <?php echo $titre ; ?></h1>
                <div class="NombreVuEtIcon">
                    <?php echo $compteur_all ; ?> 
                    <i class='fas fa-eye' style='font-size:24px'></i>
                </div>

                <div class="like_dislike">
                    <?php
                    if(isset($_SESSION['id'])){ 
                        $CliquerLiker = $bdd->prepare('SELECT COUNT(*) as NbLignes FROM `likes` WHERE id_article = :id_article AND id_membre = :id_membre');
                        $CliquerLiker->execute(array(
                            'id_article' => $_GET['id'],
                            'id_membre' => $_SESSION['id']
                        ));
                        $resultatLikes = $CliquerLiker->fetch();
            
                        if($resultatLikes['NbLignes'] == 1){
                            $image_like = like('blue');
                        }

                        if($resultatLikes['NbLignes'] == 0){
                            $image_like = like($color_gray);
                        }

                        $CliquerDisLiker = $bdd->prepare('SELECT COUNT(*) as NbLignes FROM `dislikes` WHERE id_article = :id_article AND id_membre = :id_membre');
                        $CliquerDisLiker->execute(array(
                            'id_article' => $_GET['id'],
                            'id_membre' => $_SESSION['id']
                        ));
                        $resultatDislike = $CliquerDisLiker->fetch();

                        if($resultatDislike['NbLignes'] == 1){
                            $image_dislike = dislike('red');
                        }

                        if($resultatDislike['NbLignes'] == 0){
                            $image_dislike = dislike($color_gray);
                        }
                    ?>  

                    <div class="flexNoCss">
                        <form id="likeForm"  method="POST">
                            <button id="likeAjax" class="noButtonCss"><?php echo '<span class="remonter_un_peu_like">' . $likes . "</span> " . $image_like ;?></button>
                        </form>

                        <form id="dislikeForm" method="POST">
                            <button id="dislikeAjax" class="noButtonCss"><?php echo  $image_dislike . " <span class='remonter_un_peu_dislike'> " . $dislikes  ;?></button>
                        </form>
                    </div>

                    <script>

                    var color_gray = '"rgb(160, 160, 160)"';

                    function SvgLike(color) {
                        return '<svg  version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" fill=' + color + '><path d="M53.333,224C23.936,224,0,247.936,0,277.333V448c0,29.397,23.936,53.333,53.333,53.333h64c12.011,0,23.061-4.053,32-10.795V224H53.333z" /><path d="M512,304c0-12.821-5.077-24.768-13.888-33.579c9.963-10.901,15.04-25.515,13.653-40.725c-2.496-27.115-26.923-48.363-55.637-48.363H324.352c6.528-19.819,16.981-56.149,16.981-85.333c0-46.272-39.317-85.333-64-85.333c-22.165,0-37.995,12.48-38.677,12.992c-2.517,2.027-3.989,5.099-3.989,8.341v72.341l-61.44,133.099l-2.56,1.301v228.651C188.032,475.584,210.005,480,224,480h195.819c23.232,0,43.563-15.659,48.341-37.269c2.453-11.115,1.024-22.315-3.861-32.043c15.765-7.936,26.368-24.171,26.368-42.688c0-7.552-1.728-14.784-5.013-21.333C501.419,338.731,512,322.496,512,304z"/></svg>'
                    }

                    function SvgDislike(color){
                        return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" fill=' + color + '><path d="M117.333,10.667h-64C23.936,10.667,0,34.603,0,64v170.667C0,264.064,23.936,288,53.333,288h96V21.461C140.395,14.72,129.344,10.667,117.333,10.667z"/><path d="M512,208c0-18.496-10.581-34.731-26.347-42.667c3.285-6.549,5.013-13.803,5.013-21.333c0-18.517-10.603-34.752-26.368-42.688c4.885-9.728,6.315-20.928,3.861-32.043C463.381,47.659,443.051,32,419.819,32H224c-13.995,0-35.968,4.416-53.333,12.608v228.651l2.56,1.301l61.44,133.12V480c0,3.243,1.472,6.315,3.989,8.341c0.683,0.512,16.512,12.992,38.677,12.992c24.683,0,64-39.061,64-85.333c0-29.184-10.453-65.515-16.981-85.333h131.776c28.715,0,53.141-21.248,55.637-48.363c1.387-15.211-3.691-29.824-13.653-40.725C506.923,232.768,512,220.821,512,208z"/></svg>'
                    }

                    $(function(){
                        $("#likeForm").submit(function(e){
                            e.preventDefault();
                            var t = 1
                            var id = <?php echo $_GET['id'] ?>

                            $.ajax({
                                url : "./action_like.php",
                                method : "POST",
                                data : {
                                    t : t,
                                    id : id,
                                },
                                success: function(result) { 

                                    var nombre = result.split('/');
                                    if(nombre[0] == 1){

                                        $("#likeAjax").html('<span class="remonter_un_peu_like">' + nombre[1] + '</span> ' + SvgLike(color_gray))

                                    }else if(nombre[0] == 2){
                                        $("#likeAjax").html('<span class="remonter_un_peu_like">' + nombre[1] + '</span> ' + SvgLike("blue"))
                                        $("#dislikeAjax").html(SvgDislike(color_gray) + '<span class="remonter_un_peu_dislike"> ' + nombre[2] + '</span>')
                                    }
                                }       
                            });
                        });
                    });

                    $(function(){
                        $("#dislikeForm").submit(function(e){
                            e.preventDefault();
                            var t = 2
                            var id = <?php echo $_GET['id'] ?>

                            $.ajax({
                                url : "./action_like.php",
                                method : "POST",
                                data : {
                                    t : t,
                                    id : id,
                                },
                                success: function(result) { 
                                    var nombre = result.split('/');
                                    if(nombre[0] == 3){
                                        $("#dislikeAjax").html(SvgDislike(color_gray) + '<span class="remonter_un_peu_dislike"> ' + nombre[2] + '</span>')

                                    }else if(nombre[0] == 4){
                                        $("#dislikeAjax").html(SvgDislike("red") + '<span class="remonter_un_peu_dislike"> ' + nombre[2] + '</span>')
                                        $("#likeAjax").html('<span class="remonter_un_peu_like">' + nombre[1] + '</span> ' + SvgLike(color_gray))
                                    }
                                }
                            });
                        });
                    });

                    </script>
    
                    <?php 
                    }
            }
            if(!isset($_SESSION['id'])){  
                $image_like = like($color_gray);
                $image_dislike = dislike($color_gray);
            ?>
            <div class="flexNoCss">
                <a id="like"  onclick="seconnection()">
                    <?php echo '<span class="remonter_un_peu_like">' . $likes . "</span> " . $image_like ;?>
                </a> 
                <a id="dislike" onclick="seconnection()">
                    <?php echo  $image_dislike . " <span class='remonter_un_peu_dislike'> " . $dislikes  ;?>
                </a>
            </div>
            <div id="erreur"></div>
            <?php
            }; 

            echo '<img class="imgArticle" src="../img_article/imagesArticles/' . $url_image . '"/>'; ?><br /><br /><br />

            <?php 
            echo "<p class='EspaceCommTexte contenu'>" . $contenu . '</p>'
            ?>
                

            <p>
            <br />

                <?php 
                echo "<span class='EspaceCommUsers'>" . ucFirst($signature_article).'</span> &bull; <span class="EspaceCommDate">'.strftime(/*"%A*/ "%d %B %G", strtotime($article['date_time_publi'])) . '</span>';
                
                $req = $bdd->prepare('SELECT * FROM users WHERE id = :id');
                $req->execute(array(
                    ":id" => $article['id_envoyeur']
                ));

                $user = $req->fetch();
                ?>
                </p>

                <img class="imgUserArticle" src="../img_article/imagesUsers/<?php echo $user['url_image'] ?>" alt="">

            <?php
            $afficherCommentaire = $bdd->query('SELECT * FROM users INNER JOIN espace_commentaire ON users.id = espace_commentaire.id_membres  WHERE id_article = ' . $_GET["id"] . ' ORDER BY date_time_publi_commentaire ASC '); ?>
            <?php 

            echo'<p id="comm"> Espace commentaire (<span id="compteurComm">' . $compteur .'</span>) </p>';
            ?>
            <div id="listeComm">
                <?php
                if($compteur == 0){
                    echo'<span class="EspaceCommTexte nocomm" id="noComm"> Aucun commentaire sur cet article ! Soyez le premier  <br /><br /></span>';
                }
                
                if($compteur != 0){
                    while($comm = $afficherCommentaire->fetch()) { 
                        if(isset($_SESSION['id'])){
                            if($_SESSION['admin']){
                                ?>
                                <div class="VerifborderAllAdmin">

                                    <a class="classDelete" onclick="if(confirm('Supprimer le commentaire ?')){return Del_comm(<?php echo $_GET['id'] . ',' . $comm['id_comm'] ?>)}else{return false}">
                                        <button class="Supprimer">Supprimer</button>
                                    </a>

                                    <div class="divCommentaire">
                                        <div><img class="imgUserComm" src="../img_article/imagesUsers/<?php echo $comm['url_image'] ?>" alt=""></div>
                                        <li class="Li_comm" >                                    
                                            <?php 
                                            echo "<div class='EspaceCommTexte alignLeft'>" . ucFirst($comm['commentaire']) . '</div><br />'; 
                                            echo "<span class='EspaceCommUsers'>" . ucFirst($comm['prenom']) . "</span> &bull; <span class='EspaceCommDate'>" 
                                            . strftime(/*"%A*/ "%d %B %G - %Hh%M", strtotime($comm['date_time_publi_commentaire'])) . '</span>' ; 
                                        ?>
                                        </li>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                        
                        if(!isset($_SESSION['id']) || !$_SESSION['admin']){
                            ?>
                            <div class="divCommentaire">
                                <div><img class="imgUserComm" src="../img_article/imagesUsers/<?php echo $comm['url_image'] ?>" alt=""></div>
                                <li class="Li_comm" >
                                    <?php 
                                    echo "<div class='EspaceCommTexte alignLeft'>" . ucFirst($comm['commentaire']) . '</div><br />'; 
                                    echo "<span class='EspaceCommUsers'>" . ucFirst($comm['prenom']) . "</span> &bull; <span class='EspaceCommDate'>" 
                                    . strftime(/*"%A*/ "%d %B %G - %Hh%M", strtotime($comm['date_time_publi_commentaire'])) . '</span>' ; 
                                    ?>
                                </li>
                                <hr class="hr_sous_comm">
                            </div>
                    <?php 
                        }
                    }
                }
            ?>
            </div>
            <?php
        
            if(!isset($_SESSION['id'])){
                echo '<br /><div class="erreur">Merci de vous connecter pour écrire un commentaire ! </div>';
            }

            if(isset($_SESSION['id'])){

                if(($date_atm < $date_bdd) && !$_SESSION['admin']) {
                    echo '<div class="valider">' . $success . '</div>' ;
                    echo '<br /><div class="erreur">Merci d\'attendre 5 min avant de recommenter un autre article ! </div>';
                }

                if(($date_atm > $date_bdd) || $_SESSION['admin']) {
                // else if((1 == 1) || $_SESSION['email'] == ('adrienwolf@laposte.net')) {
                    $btnSubmit = '<input id="submitComm" type="submit" value="Envoyer" name="EnvoyerCommentaire">';
                    ?>
                    <div id="nopaddingForm">
                        <form method="POST" id="formComm">
                            <!-- <textarea onKeyPress="if(event.keyCode == 13) validerForm();" type="text" placeholder="Ecrivez votre commentaire ici" name="TexteCommentaire"><?php  /* if(isset($_POST['TexteCommentaire'])){ echo $_POST['TexteCommentaire'];};*/?></textarea><br> --> 
                            <textarea onkeydown="mafunction()" type="text" id="test" placeholder="Ecrivez votre commentaire ici" name="TexteCommentaire"><?php if(isset($_POST['TexteCommentaire'])){ echo $_POST['TexteCommentaire'];};?></textarea><br>
                            <div id="here"></div>

                        <script>

                        function validerForm(){
                            document.getElementById("formmm").submit();
                        }

                        function mafunction(){
                            var enleveErreur = document.getElementById('erreur')
                            enleveErreur.innerHTML = "";

                            var enleveSuccess = document.getElementById('success')
                            enleveSuccess.innerHTML = "";
                            var aaaaaa = document.getElementById("submitComm");
                            var Rien = document.getElementById("test").value.length;
                            var here = document.getElementById("here");
                            if(Rien > 1000){
                                aaaaaa.setAttribute('disabled', 'disabled')
                                here.innerHTML = "<div class='erreur'>Limite de caractères atteint </div>"
                            }else{
                                aaaaaa.removeAttribute('disabled')
                                here.innerHTML = "<div class='valider'>" + (999 - Rien) + " charactères disponibles </div>"
                            }
                        }
                
                        </script>

                            <?php 
                            echo $btnSubmit;  
                            ?>
                        </form>                
                        <div id="erreur" class="erreur"></div>
                        <div id="success" class="valider"></div>   
                    </div>         
                <?php
                }
            }
                ?>


        <script>
    
            $("#formComm").on('submit', function(e){
                e.preventDefault();
                var id = <?php echo $_GET['id'] ?>;
                var adress_ip = '<?php echo $_SERVER['REMOTE_ADDR'] ?>';

                $.ajax({
                    url : "./verif_comm.php",
                    method : "POST",
                    data : $('#formComm').serialize() + '&id=' + id + '&adress_ip=' + adress_ip,
                    beforeSend: function() {
                        document.getElementById('test').setAttribute('disabled', 'disabled')
                        $("#erreur").html("<img class='svg' src='../img/loading2.svg' />")
                        $("#here").html("")
                        document.getElementById('submitComm').setAttribute('disabled', 'disabled')
                    },
                    success : function(data){                                  
                        if(data[0] === "errForm"){
                            $("#test").removeAttr('disabled')
                            $("#submitComm").removeAttr('disabled')

                            $("#erreur").html(data[1])
                            $("#success").html("")
                        }
                        if(data[0] === "successAdmin"){
                            
                                $("#noComm").html("")

                                $("#submitComm").removeAttr('disabled')
                                $("#test").removeAttr('disabled')

                                var a = document.createElement('div')
                                var b = document.getElementById('listeComm')
                                a.innerHTML = data[2]
                                b.appendChild(a)

                                $("#erreur").html("")
                                $("#success").html(data[1])
                                $("#test").val("")   

                                $("#compteurComm").html(data[3])
                        }
                        if(data[0] == "successUsers"){
                            
                                $("#noComm").html("")

                                $("#submitComm").removeAttr('disabled')
                                $("#test").removeAttr('disabled')

                                var a = document.createElement('div')
                                var b = document.getElementById('listeComm')
                                a.innerHTML = data[2]
                                b.appendChild(a)

                                $("#success").html(data[1])
                                $("#erreur").html("Merci d\'attendre 5 min avant de recommenter un autre article !")
                                $("#formComm").html("")  
                                
                                $("#compteurComm").html(data[3])
                        }
                    }
                });
            })

        </script>

        </div>
    </div>
            
    <div class="widget_article_index">
        <div class="titre_widget">Articles les plus récents</div>
        <?php
        $recup_trois_last_article = $bdd->query('SELECT * FROM article  ORDER BY date_time_publi DESC LIMIT 3');
        while($recup_article = $recup_trois_last_article->fetch()){
            ?>
            <a href="./article/article.php?id=<?php echo $recup_article['id']?>">
                <?php   
                    echo '<div class="titre_article_widget">&#8226; ' . $recup_article['titre'] . ' -  <span class="categorie_color">' . ucFirst($recup_article['categorie']) . '</span></div>'; 
                ?>

            </a>
            <?php
            }
            ?>

        <hr class="hr_widget">
        <div class="titre_widget">Articles les plus aimés</div>
        <?php
        $recup_best_article = $bdd->query('SELECT * , COUNT(*) FROM likes INNER JOIN article ON likes.id_article = article.id GROUP  
        BY id_article ORDER BY COUNT(*) DESC LIMIT 3');
        while($recup_like = $recup_best_article->fetch()){
            ?>

            <a href="./article/article.php?id=<?php echo $recup_like['id']?>">
                <?php
                echo '<div class="titre_article_widget">&#8226; ' . $recup_like['titre'] . ' -  <span class="categorie_color">' . ucFirst($recup_like['categorie']) . '</span></div>';
                }   
                ?>

            </a>
        <hr class="hr_widget">
        <div class="fb-page" data-href="https://www.facebook.com/Debattons.fr/" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/Debattons.fr/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/Debattons.fr/">Debattons</a></blockquote></div>
        <br />
        <hr class="hr_widget">
        <a href="../publication/publier.php"><button class="publier_article_btn" >Publier un article ➜</button></a>
        </div>
    </div>
</div>

<script>

function Del_article(param){
    $.ajax({
        url : "./article_del.php",
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


function Del_comm(param, param2){
    $.ajax({
        url : "./commentaire_del.php",
        method : "POST",
        data : {
            id_article : param,
            id : param2,
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

<?php require '../inc/footer.php' ?>

