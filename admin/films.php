<?php
$titlePage = "Gestion films";
$descriptionPage = "Gérer les films";
require_once("../inc/functions.inc.php");

if (!isset($_SESSION['user'])) {
    // Si une session n'existe pas avec un identifiant utilisateur, je me redirige vers la page authentification.php
    header("location:" . RACINE_SITE . "authentification.php");
} else if( $_SESSION['user']['role'] != 'admin') {
    header("location:" . RACINE_SITE . "profil.php");
}

$films = allFilms();
$info = "";

if(isset($_GET['action']) && isset($_GET['id'])){
    $idFilm = htmlspecialchars($_GET['id']);

    if (!empty($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id'])) {
        // je supprime le film
        deleteFilm($idFilm);
        $info = alert("Le film <b>". $title ."</b> a bien été supprimé", "info");
    }
    header("location:films.php");
}
require_once("../inc/header.inc.php");
?>

<div class="d-flex flex-column m-auto mt-5">
    
    <h2 class="text-center fw-bolder mb-5 text-danger">Liste des films</h2>
    <a href="filmForm.php" class="btn align-self-end">Ajouter un film</a>
    <table class="table table-dark table-bordered mt-5">
            <thead>
                <tr >
                <!-- th*7 -->
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Affiche</th>
                    <th>Réalisateur</th>
                    <th>Acteurs</th>
                    <th>Âge limite</th>
                    <th>Genre</th>
                    <th>Durée</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Synopsis</th>
                    <th>Date de sortie</th>
                    <th>Supprimer</th>
                    <th>Modifier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($films as $film): ?>
                <tr>
                    <!-- Je récupére les valeurs de mon tableau $film dans des td -->
                    <td><?= html_entity_decode($film['id']) ?></td>
                    <td><?= ucfirst(html_entity_decode($film['title'])) ?></td>
                    <td><img src="../assets/img/<?= html_entity_decode($film['image'])?>" alt="affiche du film" class="img-fluid"></td>
                    <td><?= ucfirst(html_entity_decode($film['director'])) ?></td>
                    <td> 
                        <ul>
                            <?php 
                            $actors = explode('/', trim($film['actors'], '/'));
                            // debug($actors);
                            foreach($actors as $actor): ?>

                                <li><?= ucfirst(html_entity_decode($actor)) ?></li>

                            <?php endforeach;?>
                        </ul>
                    </td>
                    <td><?= html_entity_decode($film['ageLimit']) ?></td>
                    <td><?= html_entity_decode($film['genre']) ?></td>
                    <td><?= html_entity_decode($film['duration']) ?></td>
                    <td><?= html_entity_decode($film['price']) ?> €</td>
                    <td><?= html_entity_decode($film['stock']) ?></td>
                    <td><?= ucfirst(substr(html_entity_decode($film['synopsis']), 0, 200)) ?> ...</td>
                    <td><?= html_entity_decode($film['date']) ?></td>
                    <td class="text-center"><a href="films.php?action=delete&id=<?= $film['id'] ?>"><i class="bi bi-trash3-fill"></i></a></td>
                    <td class="text-center"><a href="filmForm.php?action=update&id=<?= $film['id'] ?>"><i class="bi bi-pen-fill text-warning"></i></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
</div>


<?php
require_once("../inc/footer.inc.php");
?>