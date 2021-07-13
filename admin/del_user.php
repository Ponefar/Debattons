<?php session_start();

require '../inc/bdd.php';

$req = $bdd->prepare('SELECT * FROM users WHERE id = :id ');
$req->execute(array(
    "id" => $_POST['id']
));

if($a = $req->fetch()){
        $req = $bdd->prepare('DELETE FROM users WHERE id =' . $_POST['id']);
        $req->execute();
        $data = array('success', "");
}

header("Content-Type: application/json");
echo json_encode($data);

?>