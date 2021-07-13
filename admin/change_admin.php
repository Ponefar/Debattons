<?php session_start();

require '../inc/bdd.php';

$req = $bdd->prepare('SELECT * FROM users WHERE id = :id ');
$req->execute(array(
    "id" => $_POST['id']
));

if($a = $req->fetch()){
    if($a['admin'] == 0){
        $req = $bdd->prepare('UPDATE users SET admin = 1 WHERE id =' . $_POST['id']);
        $req->execute();
        $data = array('success',"&#160;&#160;&#160;&#160;<button onclick='if(confirm(`Modifier ?`)){
            return modif(" . $_POST['id'] . ")
        }else{return false}' class='del border-no hover padd-l radius-5'>del</button>
        &#160;&#160;<img class='croixSvg hover' src ='../img/croix.svg' onclick='if(confirm (`Supprimer ? `)){
            return delUser(" .  $_POST['id']  . ")}else{return false}' />");
    }  

    if($a['admin'] != 0){
        $req = $bdd->prepare('UPDATE users SET admin = 0 WHERE id =' . $_POST['id']);
        $req->execute();
        $data = array('success',"&#160;&#160;&#160;<button onclick='if(confirm(`Modifier ?`)){
            return modif(" . $_POST['id'] . ")}
            else{return false}' class='add border-no hover padd-l radius-5'>add</button>
            &#160;&#160;<img class='croixSvg hover' src ='../img/croix.svg' onclick='if(confirm (`Supprimer ? `)){
                return delUser(" .  $_POST['id']  . ")}else{return false}' />");
    }
}

header("Content-Type: application/json");
echo json_encode($data);

?>