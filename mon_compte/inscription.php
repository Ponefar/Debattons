<?php require '../inc/header.php' ?>

<script>
$("title").text("Incription - Debattons.fr")
</script>

<?php

if(isset($_SESSION['id'])){
    return header('Location: ../index.php');
}

?>

<div class="inscription">
    <div class="form_inscription">
        <div class="titreMonCompte">inscription</div>
        <p class="accepter_conditions_generales">
            En cliquant sur s'inscrire, 
            vous reconnaissez avoir lu et vous acceptez les Conditions d'utilisation et la Politique de confidentialité 
        </p>
        <form method="post" action="" name="form" id="formInscription">
            <fieldset id="fieldset">
                <input type="text" name="prenom" placeholder="pseudo"  value="<?php if(isset($_POST['prenom'])){echo $_POST['prenom'];} ?>"><br>
                <input type="text" name="email"  placeholder="e-mail"value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>"><br>
                <div class="relative">
                    <input type="password" name="password1"  placeholder="mot de passe" id="input_text-password">
                    <div class="input_text-password" ><img id="img_eyes" src="../img/eyes-no.svg" alt="" onclick="ChangeTypeInputPass('1')"></div>
                </div>
                <div class="relative">
                    <input type="password" name="password2"  placeholder="confirmation du mot de passe" id="input_text-password_verif">
                    <div class="input_text-password" ><img id="img_eyes_verif" src="../img/eyes-no.svg" alt="" onclick="ChangeTypeInputPass('2')"></div>
                </div>
                <br />
                <div class="legend">Souhaitez-vous recevoir un E-email à la publication d'un article</div>
                <div class="flexForm">
                    <input type="radio" id="envoie_email_oui" name="contact_email" value="oui" >
                    <label for="envoie_email_oui">Oui</label>
                    <input type="radio" id="envoie_email_non" name="contact_email" value="non" >
                    <label for="envoie_email_non">Non</label>
                </div>
                <div class="flexForm">
                    <input type="checkbox" id="accept" name="acceptConditions">
                    <label class="luetaccept" for="accept">J'ai lu et j'accepte les&nbsp;</label><a class="ahref" href="./conditionsUtilisations.html"> conditions générales d'utilisations</a>
                </div>
                <div id="erreur" class="erreur"></div>
                <div id="success" class="success"></div>
                <input type="submit" name="submit" value="S'inscrire &#x279C; " id="submitButton"> 
            </fieldset>
        </form>
            <a href="./connexion.php"><input type="submit" name="submit" value="Déjà un compte ? Se connecter"/></a>
        </div>
</div>

<script>

$('#formInscription').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url: './verif_inscription.php',
        method: 'POST',
        data: $('#formInscription').serialize(),
        beforeSend: function() {
            $('#fieldset').attr("disabled","disabled")
            $("#erreur").html("<img class='svg' src='../img/loading2.svg' />")
        },
        success : function(data){  
            if(data[0] === 'errForm'){
                $("#erreur").html(data[1])
                $("#success").html('')
                $('#fieldset').removeAttr('disabled')
        
            }else{
                $("#erreur").html('')
                $("#success").html(data[1])
                $("form")[0].reset();
                $('#fieldset').removeAttr('disabled')
            }
        }
    });
})

</script>

<?php require '../inc/footer.php' ?>
