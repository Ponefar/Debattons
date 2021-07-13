<link rel="stylesheet" href="../css/adrien.css">
<link rel="stylesheet" href="./style.css">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
     integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
     crossorigin="anonymous">
</script>    

<?php require '../inc/header.php' ;

if(isset($_SESSION['id'])){
    if(!$_SESSION['admin']){
        return header('Location: ../index.php');
    }
    
}else {
    return header('Location: ../index.php');
}


?>

<div class="container">
    <div class="unset">
        <div class="white title br1">Rentrez le mot de passe</div>
        <form class="center" action="" id="formAdmin">
            <input class="padd-l border-no" type="password" name="password" placeholder="mot de passe"><br /><br />
            <input class="padd-l bc-white hover border-no" type="submit" value='envoyer'>
        </form>
        <div class="center white" id="result"></div>
        <div id="resultHeight" class="center black title"></div>
    </div>
</div>

<script>

$('#formAdmin').on('submit', function(e){
    e.preventDefault()
    $.ajax({
        method: 'POST',
        url: './verif_connexion_admin.php',
        data: $('#formAdmin').serialize(),

        beforeSend: function(){
            $('#result').html('<img src="../img/loading2.svg" />')
        },
        success:function(data){
            if(data[0] == "errForm"){
                $('#result').html(data[1])
            }else {
                $('.container').html(data)
            }

        }
    })
})


function modif(param){
    
    $.ajax({
        method: 'POST',
        url: './change_admin.php',
        data: {
            id : param
        },
        beforeSend: function(){
            $('#' + param).html('<img src="../img/loading2.svg" />')
        },
        success: function(data){
            $('#' + param).html(data[1])
        }
    })
}

function delUser(param){
    $.ajax({
        method: 'POST',
        url: './del_user.php',
        data: {
            id : param
        },
        beforeSend: function(){
            $('#' + param).html('<img src="../img/loading2.svg" />')
        },
        success: function(data){
            $('#' + param).html('<div class="erreur">Supprimé </div>')
        }
    })
}


function recupValInput(){
    var e = $('#inputAutoCompletion').val()
    if($('#inputAutoCompletion').val().length > 2){
        $.ajax({
            method: 'POST',
            url: './recherche.php',
            data: {
                valeurInput : e
            },
            success:function(data){
                if(data){
                    $('#resultInput').html('<table class="autoTable">' + data + '</table><br /><br /><hr><br /><br />')
                }   

                if(data == 0){
                    $('#resultInput').html("Aucun résultat")
                }   
            }
        })
    }
}


</script>
<script src="js.js"></script>
