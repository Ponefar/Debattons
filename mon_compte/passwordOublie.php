<?php require '../inc/header.php' ?>

<script>
$("title").text("Récupération du mot de passe - Debattons.fr")
</script>

<div class="connexion">
    <div class="form_connexion">
        <div class="titreMonCompte">Mot de passe oublié</div>
        <form method="POST" action="" id="formPasswordOublie">
            <fieldset id="fieldset">
                <input type="text" name="mail_recup" placeholder="adresse e-mail"><br>
                <div class="erreur" id="erreur"></div>
                <div class="success" id="success"></div>
                <input type="submit" name="Envoyer">
            </fieldset>
        </form>
    </div>
</div>

<script>

$('#formPasswordOublie').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url: './verif_passwordOublie.php',
        method: 'POST',
        data: $('#formPasswordOublie').serialize(),
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
