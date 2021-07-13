<?php ob_start(); 

require '../inc/header.php';

?>

<script>
$("title").text("Modification du mot de passe - Debattons.fr")
</script>

<?php

if(!isset($_GET['tokenResetPswd'])){
    return header('Location: ./passwordOublie.php');
}

if(isset($_GET['tokenResetPswd'])){
    $req2 = $bdd->prepare('SELECT * FROM users WHERE tokenResetPswd = :tokenResetPswd');
    $req2->execute(array(
    "tokenResetPswd" => $_GET['tokenResetPswd']
    ));
    $test = $req2->rowCount();

    if($test !== 1){
        return header('Location:./passwordOublie.php');
    }
}
?>

<div class="connexion">
    <form method="POST" action="" id="formPasswordToken">
        <fieldset id="fieldset">
            <div class="form_connexion">
                <div class="titreMonCompte">Mot de passe oublié</div>
                <input type="password" name="password_recup" placeholder="Nouveau mot de passe" >
                <input type="password" name="password_Confirm" placeholder="Confirmation mot de passe" >
                <div class="erreur" id="erreur"></div>
                <div class="success" id="success"></div>
                <input type="submit" name="Envoyer">
            </div>
        </fieldset>
    </form>
</div>

<script>

$('#formPasswordToken').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url: './verif_passwordToken.php',
        method: 'POST',
        data: $('#formPasswordToken').serialize() + '&tokenResetPswd=<?php echo $_GET['tokenResetPswd'] ?>',
        beforeSend: function() {
            $('#fieldset').attr("disabled","disabled")
            $("#erreur").html("<img class='svg' src='../img/loading2.svg' />")
        },
        success : function(data){  
            if(data[0] === 'errForm'){
                $("#erreur").html(data[1])
                $("#success").html('')
                $('#fieldset').removeAttr('disabled')
            }

            if(data[0] === 'successForm'){
                var newLocation = "./connexion.php?pswd=true";
                window.location = newLocation;
            }
        }
    });
})

</script>

<?php require '../inc/footer.php' ?>
