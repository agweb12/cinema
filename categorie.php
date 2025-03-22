<?php
require_once("inc/functions.inc.php");
require_once("inc/header.inc.php");

// Vérification de l'ID de catégorie
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categorie_id = intval($_GET['id']);
    $films = allFilmsByCategory($categorie_id);
} else {
    die("ID de catégorie invalide ou non spécifié.");
}
?>

<h1>Films de la catégorie <?= htmlspecialchars($categorie_id) ?></h1>

<?php if (!empty($films)): ?>
    <div class="row">
        <?php foreach ($films as $film): ?>
        <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
            <div class="card">
                <img src="<?= RACINE_SITE ?>assets/img/<?= $film["image"] ?>" alt="image du film"> <!-- Affiche l'image du film -->
                <div class="card-body">
                    <h3><?= $film['title'] ?></h3> <!-- Affiche le titre du film -->
                    <h4><?= $film['director'] ?></h4> <!-- Affiche le réalisateur du film -->
                    <p><span class="fw-bolder">Résumé:</span> <?= substr($film['synopsis'], 0, 200) ?></p> <!-- Affiche un résumé du film -->
                    <a href="<?= RACINE_SITE ?>showFilm.php?id=<?=$film['id'] ?>" class="btn">Voir plus</a> <!-- Lien pour voir plus de détails -->
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <a class="btn btn-warning" href="index.php">Retour à l'accueil</a>
<?php else: ?>
    <p>Aucun film trouvé pour cette catégorie.</p>
<?php endif; ?>

<?php require_once("inc/footer.inc.php"); ?>