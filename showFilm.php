<?php
$titlePage = "Film";
$descriptionPage = "Découvrez le film";
require_once("inc/functions.inc.php");

if(!isset($_SESSION['user'])) {
    header("location:authentification.php");
}else{
    $user = $_SESSION['user'];
}


$idFilm = htmlspecialchars($_GET['id']);
$film = showFilm($idFilm);
$filmCategory = showFilmWithCategory($idFilm);

require_once("inc/header.inc.php");

?>

<div class="film bg-dark">

        <div class="back">
            <a href="<?= RACINE_SITE ?>index.php"><i class="bi bi-arrow-left-circle-fill"></i></a>
        </div>
        <div class="cardDetails row mt-5">
            <h2 class="text-center mb-5"></h2>
            <div class="col-12 col-xl-5 row p-5">
                <img src="<?= RACINE_SITE ?>assets/img/<?= $film['image'] ?>" alt="Affiche du film">
                <div class="col-12 mt-5">
                    <form action="<?= RACINE_SITE ?>boutique/cart.php" method="post" class="w-75 m-auto row justify-content-center p-5">
     <!-- Champs cachés pour transmettre les informations du film -->
                        <input type="hidden" name="id" value="<?= $film['id'] ?>">
                        <input type="hidden" name="title" value="<?= $film['title'] ?>">
                        <input type="hidden" name="price" value="<?= $film['price'] ?>">
                        <input type="hidden" name="image" value="<?= $film['image'] ?>">
                        <input type="hidden" name="stock" value="<?= $film['stock'] ?>">
                        <select name="quantity" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                            <!-- Sélecteur pour choisir la quantité -->
                            <option selected>Quantité</option>
                            <!-- Je crée une boucle pour afficher le nombre de film en stock -->
                            <?php for($i=1; $i <= $film['stock']; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <!-- Bouton pour ajouter au panier -->
                        <button class="m-auto btn btn-danger btn-lg fs-5" type="submit" name="action" value="ajout">Ajouter au panier</button>
                        <!-- je crée une session panier . Comment faire ? -->

                    </form>
                </div>
            </div>
            <div class="detailsContent  col-md-7 p-5">
                <div class="container mt-5">
                    <div class="row">
                        <h3 class="col-4"><span>Realisateur :</span></h3>
                        <ul class="col-8">
                            <li><?= $film['director'] ?></li>
                        </ul>
                        <hr>
                    </div>
                    <div class="row">
                        <h3 class="col-4"><span>Acteur :</span></h3>
                        <ul class="col-8">
                            <?php $actors =  explode('/', $film['actors']); ?>
                            <?php foreach($actors as $actor): ?>
                                <li><?= ucfirst(html_entity_decode($actor)) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                    </div>

                    <!-- // si j'ai un age limite renseigné je l'affiche si non pas de div avec Àge limite : -->

                    <div class="row">
                        <h3 class="col-4"><span>Àge limite :</span></h3>
                        <ul class="col-8">
                                <li>+ <?= $film['ageLimit'] ?> ans</li>    
                        </ul> 
                        <hr>
                    </div>


                </div>
            </div>
            <div class="row">
                <h3 class="col-4"><span>Genre : </span></h3>
                <ul class="col-8">
                    <li><?= $filmCategory['genre'] ?></li>
                </ul>
                <hr>
            </div>
            <div class="row">
                <h3 class="col-4"><span>Durée : </span></h3>
                <ul class="col-8">
                    <li><?= $film['duration'] ?></li>
                </ul>
                <hr>
            </div>
            <div class="row">
                <h3 class="col-4"><span>Date de sortie:</span></h3>
                <ul class="col-8">
                                       <li><?= $film['date'] ?></li>
                </ul>
                <hr>
            </div>
            <div class="row">
                <h3 class="col-4"><span>Prix : </span></h3>
                <ul class="col-8">
                    <li><?= $film['price'] ?> €</li>
                </ul>
                <hr>
            </div>
            <div class="row">
                <h3 class="col-4"><span>Stock :</span> </h3>
                <ul class="col-8">
                    <li><?= $film['stock'] ?></li>
                </ul>
                <hr>
            </div>
            <div class="row">

                <h5 class="col-4"><span>Synopsis :</span></h5>
                <ul class="col-8">
                    <li><?= $film['synopsis'] ?></li>
                </ul>
            </div>
        </div>
    </div>

<?php
require_once("inc/footer.inc.php");
?>
