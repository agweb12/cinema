<?php
require_once("../inc/functions.inc.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    $_SESSION['info'] = alert("Veuillez vous connecter pour finaliser votre commande", "danger");
    header("Location: " . RACINE_SITE . "authentification.php");
    exit();
}

// Vérifier si une commande en cours existe
if (!isset($_SESSION['current_order_id'])) {
    $_SESSION['info'] = alert("Aucune commande en cours", "warning");
    header("Location: " . RACINE_SITE . "boutique/cart.php");
    exit();
}

// Récupérer l'ID de la commande en cours
$order_id = $_SESSION['current_order_id'];

// Récupérer les informations de la commande depuis la base de données
$order = getOrder($order_id);

// Vérifier si la commande existe ou pas
if (!$order) {
    $_SESSION['info'] = alert("Commande introuvable", "danger");
    header("Location: " . RACINE_SITE . "boutique/cart.php");
    exit();
}

// Récupérer les détails de la commande
$order_details = getOrderDetails($order_id);
// debug($order_details);
// Récupérer le total de la commande
$total = $order['price'];

// Informations utilisateur
$user = $_SESSION['user'];

// Traitement du formulaire de confirmation de commande
if (isset($_POST['confirm_order'])) {
    if (isset($_POST['payment_method'])) {
        // Mettre à jour le statut de la commande en "payé" lorsque l'utilisateur paye la commande
        payOrder($order_id);
        // Sauvegarder les détails de la commande pour affichage dans la page de succès
        $_SESSION['lastOrder'] = $order_details;
        $_SESSION['lastOrderTotal'] = $total;
        
        // Vider le panier
        unset($_SESSION['panier']);
        
        // Supprimer l'ID de la commande en cours
        unset($_SESSION['current_order_id']);
        
        $_SESSION['info'] = alert("Votre commande a été validée avec succès !", "success");
        header("Location: " . RACINE_SITE . "boutique/success.php");
        exit();
    } else {
        $error = "Veuillez sélectionner un mode de paiement";
    }
}

// Inclure l'en-tête
require_once("../inc/header.inc.php");
?>

<div class="container mt-5" style="padding-top:8rem;">
    <h2 class="text-center fw-bolder mb-5 text-danger">Finalisation de la commande</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h3>Récapitulatif de la commande #<?= $order_id ?></h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Film</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            echo "order details";
                            debug($order_details);
                            echo "order";
                            debug($order);
                            echo "session de l'ID de la commande en cours";
                            debug($_SESSION['current_order_id']);
                            echo "session du panier";
                            debug($_SESSION['panier']);
                            ?>
                            <?php foreach($order_details as $detail):
                                // debug($detail);
                                ?>
                            <tr>
                                <td><?= $detail['title'] ?></td>
                                <td><?= $detail['quantity'] ?></td>
                                <td><?= number_format($detail['price_film'] * $detail['quantity'], 2) ?> €</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th><?= number_format($total, 2) ?> €</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3>Informations de livraison</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nom :</strong> <?= $user['lastName'] ?></p>
                    <p><strong>Prénom :</strong> <?= $user['firstName'] ?></p>
                    <p><strong>Adresse :</strong> <?= $user['addressComplete'] ?></p>
                    <p><strong>Code postal :</strong> <?= $user['zip'] ?></p>
                    <p><strong>Ville :</strong> <?= $user['city'] ?></p>
                    <p><strong>Pays :</strong> <?= $user['country'] ?></p>
                    <p><strong>Email :</strong> <?= $user['email'] ?></p>
                    <p><strong>Téléphone :</strong> <?= $user['phone'] ?></p>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h3>Paiement</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="order_id" value="<?= $order_id ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Méthode de paiement</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                <label class="form-check-label" for="card">Carte bancaire</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">PayPal</label>
                            </div>
                        </div>
                        
                        <button type="submit" name="confirm_order" class="btn btn-danger w-100">Confirmer et payer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once("../inc/footer.inc.php");
?>