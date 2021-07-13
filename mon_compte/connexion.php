<?php 

ob_start();

require '../inc/header.php'; 

@$submit = $_POST['submit'];
@$email = $_POST['email'];
@$password1 = $_POST['password1'];
$ip_ban = 25;

$success = "";

if(isset($_GET['pswd'])){
    if($_GET['pswd'] === "true"){
        $success = "Mot de passe modifié avec succès ! ";
    }
}

if(isset($_GET['verification'])){
    if($_GET['verification'] === "true"){
        $success = "Compte activé ! ";
    }
}

$date_atm = date("Y-m-d H:i:s");

$req = $bdd->prepare('SELECT * FROM users WHERE email = :email');
$req->execute(array(
    'email' => $email));
$resultat = $req->fetch();

$mon_ip = $_SERVER['REMOTE_ADDR'];

$count_ip_ban = $bdd->prepare('SELECT * FROM ip_ban WHERE adresse_ip = :mon_ip ');
$count_ip_ban->execute(array(
    'mon_ip' => $mon_ip
));
$nombre_ip_ban = $count_ip_ban->rowCount();

$time_unban = $bdd->prepare('SELECT * FROM ip_ban WHERE adresse_ip = :mon_ip ORDER BY date_unban DESC LIMIT 1');
$time_unban->execute(array(
    'mon_ip' => $mon_ip
));

while($recup_unban = $time_unban->fetch()){
    $date_unban = $recup_unban['date_unban'];
}

if($nombre_ip_ban!= 0){
    if($date_unban < $date_atm){
        $recupdonnee = $bdd->prepare('DELETE FROM ip_ban WHERE `adresse_ip` = :adresse_ip');
        $recupdonnee->execute(array(
            'adresse_ip' => $mon_ip
        ));
    }
}

if(($nombre_ip_ban >= $ip_ban)) {
    if($date_unban > $date_atm){
        require '../inc/ipBan.html';
        require '../inc/footer.php';
        die();
    }
}  

if(isset($_SESSION['id'])){ 

?>

<script>
$("title").text("Mon compte : modifications de mes informations - Debattons.fr")
</script>

<?php

    $req3 = $bdd->prepare('SELECT * FROM users WHERE id =' . $_SESSION['id']);
    $req3->execute();
    $users = $req3->fetch();
    ?>

    <div class="inscription">
        <div class="form_inscription">
            <div class="valider"><?php echo $success ?></div>
            <div class="titreMonCompte">Mon compte</div>
            <form method="post" action=""  name="form" id="formModif" enctype="multipart/form-data">
                <fieldset id="fieldset">
                    <img id="NewImg" class="imageProfil" src="../img_article/imagesUsers/<?php echo $users['url_image'] ?>" alt=""><br />
                    <input type="file" name="images" accept=".jpg, .jpeg, .png"><br>
                    <input id="NewPrenom" type="text" name="Newprenom" placeholder="pseudo" value="<?php if(isset($_POST['Newprenom'])){
                        echo $_POST['Newprenom'];}else{echo $users['prenom'];}?>" /><br /><br />
                    <input id="NewMail" type="text" name="Newemail" placeholder="e-mail" value="<?php if(isset($_POST['Newemail'])){echo $_POST['Newemail'];}else{echo $users['email'];}?>" /><br><br>

                    <div class="relative">
                        <input type="password" name="Newpassword1" placeholder="mot de passe"id="input_text-password">
                        <div class="input_text-password" ><img id="img_eyes" src="../img/eyes-no.svg" alt="" onclick="ChangeTypeInputPass('1')"></div>
                    </div>
                    <br />

                    <div class="relative">
                        <input type="password" name="Newpassword2" placeholder="mot de passe" id="input_text-password_verif">
                        <div class="input_text-password" ><img id="img_eyes_verif" src="../img/eyes-no.svg" alt="" onclick="ChangeTypeInputPass('2')"></div>
                    </div>
                    <br />

                    <div class="legend">Souhaitez-vous recevoir un E-email à la publication d'un article</div>

                    <div class="flexForm">
                        <input type="radio" id="envoie_email_oui" 
                        name="contact_email" value="oui" <?php // echo $checked_oui ?>>
                        <label for="envoie_email_oui">Oui</label>

                        <input type="radio" id="envoie_email_non"
                        name="contact_email" value="non" <?php //  echo // $checked_non ?> >
                        <label for="envoie_email_non">Non</label>
                    </div>

                    <?php
                    
                    $req2 = $bdd->prepare('SELECT * FROM users WHERE recevoir_email = "oui" && id =' . $_SESSION['id']);
                    $req2->execute();
                    $recevoir_email_oui = $req2->rowCount();
                    if($recevoir_email_oui == 0){
                    ?>

                        <script>
                        $('#envoie_email_non').attr('checked', 'checked')
                        </script>

                    <?php
                    }else{ 
                    ?>

                        <script>
                        $('#envoie_email_oui').attr('checked', 'checked')
                        </script>
                        
                    <?php
                    }
                    ?>


                    <div id="erreur" class="erreur"></div>
                    <div id="success" class="success"></div>
                    <input type="submit" name="Newsubmit" value="Modifier ➜"><br>
                </fieldset>
            </form>
        </div>
    </div>     
    <?php 

}
?>

<script>

$('#formModif').on('submit', function(e){
    e.preventDefault();
    var form = $('#formModif').get(0);
	var data = new FormData(form);
    $.ajax({
        url: './verif_modif.php',
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
                $('#success').html(data[1])
                $("#NewImg").attr('src', '../img_article/imagesUsers/' + data[2])
                $("#Newprenom").attr('value', data[3])
                $("#NewMail").attr('value', data[4])

                if(data[5] === 'non'){
                    $("#envoie_email_oui").removeAttr('checked')
                    $("#envoie_email_non").attr('checked', 'checked')
                }
                
                if(data[5] === 'oui') {
                    $("#envoie_email_non").removeAttr('checked')
                    $("#envoie_email_oui").attr('checked', 'checked')
                }

            }
        }
    })
})

</script>

<?php

if (!isset($_SESSION['id'])){
    ?>

    <script>
    $("title").text("Connexion - Debattons.fr")
    </script>

    <div class="connexion">
        <div class="form_connexion">
            <div class="valider"><?php echo $success ;?></div>
        
            <div class="titreMonCompte">Connexion</div>
            <p class="connecter_all_fonctionnalites">Veuillez vous connectez afin d'accèder à l'ensemble de nos fonctionnalités</p>
            <form method="POST" name="form" id="formconnextion">
                <fieldset id="fieldset">
                    <input type="text" name="email" placeholder="e-mail"   value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>"><br>
                    <div class="relative">
                        <input type="password" name="password1" placeholder="mot de passe" id="input_text-password">
                        <div class="input_text-password" ><img id="img_eyes" src="../img/eyes-no.svg" alt="" onclick="ChangeTypeInputPass('1')"></div>
                    </div>
                    <div class="g-recaptcha" data-sitekey="6LdoIawaAAAAAKaYFuMm3y9tRSkvSTFHqBKdRFHw"></div>
                <div class="erreur" id="erreur"></div>
                    <input type="submit" name="submit" value="Se connecter ➜">
                </fieldset>
            </form>
            <a href="./inscription.php">
                <input type="submit" name="submit" value="Nouveau sur le site ? Inscription ici ! " />
            </a><br />
            <div class="mot_de_passe_oublie">
                <a  class="mot_de_passe_oublie_hover"href="passwordOublie.php">Mot de passe oublié ? </a> 
            </div>
        </div>
    </div>
<?php
}
?>

<script>

    $('#formconnextion').on('submit', function(e){
        e.preventDefault();
        
        $.ajax({
            url : './verif_connexion.php',
            method : 'POST',
            data : $('#formconnextion').serialize(),
            beforeSend: function() {
                grecaptcha.reset();
                $('#fieldset').attr("disabled","disabled")
                $("#erreur").html("<img class='svg' src='../img/loading2.svg' />")
            },
            
            success : function(data){   
                if(data[0] === 'errForm'){
                    $("#erreur").html(data[1])
                    $('#fieldset').removeAttr("disabled")
                } 

                if (data[0] === 'successForm' || data[0] === 'ipBan' ){
                    location.reload();
                }

                if (data[0] === 'redirection'){
                    document.location.href="../index.php"; 
                }


                


            }
        });
    })
        
        
        


</script>


<?php require '../inc/footer.php' ?>
