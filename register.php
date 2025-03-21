<?php
$titlePage = "Inscription";
$descriptionPage = "Inscrivez-vous sur notre site";
require_once("inc/functions.inc.php");

if (isset($_SESSION['user'])) {
    // Si une session existe avec un identifiant utilisateur, je me redirige vers la page profil.php
    // puisque cela veut dire que l'utilisateur est déjà connecté.
    // Ainsi, on fait une restriction d'accès à la page authentification.php
    header("location:profil.php");
}

$info = "";

if (!empty($_POST)) {
    $verification = true;

    foreach ($_POST as $key => $value) {
        if (empty(trim($value))) {
            $verification = false;
        }
    }

    if ($verification === false) {
        $info .= alert("Veuillez renseigner tous les champs", "danger");
    } else {
        # vérification du lastname existant
        if (!isset($_POST['lastName']) || strlen(trim($_POST['lastName'])) > 50 || strlen(trim($_POST['lastName'])) < 2) {
            $info = alert("Le champs nom n'est pas valide", "danger");
        }

        # vérification du firstname existant
        if (!isset($_POST['firstName']) || strlen(trim($_POST['firstName'])) > 50 || strlen(trim($_POST['firstName'])) < 2) {
            $info .= alert("Le champs prénom n'est pas valide", "danger");
        }

        # vérification du pseudo existant
        if (!isset($_POST['pseudo']) || strlen(trim($_POST['pseudo'])) > 50 || strlen(trim($_POST['pseudo'])) < 2) {
            $info .= alert("Le champs pseudo n'est pas valide", "danger");
        }

        # vérification du mail existant
        if (!isset($_POST['email']) || strlen(trim($_POST['email'])) > 100 || strlen(trim($_POST['email'])) < 6 || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $info .= alert("Le champs mail n'est pas valide", "danger");
        }

        # vérification du phone existant
        $regexPhone = "/^[0-9]{10}$/";
        if (!isset($_POST['phone']) || !preg_match($regexPhone, $_POST['phone'])) {
            $info .= alert("Le numéro de téléphone n'est pas valide", "danger");
        }

        # vérification du mdp existant
        $regexMdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        // Explique le regex mdp
        /**
         * ^ : début de la chaine
         * (?=.*[a-z]) : au moins une lettre minuscule
         * (?=.*[A-Z]) : au moins une lettre majuscule
         * (?=.*\d) : au moins un chiffre
         * (?=.*[@$!%*?&]) : au moins un caractère spécial
         * [A-Za-z\d@$!%*?&]{8,} : au moins 8 caractères parmi les lettres minuscules, majuscules, chiffres et caractères spéciaux
         * $ : fin de la chaine
         */

        if (!isset($_POST['mdp']) || !preg_match($regexMdp, $_POST['mdp'])) {
            $info .= alert("Le mot de passe n'est pas valide", "danger");
        }

        if (!isset($_POST['confirmMdp']) || $_POST['mdp'] !== $_POST['confirmMdp']) {
            $info .= alert("Le mot de passe de confirmation n'est pas valide", "danger");
        }

        if (!isset($_POST['civility']) || !in_array($_POST['civility'], ['f', 'h'])) {
            $info .= alert("La civilité n'est pas valide", "danger");
        }

        $yearMin = ((int)date('Y')) - 13; // 2012
        $yearMax = ((int)date('Y')) - 90; // 1935
        // var_dump($yearMin);
        // var_dump($yearMax);
        // vérification de la date de naissance
        // Vérification de la date de naissance
        if (!isset($_POST['birthday'])) {
            $info .= alert("La date de naissance n'est pas valide", "danger");
        } else {
            $birthDate = $_POST['birthday'];
            $birthYear = (int)date('Y', strtotime($birthDate));
            if ($birthYear > $yearMin || $birthYear < $yearMax) {
                $info .= alert("L'année de naissance doit être comprise entre {$yearMax} et {$yearMin}", "danger");
            }
        }

        // vérification de l'adresse
        if (!isset($_POST['addressComplete']) || strlen(trim($_POST['addressComplete'])) > 50 || strlen(trim($_POST['addressComplete'])) < 5) {
            $info .= alert("L'adresse n'est pas valide", "danger");
        }

        // vérification du code postal
        $regexZip = "/^[0-9]{5}$/"; // 10 chiffres pour le code postal
        if (!isset($_POST['zip']) || !preg_match($regexZip, $_POST['zip'])) {
            $info .= alert("Le code postal n'est pas valide", "danger");
        }

        // vérification de la ville
        if (!isset($_POST['city']) || strlen(trim($_POST['city'])) > 50 || strlen(trim($_POST['city'])) < 2 || preg_match('/[0-9]/', $_POST['city'])) {
            $info .= alert("La ville n'est pas valide", "danger");
        }

        // vérification du pays
        if (!isset($_POST['country']) || strlen(trim($_POST['country'])) > 50 || strlen(trim($_POST['country'])) < 2 || preg_match('/[0-9]/', $_POST['country'])) {
            $info .= alert("Le pays n'est pas valide", "danger");
        }

        if (empty($info)) {
            $lastName = trim($_POST['lastName']);
            $firstName = trim($_POST['firstName']);
            $pseudo = trim($_POST['pseudo']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $mdp = trim($_POST['mdp']);
            // $confirmMdp = trim($_POST['confirmMdp']); // inutile de le récupérer car on a déjà vérifié que les deux mdp sont identiques
            $civility = trim($_POST['civility']);
            $birthday = trim($_POST['birthday']);
            $address = trim($_POST['addressComplete']);
            $zip = trim($_POST['zip']);
            $city = trim($_POST['city']);
            $country = trim($_POST['country']);

            $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

            // Cette fonction PHP crée un hachage sécurisé d'un mot de passe en utilisant un algorithme de hachage fort : génère une chaîne de caractères unique à partir d'une entrée. C'est un mécanisme unidirectionnel dont l'utilité est d'empêcher le déchiffrement d'un hash. Lors de la connexion, il faudra comparer le hash stocké dans la base de données avec celui du mot de passe fourni par l'internaute.
            // PASSWORD_DEFAULT : constante indique à password_hash() d'utiliser l'algorithme de hachage par défaut actuel c'est le plus recommandé car elle garantit que le code utilisera toujours le meilleur algorithme disponible sans avoir besoin de modifications.
            // debug($mdpHash);

            $emailExist = checkEmailUser($email);
            // debug($emailExist);
            $pseudoExist = checkPseudoUser($pseudo);
            // debug($pseudoExist);

            $emailPseudoExist = checkEmailPseudoUser($email, $pseudo);
            // debug($emailPseudoExist);

            #Vérification si l'email existe dans la BDD
            if ($emailExist) {
                $info = alert("L'email existe déjà", "warning");
            }

            // #vérification si le pseudo existe dans la BDD
            if ($pseudoExist) {
                $info = alert("Le pseudo existe déjà", "warning");
            }

            // #vérification si l'email et le pseudo correspondent au même utilisateur
            if ($emailPseudoExist) {
                $info = alert("Vous avez déjà un compte utilisateur", "info");
            } elseif (empty($info)) {
                addUser($lastName, $firstName, $pseudo, $email, $phone, $mdpHash, $civility, $birthday, $address, $zip, $city, $country);
                $info = alert("Vous êtes bien inscrit, vous pouvez vous connectez <a href='authentification.php' class='text-danger fw-bold'>ici</a>", "success");
            }
        }
    }
}
require_once("inc/header.inc.php");
?>

<main style="background:url(assets/img/5818.png) no-repeat; background-size: cover; background-attachment: fixed;">

    <div class="w-75 m-auto p-5" style="background: rgba(20, 20, 20, 0.9);">
        <h2 class="text-center mb-5 p-3">Créer un compte</h2>
        <?php
        echo $info;
        ?>

        <form action="" method="post" class="p-5">
            <div class="row mb-3">
                <div class="col-md-6 mb-5">
                    <label for="lastName" class="form-label mb-3">Nom</label>
                    <input type="text" class="form-control fs-5" id="lastName" name="lastName">
                </div>
                <div class="col-md-6 mb-5">
                    <label for="firstName" class="form-label mb-3">Prenom</label>
                    <input type="text" class="form-control fs-5" id="firstName" name="firstName">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 mb-5">
                    <label for="pseudo" class="form-label mb-3">Pseudo</label>
                    <input type="text" class="form-control fs-5" id="pseudo" name="pseudo">
                </div>
                <div class="col-md-4 mb-5">
                    <label for="email" class="form-label mb-3">Email</label>
                    <input type="text" class="form-control fs-5" id="email" name="email" placeholder="exemple.email@exemple.com">
                </div>
                <div class="col-md-4 mb-5">
                    <label for="phone" class="form-label mb-3">Téléphone</label>
                    <input type="text" class="form-control fs-5" id="phone" name="phone">
                </div>

            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-5 input-form">
                    <label for="mdp" class="form-label mb-3">Mot de passe</label>
                    <div class="input-form positionPassword">
                        <input type="password" class="form-control fs-5" id="mdp" name="mdp" placeholder="Entrer votre mot de passe">
                        <i id="show" class="bi bi-eye-slash-fill iconPassword"></i>
                    </div>
                </div>
                <div class="col-md-6 mb-5 input-form">
                    <label for="confirmMdp" class="form-label mb-3">Confirmation mot de passe</label>
                    <div class="input-form positionPasswordConfirm">
                        <input type="password" class="form-control fs-5 mb-3" id="confirmMdp" name="confirmMdp" placeholder="Confirmer votre mot de passe ">
                        <i id="show2" class="bi bi-eye-slash-fill iconPasswordConfirm"></i>
                    </div>
                    <!--<input type="checkbox" onclick="myFunction()"> <span class="text-danger">Afficher/masquer le mot de passe</span>-->
                </div>


            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-5">
                    <label class="form-label mb-3">Civilité</label>
                    <select class="form-select fs-5" name="civility">
                        <option value="h">Homme</option>
                        <option value="f">Femme</option>
                    </select>
                </div>
                <div class="col-md-6 mb-5">
                    <label for="birthday" class="form-label mb-3">Date de naissance</label>
                    <input type="date" class="form-control fs-5" id="birthday" name="birthday">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 mb-5">
                    <label for="address" class="form-label mb-3">Adresse</label>
                    <input type="text" class="form-control fs-5" id="addressComplete" name="addressComplete">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="zip" class="form-label mb-3">Code postale</label>
                    <input type="text" class="form-control fs-5" id="zip" name="zip">
                </div>
                <div class="col-md-5">
                    <label for="city" class="form-label mb-3">Cité</label>
                    <input type="text" class="form-control fs-5" id="city" name="city">
                </div>
                <div class="col-md-4">
                    <label for="country" class="form-label mb-3">Pays</label>
                    <input type="text" class="form-control fs-5" id="country" name="country">
                </div>
            </div>
            <div class="row mt-5">
                <button class="w-25 m-auto btn btn-danger btn-lg fs-5" type="submit">S'inscrire</button>
                <p class="mt-5 text-center">Vous avez dèjà un compte ! <a href="authentification.php" class=" text-danger">connectez-vous ici</a></p>
            </div>
        </form>
    </div>



</main>
<script src="assets/js/password.js"></script>
<?php
require_once("inc/footer.inc.php");
?>