<?php ob_start();

require '../inc/header.php' ;


?>
<script>
$("title").text("Vérification de l'article - Debattons.fr")
</script>

<?php

$get_id = htmlspecialchars($_GET['id']);
$article = $bdd->prepare('SELECT * FROM verification WHERE id = ?');
$article->execute(array($get_id));

if(isset($_SESSION['id'])){
    if(!$_SESSION['admin']){ 
        return header('Location: ../index.php');
    }
}else {
    return header('Location: ../index.php');
}

if($article->rowCount() == 0){
    return header('Location: ./adminVerificationArticles.php');
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    return header('Location: ../index.php');
}

if($article->rowCount() == 1){
    $article = $article->fetch();
    $titre = $article['titre'];
    $contenu = $article['contenu'];
    $url_image = $article['url_image'];
    $emailEnvoyeur = $article['emailEnvoyeur'];
    $categorie = $article['categorie'];
    $signature_article = $article['signature_article'];
    $id = $article['id_envoyeur'];
}
?>

<div class="liste_article">
    <div class="index_php_admin">
        <div class="classAddDel">
            <a onclick="if(confirm('Supprimer l\'article ?')){return Del_article(<?php echo $_GET['id'] ?>)}else{return false}">
                <button class="Supprimer">Supprimer</button>
            </a>

            <a onclick="if(confirm('Ajouter l\'article ?')){return Add_article(<?php echo $_GET['id'] ?>)}else{return false}">
                <button class="Ajouter">Ajouter</button>
            </a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="formPublierVerif">    
            <fieldset id="fieldset">
                <h1><?php echo '<textarea name="titre" id="titre" cols="150" rows="3">' .  $titre . '</textarea>' ; ?></h1>
                <?php 
                echo '<img id="imgVerif" src = "../img_article/imagesArticlesVerifications/' . $url_image . '" width = "100px" height = "100px"/><br><br> ';
                ?>

                <input type="file" name="images" accept=".jpg, .jpeg, .png"  style="width: 700px; height: 30px;"><br>
                <?php
                echo '<textarea name="contenu" id="contenu" cols="150" rows="12">' .  $contenu . '</textarea><br><br>' ;
                ?>
                <table>
                    <tr><td>Catégorie : </td> <td><?php    echo '<input id="categorie" type="text" name="categorie" value="'. $categorie. '"<br><br>'; ?> </td></tr>
                    <tr><td>Signature : </td> <td><?php    echo '<input id="signature" type="text" name="signature_article" value="'. $signature_article. '"<br><br>';  ?> </td></tr>
                    <tr><td>Email envoyeur : </td> <td><?php    echo $emailEnvoyeur. '<br>';  ?> </td></tr>
                    <tr><td>Id envoyeur  : </td> <td><?php    echo $id. '<br>';  ?> </td></tr>
                    <tr><td>Date : </td> <td><?php    echo strftime(/*"%A*/ "%d/%m/%Y à %Hh%M", strtotime($article['date_time_publi']))  ;?>  </td></tr>
                </table>
                <div class="erreur" id="erreur"></div>
                <div id="success" class="success"></div>
                <input type="submit" value="Modifier l'article" name="submit_modif_article">
            </fieldset>
        </form>
    </div>
</div>

<script>


$(window).bind('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (String.fromCharCode(event.which).toLowerCase()) {
        case 's':
            Reload()
            window.event.cancelBubble = true;
            window.event.returnValue  = false;
            break;
        }
    }
});

$('#formPublierVerif').on('submit', function(e){
    e.preventDefault();
    Reload()
})

function DateAtm(){
    let date = new Date
    let dateMinute = 0
    date.getMinutes() < 10 ? dateMinute = '0' + date.getMinutes() : dateMinute = date.getMinutes()
    return date = date.getHours() + 'h ' +  dateMinute + 'min '  + date.getSeconds() 
}

function Reload() {

    var form = $('#formPublierVerif').get(0);
	var data = new FormData(form);
    $.ajax({
        url: './verif_index.php?' + '&id=<?php echo $_GET['id'] ?>',
        method: 'POST',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#fieldset').attr('disabled','disabled')
            $('#erreur').html('<img src="../img/loading2" />')
        },
        success: function(data){
            if(data[0] === 'errForm'){
                $('#fieldset').removeAttr("disabled")
                $('#erreur').html(data[1])
                $('#success').html('')
            }

            if(data[0] === 'successForm'){

                $('#fieldset').removeAttr("disabled")
                $('#erreur').html('')
                $('#success').html(data[1] + ' - ' + DateAtm())
                $("#titre").html(data[2])
                $("#imgVerif").attr('src', '../img_article/imagesArticlesVerifications/' + data[3])
                $("#contenu").html(data[4])
                $("#categorie").attr('value', data[5])
                $("#signature").attr('value', data[6])
            }
        }
    })
}


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
          });
        },
        success : function(data){
            location.reload()
        }
    })
};

</script>

<?php require '../inc/footer.php' ?>
