<?php
$titlePage = "Authentification";
$description = "Connectez-vous sur notre site";
require_once("inc/functions.inc.php"); // session_start(); // connexionBDD(); // alert(); // debug(); et autres fonctions utilisateurs

if (isset($_SESSION['user'])) {
    // Si une session existe avec un identifiant utilisateur, je me redirige vers la page profil.php
    // puisque cela veut dire que l'utilisateur est déjà connecté.
    // Ainsi, on fait une restriction d'accès à la page authentification.php
    header("location:profil.php");
}
$pseudo = $email = $mpd = $pseudoErreur = $emailErreur = $mdpErreur = "";
// $info = "";

if (!empty($_POST)) { // Si le formulaire est soumis
    // On a cliqué sur le bouton "Se connecter"
    // SI le formulaire n'est pas vide === si le formulaire est soumis
    // On vérifie si un champs est vide
    $soumissionFormulaire = true; // c'est un booléen qui va nous permettre de savoir si tous les champs sont remplis

    foreach ($_POST as $key => $value) {
        // Pourquoi on boucle sur les valeurs ? Pour vérifier si elles sont vides ou pas
        if (empty(trim($value))) {
            $soumissionFormulaire = false;
            // dans le cas où il y a un champs qui est vide, alors on va le signaler
        }
    }

    if (!$soumissionFormulaire) { // Si "n'est pas" true
        $info = alert("Veuillez renseigner tous les champs", "danger");
    } else {

        // debug($_POST['pseudo']);
        // debug($_POST['email']);
        // debug($_POST['mdp']);

        $pseudo = $_POST['pseudo'];
        $email = $_POST['email'];
        $mdp = $_POST['mdp'];

        $checkEmailPseudoUser = checkEmailPseudoUser($email, $pseudo); // je vérifie si l'utilisateur existe
        if ($checkEmailPseudoUser) {
            // debug($checkEmailPseudoUser);
            // debug($mdp, $checkEmailPseudoUser['mdp']);
            // debug(password_verify($mdp, $checkEmailPseudoUser['mdp']));

            if (password_verify($mdp, $checkEmailPseudoUser['mdp'])) {
                // password_verify() pour vérifier si un mot de passe correspond à un mot de passe haché créé par la password_hash().
                // Si le hash du mdp de la BDD correspond au mdp du formulaire, alors password_verify retourne true
                $info = alert("Mot de passe OK", "success");

                /*  Suite à la connexion on vas crére ce qu'on appelle une session :
                    Principe des sessions : un fichier temporaire appeléé "session" est crée sur le serveur, avec un identifiant unique . Les sessions constituent un moyen de stocker les données sur le serveur. Cette session est liée à un internaute car ces données sont propres à ce dernier,  Les données du fichier de session sont accessibles et manipulables à partir de la superglobale $_SESSION, elle est mêmoriser par le serveur et est disponible tant que la session de l'utilsateur est maintenu sur le serveur.
                    Quand une session est créée sur le serveur, ce dernier envoie son identifiant (unique) au client sous forme d'un cookie.
                    un cookie est déposé sur le poste de l'internaute avec l'identifiant (au nom de PHPSESSID). Ce cookie se détruit lorsqu'on quitte le navigateur.
                */
                // debug($_SESSION);
                $_SESSION['user'] = $checkEmailPseudoUser; // nous créons une session ave les infos de l'utilisateur provenant de la BDD
                // debug($_SESSION['user']); // renvoie un tableau associatif avec les infos de l'utilisateur qui n'est pas multidimensionnel
                // debug($_SESSION); renvoie un tableau multidimensionnel avec les infos de l'utilisateur
                // debug($_SESSION['user']['pseudo']); renvoie le pseudo de l'utilisateur
                // debug($_SESSION['user']['email']); renvoie l'email de l'utilisateur

                header("location:profil.php"); // on redirige l'utilisateur vers la page profil.php
                exit(); // on arrête le script
            }
        } else {
            $info = alert("Les identifiants sont incorrects", "danger");
        }
    }
}
require_once("inc/header.inc.php"); // Toujours mettre notre header.inc.php après l'appel de la fonction header() pour éviter les problèmes de redirection
?>

<main style="background:url(assets/img/5818.png) no-repeat; background-size: cover; background-attachment: fixed;">
    <div class="w-50 m-auto p-5 mt-5" style="background: rgba(20, 20, 20, 0.9);">
        <h2 class="text-center mb-5 p-3">Connexion</h2>

        <?php
        echo ($info);   // pour afficher les messages de vérification
        ?>

        <form action="" method="post" class="p-5">
            <div class="row mb-3">
                <div class="col-12 mb-5">
                    <label for="pseudo" class="form-label mb-3">Pseudo</label>
                    <input type="text" class="form-control fs-5" id="pseudo" name="pseudo">
                </div>
                <div class="col-12 mb-5">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control fs-5" id="email" name="email" placeholder="exemple.email@exemple.com">
                </div>
                <div class="col-12 mb-5">
                    <label for="mdp" class="form-label mb-3">Mot de passe</label>
                    <input type="password" class="form-control fs-5 mb-3" id="mdp" name="mdp">
                    <input type="checkbox" onclick="myFunction()"> <span class="text-danger">Afficher/masquer le mot de passe</span>
                </div>

                <button class="w-25 m-auto btn btn-danger btn-lg fs-5" type="submit">Se connecter</button>
                <p class="mt-5 text-center">Vous n'avez pas encore de compte ! <a href="register.php" class=" text-danger">créer un compte ici</a></p>
            </div>
        </form>
    </div>
</main>

<?php
require_once("inc/footer.inc.php");
?>