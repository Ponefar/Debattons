<form class="implementation" action="" method="get">
    <input id="activite_recherche" type="text" placeholder="Nom de l'article recherché" name="activite_rechercher">
    <div style="margin:15px auto;background-color:wheat" id="resultat"></div>
</form>

<script>
        
$( function(){

    $("#activite_recherche").on('keyup', function(){
        if ($(this).val().length > 1){
            let act = $(this).val();
            $(this).val();
            $.ajax({
                url : "./recherche.php",
                method : "get",
                data : {activite_rechercher : act },
                success : function(resultat){
                    if (resultat != 0){
                        $("#resultat").html(resultat);

                    }else  $("#resultat").html('<div class="erreur">Aucun résultat trouvé</div>') 
                }
            });
        }
    })
});

</script>