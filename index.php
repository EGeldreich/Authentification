<?php

$password = "1Mon2Mot3De4Passe";
$password2 = "1Mon2Mot3De4Passe";

// Algorithmes de hachage FAIBLE
$md5 = hash('md5', $password);
$md5_2 = hash('md5', $password2);
// echo "Hash avec md5 : ".$md5;
// echo "<br>";
// echo "Hash avec md5 : ".$md5_2;
// echo "<br><br><br>";

$sha256 = hash('sha256', $password);
$sha256_2 = hash('sha256', $password2);
// echo "Hash avec sha256 : ".$sha256;
// echo "<br>";
// echo "Hash avec sha256 : ".$sha256_2;
// echo "<br><br><br>";

// Algorithmes de hachage FORT
// PASSWORD_DEFAULT utilise BCRYPT
$hash = password_hash($password, PASSWORD_DEFAULT);
$hash2 = password_hash($password2, PASSWORD_DEFAULT);

// echo "Hash avec password_hash : ".$hash;
// echo "<br>";
// echo "Hash avec password_hash : ".$hash2;

// Password verify

//Saisie dans le formulaire
$saisie = "1Mon2Mot3De4Passe";

$check = password_verify($saisie, $hash);

var_dump($check);

if(password_verify($saisie, $hash)){
    echo "all good.";
    // $_SESSION["user"] = ...;
} else {
    echo "not all good.";
}

// REGISTER
// - Filtrer (sanitize) les champs du formulaire
// - Si valides, vérifier que le mail n'existe pas deja (sinon, msg d'erreur)
// - Pareil pour pseudo
// - Verifier que les 2 mdp sont identiques
// - Si oui, hash mdp
// - Ajouter utilisateur à la BDD

// LOGIN
// - Filtrer champs formulaire
// - Si valides, retrouver utilisateur et mdp lié à l'adresse mail
// - Si trouvé, récupérer le hash
// - password_verify le mdp
// - Si ok, connexion, passer utilisateur en session
// - Erreur à chacune de ses étapes si pas ok

