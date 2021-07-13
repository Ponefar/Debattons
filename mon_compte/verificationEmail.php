<?php require '../inc/bdd.php' ; 

if(!isset($_GET['verificationEmail'])){
    return header('Location: ../index.php');
}

if(isset($_GET['verificationEmail'])){
    $req2 = $bdd->prepare('SELECT * FROM users WHERE token = :tokenVerificationEmail AND verificationEmail = 0 ');
    $req2->execute(array(
    "tokenVerificationEmail" => $_GET['verificationEmail']
    ));
    $test = $req2->rowCount();

    if($test !== 1){
        return header('Location: ../index.php');
    }
}


$req1 = $bdd->prepare('UPDATE users SET verificationEmail = :verificationEmail WHERE token = :tokenVerificationEmail');
$req1->execute(array(
    "verificationEmail" => 1,
    "tokenVerificationEmail" => $_GET['verificationEmail']
));

header('Location: ./connexion.php?verification=true')


?>

