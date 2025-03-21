<?php
$titlePage = "Gestion Catégories";
$descriptionPage = "Gérer les catégories";

require_once("../inc/functions.inc.php");

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    // Si une session n'existe pas avec un identifiant utilisateur, je me redirige vers la page authentification.php
    if(!empty($_POST['action']) && $_POST['action'] === 'updateCategory'){
        echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
        exit;
    }
    header("location:" . RACINE_SITE . "authentification.php");
    exit;
} else if( $_SESSION['user']['role'] != 'admin') {
    header("location:" . RACINE_SITE . "profil.php");
}


// Mise à jour de la catégorie
if(!empty($_POST['action']) && $_POST['action'] === 'updateCategory'){
    ini_set('display_errors', 0); // Désactive l'affichage des erreurs
    error_reporting(0); // Désactive les rapports d'erreurs
    error_log("Requête AJAX reçue avec les données :" .json_encode($_POST));
    header('Content-Type: application/json');    //ajout de l'entête json

    // récupération des données
    $id = intval($_POST['id']); //
    $nom = htmlspecialchars(trim($_POST['nom']));
    $description = htmlspecialchars(trim($_POST['descriptif']));

    // vérification des données
    if(!empty($id) && !empty($nom) && !empty($description)){
        // error_log("Données valides:  ID= $id, nom=$nom, descriptif=$description");
        $result = updateCategorie($id, $nom, $description);
        //

        //vérifier si la mise à jour a réussi
        if($result){
            echo json_encode(['success' => true, 'message' => 'Catégorie mise à jour avec succès']);
        } else{
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la catégorie']);// error_log("Données invalides");
        }
    } else{
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis.']); // error_log("Données requises");
    }
    exit; //
}

$categories = allCategories();
$info = "";

// je vérifie si l'utilisateur a cliqué sur le bouton créer
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
        // vérification du lastname existant
        if (!isset($_POST['nom']) || strlen(trim($_POST['nom'])) > 50 || strlen(trim($_POST['nom'])) < 2) {
            $info = alert("Le champs du nom de la catégorie n'est pas valide", "danger");
        }

        // vérification du firstname existant
        if (!isset($_POST['descriptif']) || strlen(trim($_POST['descriptif'])) > 10000 || strlen(trim($_POST['descriptif'])) < 10) {
            $info .= alert("Le champs de la description n'est pas valide", "danger");
        }
    }

    if (empty($info)) {
        $nom = htmlspecialchars(trim($_POST['nom']));
        $description = htmlspecialchars(trim($_POST['descriptif']));

        $categorieExiste = categorieExist($nom);

        if($categorieExiste) {
            $info .= alert("Cette catégorie existe déjà", "danger");
        } elseif(empty($info)) {
            //  je crée la catégorie
            createCategorie($nom, $description);
            $info = alert("La catégorie <b>". $nom ."</b> a bien été créée", "success");
        }
    }
}
// je vérifie si l'utilisateur a cliqué sur le bouton supprimer
if(isset($_GET['action']) && isset($_GET['nom'])){
    $nomCategorie = htmlspecialchars($_GET['nom']);

    if (!empty($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['nom'])) {
        // je supprime la catégorie

        deleteCategorie($nomCategorie);
        $info = alert("La catégorie <b>". $nomCategorie ."</b> a bien été supprimée", "info");
    }
    header("location:categories.php");
}
require_once("../inc/header.inc.php");
?>
<div class="row mt-5" style="padding-top: 8rem;">
    <div class="col-sm-12 col-md-6 mt-5">
        <h2 class="text-center fw-bolder mb-5 text-danger">Gestion des catégories</h2>
        <form action="" method="post" class="back">
            <div class="row">
                <?= $info ?>
                <div class="col-md-8 mb-5">
                    <label class="text-white" for="name">Nom de la catégorie</label>
                    <input type="text" id="name" name="nom" class="form-control" value=""> 
                </div>
                <div class="col-md-12 mb-5">
                    <label class="text-white" for="description">Description</label>
                    <textarea id="description"  name="descriptif" class="form-control" rows="10"></textarea>
                </div>
            </div>
            <div class="row justify-content-center">
                <button type="submit" class="btn btn-danger p-3">Créer la catégorie</button>
            </div>
        </form>
    </div>

    <div class="col-sm-12 col-md-6 d-flex flex-column mt-5 pe-3">
        <h2 class="text-center fw-bolder mb-5 text-danger">Liste des catégories</h2>
        <div id="alertMessage" class="alert alert-dismissible fade" role="alert">
            <span id="alertText"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="progress mt-2" style="height: 3px;">
                <div id="alertProgress" class="progress-bar" role="progressbar" style="width: 100%"></div>
            </div>
        </div>
        <table class="table table-dark table-bordered mt-5 " >
            <thead>
                    <tr>
                    <!-- th*7 -->
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Supprimer</th>
                        <th>Modifier</th>
                    </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $categorie): ?>
                    <tr data-id="<?= $categorie['id'] ?>">
                        <td name="id<?= html_entity_decode($categorie['id']);?>" id="id<?= html_entity_decode($categorie['id']);?>"><?= html_entity_decode($categorie['id']);?></td>
                        <td>
                            <input type="text" name="nom<?= html_entity_decode($categorie['id']);?>" id="nom<?= html_entity_decode($categorie['id']);?>" value="<?= html_entity_decode($categorie['nom']);?>" class="w-100 bg-transparent text-white inputChange btn btn-outline-danger fs-3" disabled>
                        </td>
                        <td>
                            <div class="input-form mb-3">
                                <textarea class="w-100 bg-transparent text-white inputChange fs-3" rows="6" name="descriptif<?= html_entity_decode($categorie['id']);?>" id="descriptif<?= html_entity_decode($categorie['id']);?>" aria-describedby="modify<?= html_entity_decode($categorie['id']); ?>" disabled><?= substr(html_entity_decode($categorie['descriptif']), 0, 200)." [...]";?></textarea>
                            </div>
                        </td>
                        <td class="text-center"><a href="categories.php?action=delete&nom=<?= $categorie['nom'] ?>" onclick="return(confirm('êtes-vous sur de vouloir supprimer cette catégorie ?'))"><i class="bi bi-trash3-fill"></i></a></td>

                        <td class="text-center" id="iconActions">
                            <i class="bi bi-pen-fill" onclick="createButtonModify(<?= $categorie['id'] ;?>)"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once("../inc/footer.inc.php");
?>