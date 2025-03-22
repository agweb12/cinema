<?php
$titlePage = "Accueil";
$descriptionPage = "Bienvenue sur le site de location de films";
require_once("inc/functions.inc.php");
// debug($_SESSION["user"]);
// debug($_SESSION["panier"]);
$films = sixFilms();
$allFilms = allFilms();
$info = "";

require_once("inc/header.inc.php");
?>

<div class="films">
    <h2 class="fw-bolder fs-1 mx-5 text-center">
        <?php 
            if(isset($_GET['view']) && $_GET['view'] == 'allfilms'){
               echo "Tous les films : " . count($allFilms);
            } else{
               echo "Les ". count($films) . " derniers films ajoutés";
            }  
            ?>
    </h2> <!-- Affiche le message et le nombre de films -->

    <div class="row">
    <?php if(isset($_GET['view']) && $_GET['view'] == "allfilms"): ?>
        <?php foreach($allFilms as $film): ?>
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
    <?php else: ?>
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
    <?php endif; ?>

    </div>
    <div class="col-12 text-center">
        <a href="<?= RACINE_SITE ?>index.php?view=allfilms" class="btn p-4 fs-3">Voir plus de films</a> <!-- Lien pour voir plus de films -->
    </div> 
</div>

<?php
require_once("inc/footer.inc.php");
?>
