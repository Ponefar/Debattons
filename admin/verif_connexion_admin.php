<?php session_start();

require '../inc/bdd.php';

$req = $bdd->query("SELECT * FROM users ORDER BY id DESC LIMIT 0 , 15");
$req1 = $bdd->query('SELECT * FROM users');
$compteur = $req1->rowCount();

$req2 = $bdd->query('SELECT * FROM users WHERE admin = 1');
$compteurAdmin = $req2->rowCount();

$error = 0;

if(!isset($_SESSION['id'])){
    return header('Location: ../index.php');
}

if(!isset($_SESSION['admin'])){
    return header('Location: ../index.php');
}

if(!isset($_POST['password'])){
    return header('Location: ../index.php');
}

if(empty($_POST['password'])){
    $data = array('errForm','Merci de remplir le mot de passe');
    $error = 1;
}

if($_POST['password'] !== "aa" && $error == 0){
    $data = array('errForm','Mauvais mot de passe');
}

if($_POST['password'] !== "aa"){
    header("Content-Type: application/json");
    echo json_encode($data);
}

if($_POST['password'] === "aa"){
    ?>
    <div class="unset">
        <form action="" class="center" id="formAutoCompletion">
            <div class="black title left">Nombre d'utilisateurs : <?php echo $compteur ?></div><br />
            <div class="black title left">Nombre d'administrateur : <?php echo $compteurAdmin ?></div><br />
            <input type="text" class="texte"  placeholder="utilisateurs // 'admin' -> voir tous les admins" id="inputAutoCompletion" onkeyup="recupValInput()"><br /><br />
        </form>
        <div id="resultInput" class="black center title"></div>

        <table id="table">
            <?php
            while($a = $req->fetch()){
            ?>
                <tr>
                    <?php
                    echo "<td class='padd-xl'>" . $a['email'] . "</td>";
                    if($a['admin'] == 0){
                        echo "<td id='" . $a['id'] . "'class='padd-xl flexx'>&#160;&#160;&#160;&#160;
                        <button onclick='if(confirm(`Modifier ?`)){return modif(" . $a['id'] . ")
                        }else{return false}' class='add border-no hover padd-l radius-5'>add</button>
                        &#160;&#160;<img class='croixSvg hover' src ='../img/croix.svg' onclick='if(confirm (`Supprimer ? `)){
                        return delUser(" . $a['id'] . ")}else{return false}' /><td>";
                    }
                    
                    if($a['admin'] != 0){
                        echo "<td id='" . $a['id'] . "'class='padd-xl flexx'>&#160;&#160;&#160;&#160;
                        <button onclick='if(confirm(`Modifier ?`)){return modif(" . $a['id'] . ")
                        }else{return false}' class='del border-no hover padd-l radius-5'>del</button>
                        &#160;&#160;<img class='croixSvg hover' src ='../img/croix.svg' onclick='if(confirm (`Supprimer ? `)){
                        return delUser(" . $a['id'] . ")}else{return false}' /><td>";                    
                    }

                    ?>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <?php
}

?>
