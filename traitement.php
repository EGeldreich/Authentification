<?php
session_start();

if(isset($_GET["action"])){
    switch($_GET["action"]){
        case "register":
            // REGISTER
            // - Filtrer (sanitize) les champs du formulaire
            // - Si valides, vérifier que le mail n'existe pas deja (sinon, msg d'erreur)
            // - Pareil pour pseudo
            // - Verifier que les 2 mdp sont identiques
            // - Si oui, hash mdp
            // - Ajouter utilisateur à la BDD
            if($_POST["submit"]){
                $pdo = new \PDO("mysql:host=localhost;dbname=php_hash;charset=utf8", "root","");
                
                //filtrer saisie des champs
                $pseudo = filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_VALIDATE_EMAIL);
                $pass1 = filter_input(INPUT_POST, "pass1", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $pass2 = filter_input(INPUT_POST, "pass2", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
                if($pseudo && $email && $pass1 && $pass2) {
                    // Verifier que le mail n'existe pas deja
                    // Requete sql pour recherche
                    $requete = $pdo->prepare("
                    SELECT * FROM user
                    WHERE email = :email;
                    ");
                    $requete->execute(["email" => $email]);
                    $user = $requete->fetch();
    
                    $requetePseudo = $pdo->prepare("
                    SELECT * FROM user
                    WHERE pseudo = :pseudo;
                    ");
                    $requetePseudo->execute(["pseudo" => $pseudo]);
                    $pseudoUsed = $requetePseudo->fetch();
    
                    if($user) {
                        // Si $user existe, alors mail déjà utilisé
                        header("Location: register.php"); exit;
                    } else {
                        // Si $user n'existe pas, alors nouveau mail, donc suite
                        if($pseudoUsed) {
                            // Si $pseudoUsed existe, alors pseudo déjà utilisé
                            header("Location: register.php"); exit;
                        } else {
                            // Si $pseudoUsed n'existe pas, alors nouveau pseudo, donc suite
    
                            // Verifier que les 2mdp sont identiques, et indiquer une longueur min
                            if($pass1 == $pass2 && strlen($pass1) >= 5) {
    
                                // Insère le user dans le BDD, avec le mdp hash
                                $insertUser = $pdo->prepare("
                                INSERT INTO user (pseudo, email, password)
                                VALUES (:pseudo, :email, :password);
                                ");
                                $insertUser->execute ([
                                    "pseudo" => $pseudo,
                                    "email" => $email,
                                    "password" => password_hash($pass1, PASSWORD_DEFAULT)
                                ]);
    
                                header("Location: login.php"); exit;
                            } else {
                                // mdp différents ou trop court
                            }
                        }
                    }
                } else {
                    // inputs non valides
                }
            }
            header("Location: register.php"); exit;
            break;

        case "login":
            // LOGIN
            // - Filtrer champs formulaire
            // - Si valides, retrouver utilisateur et mdp lié à l'adresse mail
            // - Si trouvé, récupérer le hash
            // - password_verify le mdp
            // - Si ok, connexion, passer utilisateur en session
            // - Erreur à chacune de ses étapes si pas ok
            if($_POST["submit"]){
                $pdo = new \PDO("mysql:host=localhost;dbname=php_hash;charset=utf8", "root","");
    
                // - Filtrer champs formulaire
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_VALIDATE_EMAIL);
                $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                
                // - Si valides, retrouver utilisateur et mdp lié à l'adresse mail
                if($email && $password) {
                    $requete = $pdo->prepare("
                        SELECT * FROM user
                        WHERE email = :email;
                    ");
                    $requete->execute(["email" => $email]);
                    $user = $requete->fetch();
    
                    // - Si trouvé, récupérer le hash
                    if($user){
                        $hash = $user['password'];
    
                        // - password_verify le mdp
                        if($hash == password_verify($password, $hash)) {
                            // - Si ok, connexion, passer utilisateur en session
                            $_SESSION['user'] = $user;
                            header("Location: home.php"); exit;
                        } else {
                            // Mauvais mdp
                            header("Location: login.php"); exit;
                        }
                    } else {
                        // Mauvais email
                        header("Location: login.php"); exit;
                    }
                } else {
                    // Mauvais inputs
                    header("Location: login.php"); exit;
                }
            }
            header("Location: login.php"); exit;
            break;

            case "logout":
                unset($_SESSION['user']);
                header("Location: home.php"); exit;
            break;
    }
}