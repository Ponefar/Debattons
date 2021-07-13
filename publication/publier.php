<?php 

ob_start();

require '../inc/header.php'; 

?>

<script>
$("title").text("Publication d'un article - Debattons.fr")
</script>

<?php

if(isset($_SESSION['id'])){
    
    $date_atm = date("Y-m-d H:i:s");
  
    $date_time_unban = $bdd->prepare('SELECT * FROM verification WHERE emailEnvoyeur = :emailenvoyeur ORDER BY date_time_unban DESC LIMIT 1');
    $date_time_unban->execute(array(
        'emailenvoyeur' => $_SESSION['email']
    ));
    
    $zeubi = $date_time_unban->fetch();  
    $date_bdd = $zeubi['date_time_unban'];
}

if(!isset($_SESSION['id'])){ 
        require './noSession.php';
        require '../inc/footer.php';
        die();
}

if(!$_SESSION['admin']){
    if($date_atm < $date_bdd){
        require 'noSpamPublier.php';
        require '../inc/footer.php';
        die();
    }
}

?>
<div class="inscription">
    <form action="" method="POST" enctype="multipart/form-data" id="formPublier">
        <fieldset id="fieldset">
            <div class="form_verif_redaction">
                <div class="titreMonCompte" id="redirect">Rédigez votre article </div>
                <textarea onkeydown="titreCaracteres();Desabled()" id="titreCaracteresTextarea" name="article_titre" rows="2" cols="68px"  placeholder="Titre*"><?php if(isset($_POST['article_titre'])){echo $_POST['article_titre'];}?></textarea><br>
                <div id="titreCaracteres"></div>
                <textarea onkeydown="contenuCaracteres();Desabled()" id="contenuCaracteresTextarea" name="article_contenu" rows="10" cols="68px"  placeholder="Contenu de l'article*"><?php if(isset($_POST['article_contenu'])){echo $_POST['article_contenu'];}?></textarea><br>
                <div id="contenuCaracteres"></div>
                <input type="file" name="images" accept=".jpg, .jpeg, .png"  style="width: 700px; height: 30px;"><br>
                <textarea onkeydown="pseudoCaracteres();Desabled()" id="pseudoCaracteresTextarea" name="signature_article" style="width: 700px; height: 30px;"  placeholder="Signature / Si anonyme remplir 'ANONYME'"><?php if(isset($_POST['signature_article'])){echo $_POST['signature_article'];}?></textarea><br>
                <div id="pseudCaracteres"></div>

                <select class="select"  name="categorie" >
                    <option value="">--Categorie de votre article*--</option>
                    <option value="politique">Politique</option>
                    <option value="santé">Santé</option>
                    <option value="lobbing">Lobbing</option>
                    <option value="autre">Autre</option>
                </select><br /><br /> 
                <div id="erreur" class="erreur"></div>
                <div id="success" class="success"></div>
                <input id="submit"  type="submit" name="Envoyer"  value="Envoyer ➜"  />
            </div>
        </fieldset>
    </form>
</div>

<script>

$('#formPublier').on('submit', function(e){
    e.preventDefault();
    var form = $('#formPublier').get(0);
	var data = new FormData(form);
    $.ajax({
        url: './verif_publier.php',
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

            if(data[0] === 'delaiPubli'){
                $('#erreur').html(data[1])
            }

            if(data[0] === 'successForm'){
                <?php 
                if(!$_SESSION['admin']){
                ?>
                    location.reload()
                <?php
                }
 
                if($_SESSION['admin']){
                ?>
                    $("#formPublier")[0].reset()
                    $('#fieldset').removeAttr("disabled")
                    $('#erreur').html('')
                    $('#success').html(data[1])
                    $('#titreCaracteres').html('')
                    $('#contenuCaracteres').html('')
                    $('#pseudCaracteres').html('')
                <?php
                }
                ?>
            }
        }
    })
})


function titreCaracteres(){
    var Rien = document.getElementById("titreCaracteresTextarea").value.length;
    var here = document.getElementById("titreCaracteres");
    if(Rien < 255){
        here.innerHTML = `<div class='valider'> ${Rien + 1} / 255 caractères </div>`      
    }else {
        here.innerHTML = `<div class='erreur'> ${Rien + 1} / 255 caractères</div>`
    }
}

function contenuCaracteres(){
    var Rien = document.getElementById("contenuCaracteresTextarea").value.length;
    var here = document.getElementById("contenuCaracteres");
    if(Rien < 40000){
        here.innerHTML = `<div class='valider'> ${Rien + 1} / 40 000 caractères </div>`      
    }else {
        here.innerHTML = `<div class='erreur'> ${Rien + 1} / 40 000 caractères</div>`
    }     
}

function pseudoCaracteres(){
    var Rien = document.getElementById("pseudoCaracteresTextarea").value.length;
    var here = document.getElementById("pseudCaracteres");
    if(Rien < 100){
        here.innerHTML = `<div class='valider'> ${Rien + 1} / 100 caractères </div>`      
    }else {
        here.innerHTML = `<div class='erreur'> ${Rien + 1} / 100 caractères</div>`
    }
}

function Desabled(){
    var Rien1 = document.getElementById("titreCaracteresTextarea").value.length;
    var Rien2 = document.getElementById("contenuCaracteresTextarea").value.length;
    var Rien3 = document.getElementById("pseudoCaracteresTextarea").value.length;

    if(Rien1 > 254 || Rien2 > 39999 || Rien3 > 99){
        document.getElementById('submit').setAttribute('disabled', "")
    }else{
        document.getElementById('submit').removeAttribute('disabled')
    }
}

</script>  

<?php require '../inc/footer.php' ?>
