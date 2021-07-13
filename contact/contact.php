<?php require '../inc/header.php' ?>

<script>
 $("title").text("Contactez-nous - Debattons.fr")
</script>

<link rel="stylesheet" href="../css/contact.css">

<div class="contact" id="contact">
    <div class="flex noFullSize">
        <div class="container"><img class="imgAbsoluteCenter" src="../img/message.svg" alt=""></div>
        <div class="container center">
            <form method="POST" id="formContact">
                <fieldset id="fieldset">
                    <div class="titreContact">Devis / Contactez-nous <i class="material-icons" style="font-size:35px">mail</i></div>
                    <input type="text" placeholder="Adresse mail" id="mail" name="mail"value="<?php if(isset($submit)){echo $mail;} ?>"><br />
                    <input type="text" placeholder="Objet" id="objet" name="objet"value="<?php if(isset($submit)){echo $objet;} ?>"><br />
                    <textarea rows="5" placeholder="Message" id="message" name="message"><?php if(isset($submit)){echo $message;} ?></textarea><br>
                    <div class="g-recaptcha" data-sitekey="6LdoIawaAAAAAKaYFuMm3y9tRSkvSTFHqBKdRFHw"></div>
                    <div id="success" class="success"></div>
                    <div id="erreur" class="erreur"></div>
                    <div id="envoyer"><input id="contactSubmit" type="submit" value="Envoyer" name="submit"></div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script>
        
$( function(){

    $("form").on('submit', function(e){
        e.preventDefault();

        $.ajax({
            url : "./envoieMail/contact.php",
            method : "POST",
            data: $('#formContact').serialize(),
            beforeSend: function() {
                grecaptcha.reset();
                $('#fieldset').attr("disabled","disabled")
                $("#erreur").html("<img class='svg' src='../img/loading2.svg' />")
            },
            
            success : function(data){   
                if(data[0] == 'errForm'){
                        $('#fieldset').removeAttr("disabled")
                        $("#erreur").html(data[1])
                        $("#success").html('')
                }
                
                if(data[0] == 'successForm'){
                        $('#fieldset').removeAttr("disabled")
                        $("#success").html(data[1])
                        $("#erreur").html('')
                        $("form")[0].reset();
                }
            }
        });
    })
});

</script>

<?php require '../inc/footer.php' ?>