<?php
$titlePage = "Accueil";
$descriptionPage = "Bienvenue sur le site de location de films";
require_once("inc/functions.inc.php");
// debug($_SESSION["user"]);
// debug($_SESSION["panier"]);
$films = sixFilms();
$info = "";

require_once("inc/header.inc.php");
?>

<div class="films">
    <h2 class="fw-bolder fs-1 mx-5 text-center">Tous les films : <?= count($films) ?></h2> <!-- Affiche le message et le nombre de films -->

    <div class="row">
    <?php foreach($films as $film): ?>
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
    <div class="col-12 text-center">
        <a href="" class="btn p-4 fs-3">Voir plus de films</a> <!-- Lien pour voir plus de films -->
    </div> 
</div>

<?php
require_once("inc/footer.inc.php");
?>
