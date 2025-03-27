<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$titlePage = "Ajouter et Mettre à jour film";
$descriptionPage = "Gérer les films";
require_once("../inc/functions.inc.php");

if (!isset($_SESSION['user'])) {
    // Si une session n'existe pas avec un identifiant utilisateur, je me redirige vers la page authentification.php
    header("location:" . RACINE_SITE . "authentification.php");
} else if( $_SESSION['user']['role'] != 'admin') {
    header("location:" . RACINE_SITE . "profil.php");
}

$categories = allCategories();
// je récupère toutes les catégories
// je les stocke dans une variable $categories


$info = "";
#### Traitement du formulaire ####
// je vérifie si l'utilisateur a cliqué sur le bouton créer
if (!empty($_POST)) {
    $verification = true;
    foreach ($_POST as $key => $value) {
        if (empty(trim($value))) {
            $verification = false;
        }
    }
    
    if ($verification === false) {
        $info = alert("Veuillez renseigner tous les champs", "danger");
    } else {
        // vérification du titre existant
        if (!isset($_POST['title']) || strlen(trim($_POST['title'])) > 50 || strlen(trim($_POST['title'])) < 2) {
            $info = alert("Le champs du titre de film n'est pas valide", "danger");
        }


        // vérification du réalisateur existant
        if (!isset($_POST['director']) || strlen(trim($_POST['director'])) > 50 || strlen(trim($_POST['director'])) < 2) {
            $info .= alert("Le champs du réalisateur n'est pas valide", "danger");
        }


        // vérification du synopsis existant
        if (!isset($_POST['synopsis']) || strlen(trim($_POST['synopsis'])) > 10000 || strlen(trim($_POST['synopsis'])) < 10) {
            $info .= alert("Le champs du synopsis n'est pas valide", "danger");
        }


        // vérification du stock existant
        if (!isset($_POST['stock']) || !is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
            $info .= alert("Le champs du stock n'est pas valide", "danger");
        }


        // regexDate permet de vérifier que la date est au format YYYY-MM-DD
        $regexDate = "/^\d{4}-\d{2}-\d{2}$/";
        // vérification de la date existante qui ne doit pas être supérieure à la date du jour
        if (!isset($_POST['date'])) {
            $info .= alert("Le champs de la date n'est pas valide", "danger");
        } elseif (!preg_match($regexDate, $_POST['date'])) {
            $info .= alert("Le date doit être au format AAAA-MM-JJ", "danger");
        } elseif (strtotime($_POST['date']) >= time()) {
            $info .= alert("La date de sortie ne peut pas être supérieure à la date du jour", "danger");
        }


        // Stockage des informations de l'image
        $fileImage = $_FILES['image'];
        $extension = pathinfo($fileImage['name'], PATHINFO_EXTENSION);
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif'];

        // // Vérification de l'existence de l'image avec un switch pour gérer les cas d'erreurs et les messages d'erreurs
        if ($fileImage['error'] == UPLOAD_ERR_NO_FILE) {
            // Utiliser l'image existante si aucune nouvelle image n'est téléchargée
            $image = htmlspecialchars(trim($_POST['existing_image']));
        } else{
            if ($fileImage['error'] != 0) {
                switch ($fileImage['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        $info .= alert("Le fichier est trop gros", "warning");
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $info .= alert("Le fichier est trop gros", "warning");
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $info .= alert("Le fichier n'a pas été entièrement téléchargé", "warning");
                        break;
                    // case UPLOAD_ERR_NO_FILE:
                    //     $info .= alert("Aucun fichier n'a été téléchargé", "warning");
                    //     break;
                    default:
                        $info .= alert("Erreur inconnue lors du téléchargement du fichier", "danger");
                }
            } elseif (!in_array($extension, $extensions_autorisees)) {
                $info .= alert("L'extension de l'image n'est pas valide", "info");
            } elseif ($fileImage['size'] > 2000000) {
                $info .= alert("L'image doit faire moins de 2Mo", "info");
            } elseif ($fileImage['error'] == 0) {
                // je déplace le fichier image dans le dossier img
                $path = "../assets/img/" . $_FILES['image']['name'];
                move_uploaded_file($fileImage['tmp_name'], $path);
                $image = $fileImage['name'];
            } else {
                $info .= alert("Erreur lors du téléchargement de l'image", "danger");
            }
        }


        // $regexActors permet de vérifier que les acteurs sont séparés par un / et que les acteurs ayant un tiret ou un point ne sont pas séparés par un /
        $regexChiffres = "/[0-9]/";
        $regexActors = "/.*\/.*/";
         //Explications: 
            //    .* : correspond à n'importe quel nombre de caractères (y compris zéro caractère), sauf une nouvelle ligne.
            //     \/ : correspond au caractère /. Le caractère / doit être précédé d'un backslash \ car il est un caractère spécial en expression régulière. Le backslash est appelé caractère d'échappement et il permet de spécifier que le caractère qui suit doit être considéré comme un caractère ordinaire.
            //     .* : correspond à n'importe quel nombre de caractères (y compris zéro caractère), sauf une nouvelle ligne.
            // vérification des acteurs existants

        if(!isset($_POST['actors']) ||  strlen($_POST['actors']) < 3 || preg_match($regexChiffres, $_POST['actors']) || !preg_match($regexActors, $_POST['actors']) ){ 
            $info .= alert("Le champ acteurs n'est pas valide, il faut séparer les acteurs avec le symbole","danger");
        }


        // vérification de la catégorie existante
        if (!isset($_POST['categorie_id']) || strlen(trim($_POST['categorie_id'])) > 50 || strlen(trim($_POST['categorie_id'])) < 2) {
            $info .= alert("Le champs de la catégorie n'est pas valide", "danger");
        }


        // vérification du prix existant
        if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
            $info .= alert("Le champs du prix n'est pas valide", "danger");
        }


        // vérification de la durée existante
        if (!isset($_POST['duration'])) {
            $info .= alert("Le champs de la durée n'est pas valide", "danger");
        }

        if(empty($info)){
            // je crée le film
            $category = htmlspecialchars(trim($_POST['categorie_id']));
            $title = htmlspecialchars(trim($_POST['title']));
            $director = htmlspecialchars(trim($_POST['director']));
            $actors = htmlspecialchars(trim($_POST['actors']));
            $ageLimit = htmlspecialchars(trim($_POST['ageLimit']));
            $duration = htmlspecialchars(trim($_POST['duration']));
            $date = htmlspecialchars(trim($_POST['date']));
            $price = htmlspecialchars(trim($_POST['price']));
            $stock = htmlspecialchars(trim($_POST['stock']));
            $synopsis = htmlspecialchars(trim($_POST['synopsis']));
            $image = str_replace(" ", "_", $image); // je remplace les espaces par des underscores
            $image = str_replace("'", "_", $image); // je remplace les apostrophes par des underscores
            $image = str_replace("(", "_", $image); // je remplace les parenthèses par des underscores
            $image = str_replace(")", "_", $image); // je remplace les parenthèses par des underscores
            
            // je vérifie si le film existe déjà
            $filmExiste = filmExist($title, $date);

            if (isset($_GET['action']) && $_GET['action'] == "update") {
                // je récupère l'id du film
                $idFilm = htmlspecialchars($_GET['id']);
                if($idFilm){
                    updateFilm($idFilm, $category, $title, $director, $actors, $ageLimit, $duration, $synopsis, $date, $image, $price, $stock);
                    $info = alert("Le film <b>". $title ."</b> a bien été modifié", "success");
                    // je modifie le film
                }
            }
            elseif($filmExiste) {
                $info .= alert("Ce film existe déjà", "info");
            } else {
                // je crée le film
                addFilm($category, $title, $director, $actors, $ageLimit, $duration, $synopsis, $date, $image, $price, $stock);

                $info = alert("Le film <b>". $title ."</b> a bien été créé", "success");
            }

        }
    }
}

$films = allFilms();

// Modification d'un film
if (isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == "update") {
    $idFilm = htmlspecialchars($_GET['id']);
    $film = showFilm($idFilm);
    // debug($film);
    $titleUpdate = $film['title'];
    $directorUpdate = $film['director'];
    $actorsUpdate = $film['actors'];
    $ageLimitUpdate = $film['ageLimit'];
    $durationUpdate = $film['duration'];
    $dateUpdate = $film['date'];
    $priceUpdate = $film['price'];
    $stockUpdate = $film['stock'];
    $synopsisUpdate = $film['synopsis'];
    $fileImageUpdate = $film['image'];
    $categoryUpdate = $film['categorie_id'];
    $submitUpdate = "Modifier le film";
}

require_once("../inc/header.inc.php");
?>

<main>
    <h2 class="text-center fw-bolder mb-5 text-danger">Formulaire d'ajout de film</h2>
        <?= $info ?>
        <form action="" method="post" class="back" enctype="multipart/form-data">
        <!-- il faut insérer une image pour chaque film, pour le traitement des images et des fichiers en PHP on utilise la surperglobal $_FILES -->
        <div class="row">
            <div class="col-md-6 mb-5">
                <label for="title" class="text-light">Titre de film</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= $titleUpdate ?? "" ?>">
            </div>
            <div class="col-md-6 mb-5">
                <label for="image" class="text-light">Photo</label>
                <br>                
                <input type="file" name="image" id="image">
            <!-- Champ caché pour stocker le nom de l'image existante -->
            <?php if (isset($fileImageUpdate)): ?>
                <input type="hidden" name="existing_image" value="<?= $fileImageUpdate ?? "" ?>">
                <img src="../assets/img/<?= $fileImageUpdate ?>" alt="affiche du film" class="img-fluid mt-2">
            <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-5">
                <label for="director" class="text-light">Réalisateur</label>
                <input type="text" class="form-control" id="director" name="director" value="<?= $directorUpdate ?? "" ?>">
            </div>
            <div class="col-md-6">
                <label for="actors" class="text-light">Acteur(s)</label>
                <input type="text" class="form-control" id="actors" name="actors" value="<?= $actorsUpdate ?? "" ?>" placeholder="séparez les noms d'acteurs avec un /">
            </div>
        </div>
        <div class="row">
            <!-- raccourci bs5 select multiple -->
            <div class="mb-3">
                <label for="ageLimit" class="form-label text-light">Âge limite</label>
                <select class="form-select form-select-lg" name="ageLimit" id="ageLimit">
                    <option value="10" <?= (isset($ageLimitUpdate) && $ageLimitUpdate == 10) ? 'selected' : '' ?>>10</option>
                    <option value="13" <?= (isset($ageLimitUpdate) && $ageLimitUpdate == 13) ? 'selected' : '' ?>>13</option>
                    <option value="16" <?= (isset($ageLimitUpdate) && $ageLimitUpdate == 16) ? 'selected' : '' ?>>16</option>
                </select>
            </div>
        </div>
        <div class="row">
            <label for="categories" class="text-light">Genre du film</label>

            <?php foreach($categories as $categorie): ?>
                <div class="form-check col-sm-12 col-md-4">

                    <input class="form-check-input" type="radio" name="categorie_id" id="<?= $categorie["id"]?>" value="<?= $categorie["id"]?>" <?= (isset($categoryUpdate) && $categoryUpdate == $categorie["id"]) ? 'checked' : '' ?>>

                    <label class="form-check-label text-light" for="<?= $categorie["id"]?>"><?= $categorie["nom"]?></label>
                </div>
            <?php endforeach; ?>

        </div>
        <div class="row">
            <div class="col-md-6 mb-5">
                <label for="duration" class="text-light">Durée du film</label>
                <input type="time" class="form-control" id="duration" name="duration"  min="01:00:00" value="<?= $durationUpdate ?? "" ?>">
            </div>

            <div class="col-md-6 mb-5">
                <label for="date" class="text-light">Date de sortie</label>
                <input type="date" name="date" id="date" class="form-control" value="<?= $dateUpdate ?? "" ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="price" class="text-light">Prix</label>
                <div class=" input-group">
                    <input type="text" class="form-control" id="price" name="price" aria-label="Euros amount (with dot and two decimal places)" value="<?= $priceUpdate ?? "" ?>" >
                    <span class="input-group-text">€</span>
                </div>
            </div>

            <div class="col-md-6">
                <label for="stock" class="text-light">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" min="0" value="<?= $stockUpdate ?? "" ?>"> <!--pas de stock négatif donc je rajoute min="0"-->
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <label for="synopsis" class="text-light">Synopsis</label>
                <textarea type="text" class="form-control" id="synopsis" name="synopsis" rows="10"><?= $synopsisUpdate ?? "" ?></textarea>
            </div>
        </div>

        <div class="row justify-content-center">
            <button type="submit" class="btn btn-danger p-3 w-25"><?= $submitUpdate ?? "Ajouter un film" ?></button>
        </div>

    </form>

</main>

<?php
require_once("../inc/footer.inc.php");
?>