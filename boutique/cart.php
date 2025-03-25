<?php
require_once("../inc/functions.inc.php");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/*
Création d'une variable de session pour les messages :

Utilisez $_SESSION['info'] pour stocker les messages entre les redirections.
Récupération des messages au début du script :

Au début du script, récupérez le message stocké dans la session et effacez-le pour éviter qu'il ne s'affiche plusieurs fois.
Ajout des redirections manquantes :

Assurez-vous que toutes les actions (ajout, suppression) se terminent par une redirection et un exit().
 */

if(!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
// récupérer et effacer les messages stockés dans la session
$info = "";
if(isset($_SESSION['info'])) {
    $info = $_SESSION['info'];
    unset($_SESSION['info']);
}

if(!isset($_SESSION['order_token'])){
    $_SESSION['order_token'] = bin2hex(random_bytes(32)); // Générer un token unique pour la commande qui permet de vérifier l'authenticité de la commande et éviter les attaques CSRF (Cross-Site Request Forgery)
}
// Traitement de l'ajout au panier
if(isset($_POST['action']) && $_POST['action'] == 'ajout'){
    $id_film = (int) $_POST['id'];
    $quantity = (int) $_POST['quantity'];
    $title = htmlspecialchars($_POST['title']);
    $price = (float) $_POST['price'];
    $image = htmlspecialchars($_POST['image']);
    $stock = (int) $_POST['stock'];

    // vérifier si le film est déjà dans le panier
    if(isset($_SESSION['panier'][$id_film])) {
        // Vérifier si la nouvelle quantité totale ne dépasse pas le stock
        $nouvelle_quantite = $_SESSION['panier'][$id_film]['quantity'] + $quantity;
        if($nouvelle_quantite > $stock) {
            $_SESSION['info'] = alert("La quantité demandée dépasse le stock disponible", "warning");
            header('Location: '. RACINE_SITE .'boutique/cart.php');
            exit();
        }
        // Mettre à jour la quantité et le sous-total
        $_SESSION['panier'][$id_film]['quantity'] = $nouvelle_quantite;
        $_SESSION['panier'][$id_film]['subtotal'] = $nouvelle_quantite * $price;
    } else {
        // Vérifier si la quantité demandée dépasse le stock
        if($quantity > $stock) {
            $_SESSION['info'] = alert("Stock Insuffisant pour le film <b>". $title ."</b>", "danger");
            header('Location: '. RACINE_SITE .'showFilm.php?id='. $id_film);
            exit();
        }

        // Ajouter le film au panier
        $_SESSION['panier'][$id_film] = [
            'id' => $id_film,
            'title' => $title,
            'price' => $price,
            'image' => $image,
            'quantity' => $quantity,
            'stock' => $stock,
            'subtotal' => $quantity * $price
        ];
    }
    $_SESSION['info'] = alert("Le film <b>". $title ."</b> a bien été ajouté au panier", "success");
    header('Location: '. RACINE_SITE .'boutique/cart.php');
    exit();
}
// Traitement de la suppression du film du panier
if (isset($_GET['action']) && $_GET['action'] == 'supprimer') {
    $id_film = (int)$_GET['id'];
    if (isset($_SESSION['panier'][$id_film])) {
        unset($_SESSION['panier'][$id_film]);
        // $_SESSION['message'] = [
        //     'type' => 'success',
        //     'text' => 'Film retiré du panier !'
        // ];
        $_SESSION['info'] = alert("Film retiré du panier !", "success");
    }
    header('Location: ' . RACINE_SITE . 'boutique/cart.php');
    exit();
}
// Traitement de la suppression de tous les films du panier
if (isset($_GET['vider']) && $_GET['vider'] == 1) {
    unset($_SESSION['panier']);
    $_SESSION['info'] = alert("Le panier a été vidé !", "success");
    header('Location: ' . RACINE_SITE . 'boutique/cart.php');
    exit();
}

// Traitement pour créer la commande
if(isset($_POST['action']) && $_POST['action'] == 'payer'){
    // Vérifier si l'utilisateur est connecté
    if(!isset($_SESSION['user'])){
        $_SESSION['info'] = alert("Veuillez vous connecter pour finaliser votre commande", "warning");
        header('Location:' .RACINE_SITE. 'authentification.php');
        exit();
    }

    // Vérifier le token
    if(!isset($_POST['token']) || !isset($_SESSION['order_token']) || $_POST['token'] !== $_SESSION['order_token']) {
        $_SESSION['info'] = alert("Erreur de validation du formulaire", "danger");
        header('Location: ' . RACINE_SITE . 'boutique/cart.php');
        exit();
    }
    
    // Supprimer le token
    unset($_SESSION['order_token']);

    // Vérifier si le panier n'est pas vide
    if(empty($_SESSION['panier'])){
        $_SESSION['info'] = alert("Votre panier est vide", "danger");
        header('Location:' .RACINE_SITE. 'boutique/cart.php');
        exit();
    }

    // Calculer le total du panier
    $total_panier = 0;
    foreach($_SESSION['panier'] as $film){
        $total_panier += $film['subtotal'];
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $_SESSION['user']['id'];
    try{
        // Utiliser la fonction addOrder pour créer la commande dans la table orders
        // Cette fonction ajoute une nouvelle commande avec le statut "en attente" par défaut
        // echo "Ajout d'une commande pour l'utilisateur ID: $user_id avec un total de: $total_panier<br>";

        // Enregistre le résultat explicitement pour le déboguer
        $order_id = addOrder($user_id, $total_panier);

        // Vérifier si l'ID est valide
        if (!is_numeric($order_id) || $order_id <= 0) {
            throw new Exception("ID de commande invalide après addOrder: " . var_export($order_id, true));
        }

        // echo "Commande créée avec l'ID: $order_id<br>";

        // // Récupérer l'ID de la dernière commande insérée
        // $pdo = connexionBDD();
        // $order_id = $pdo->lastInsertId();

        // Ajouter les détails de la commande pour chaque film du panier
        // echo "Début de l'ajout des détails de commande...<br>";
        foreach($_SESSION['panier'] as $film){
            // Utiliser la fonction addOrderDetails pour ajouter chaque film dans la table order_details
            // echo "Ajout de détails pour le film ID: {$film['id']}, quantité: {$film['quantity']}<br>";

            // Vérifier les valeurs avant l'appel
            // echo "Valeurs passées: order_id=$order_id, film_id={$film['id']}, price={$film['price']}, quantity={$film['quantity']}<br>";

            addOrderDetails($order_id, $film['id'], $film['price'], $film['quantity']);

            // echo "Détails ajoutés avec succès pour le film ID: {$film['id']}<br>";
        }
        // echo "Tous les détails ont été ajoutés avec succès!<br>";

        // Stocker l'ID de la commande en session pour le récupérer dans checkout.php
        $_SESSION['current_order_id'] = $order_id;
        // echo "ID de commande stocké en session: {$_SESSION['current_order_id']}<br>";
        // Rediriger vers la page de paiement (checkout.php)
        $_SESSION['info'] = alert("Votre commande a été créée avec succès. Veuillez procéder au paiement.", "success");
        header('Location: ' . RACINE_SITE . 'boutique/checkout.php');
        exit();
        // echo "<a href='" . RACINE_SITE . "boutique/checkout.php'>Continuer vers la page de paiement</a>";

    } catch(Exception $e){
        echo "Erreur: " . $e->getMessage() . "<br>";
        echo "Trace: " . $e->getTraceAsString() . "<br>";
        // En cas d'erreur, afficher un message d'erreur
        $_SESSION['info'] = alert("Une erreur est survenue lors de la création de la commande : " . $e->getMessage(), "danger");
        // header('Location: ' . RACINE_SITE . 'boutique/cart.php');
        // exit();
    }

}
$total = 0;

require_once("../inc/header.inc.php");
?>

<div class="panier d-flex justify-content-center" style="padding-top:8rem;">
        <div class="d-flex flex-column  mt-5 p-5">
            <h2 class="text-center fw-bolder mb-5 text-danger">Mon panier</h2>
            <?= $info; ?>
            <?php if (empty($_SESSION['panier'])): ?>
                <div class="alert alert-info">
                    Votre panier est vide
                </div>
            <?php else: ?>
                    <!-- le paramètre vider=1 pour indiquer qu'il faut vider le panier. -->
                    <a href="<?= RACINE_SITE ?>boutique/cart.php?vider=1" class="btn align-self-end mb-5">Vider le panier</a>

                <table class="fs-4">
                    <thead>
                        <tr>
                            <th class="text-center text-danger fw-bolder">Affiche</th>
                            <th class="text-center text-danger fw-bolder">Nom</th>
                            <th class="text-center text-danger fw-bolder">Prix</th>
                            <th class="text-center text-danger fw-bolder">Quantité</th>
                            <th class="text-center text-danger fw-bolder">Sous-total</th>
                            <th class="text-center text-danger fw-bolder">Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['panier'] as $films): ?>
                            <?php $total += $films['subtotal']; ?>
                        <tr>
                            <td class="text-center border-top border-dark-subtle"><a href=""><img src="<?= RACINE_SITE ?>assets/img/<?= $films['image'] ?>" style="width: 100px;"></a></td>
                            <td class="text-center border-top border-dark-subtle"><?= $films['title']; ?></td>
                            <td class="text-center border-top border-dark-subtle"><?= number_format($films['price'], 2) ?> €</td>
                            <td class="text-center border-top border-dark-subtle d-flex align-items-center justify-content-center" style="padding: 7rem;"><?= $films['quantity'] ?></td>
                            <td class="text-center border-top border-dark-subtle"><?= number_format($films['subtotal'], 2) ?> €</td>
                            <td class="text-center border-top border-dark-subtle"><a href="cart.php?action=supprimer&id=<?= $films['id'] ?>"><i class="bi bi-trash3"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tr class="border-top border-dark-subtle">
                        <th class="text-danger p-4 fs-3">Total :</th>
                        <th class="text-center text-danger p-4 fs-3"><?= number_format($total, 2) ?> €</th>
                    </tr>
               </table>
                <form action="cart.php" method="post">
                    <input type="hidden" name="action" value="payer">
                    <input type="hidden" name="total" value="<?= $total ?>">
                    <input type="hidden" name="token" value="<?= $_SESSION['order_token'] ?>">
                    <button type="submit" class="btn btn-danger mt-5 p-3" id="checkout-button">Passer à la commande</button>
              </form>
        </div>
        <?php endif; ?>
    </div>

<?php
require_once("../inc/footer.inc.php");
?>