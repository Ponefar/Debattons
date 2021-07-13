<?php session_start();

require '../inc/bdd.php';

if(!isset($_SESSION['id'])){
    return header('Location: ../index.php');
}

if(!$_SESSION['admin']){
    return header('Location: ../index.php');
}

if(!isset($_POST['valeurInput'])){
    return header('Location: ../index.php');
}

if($_POST['valeurInput'] === 'admin'){
    $req = $bdd->prepare('SELECT * FROM users WHERE admin = 1');
    $req->execute();
}else {
    $chaine = "%" . $_POST['valeurInput'] . "%";
    $req = $bdd->prepare('SELECT * FROM users WHERE email LIKE :chaine ORDER BY email ASC');
    $req->execute(array(
        "chaine" => $chaine
    ));
}
?>

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
