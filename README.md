# Appli - Php - Média interactif

Projet debattons dans le cadre du CDA : https://www.montpellier-meilleur-ville.site/Debattons/liste_article/


BDD format sql


Pour la connexion à la BDD, rajoutez un fichier bdd.php dans le dossier inc et remplacer les X par vos informations : 


    $utilisateur = "X";
    $mdp = "X";
    $bdd = new PDO('mysql:host=X.mysql.db;dbname=X', $utilisateur, $mdp);
    $bdd->exec('SET NAMES utf8');
