<?php
require_once("../inc/functions.inc.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: " . RACINE_SITE . "authentification.php");
    exit();
}

// Récupérer les informations de la dernière commande
$lastOrder = isset($_SESSION['lastOrder']) ? $_SESSION['lastOrder'] : [];
$total = isset($_SESSION['lastOrderTotal']) ? $_SESSION['lastOrderTotal'] : 0;

require_once("../inc/header.inc.php");
?>

<div class="container text-center" style="padding-top:8rem;">
    <div class="card w-75 mx-auto mt-5">
        <div class="card-body">
            <h2 class="card-title text-success mb-4"><i class="bi bi-check-circle-fill"></i> Commande réussie !</h2>
            <?= isset($_SESSION['info']) ? $_SESSION['info'] : ''; ?>
            <?php unset($_SESSION['info']); ?>
            
            <p class="card-text fs-5 mb-4">Merci pour votre achat, <?= $_SESSION['user']['firstName'] ?> !</p>
            
            <?php if (!empty($lastOrder)): ?>
            <div class="mt-4">
                <h3 class="text-danger">Récapitulatif de votre commande</h3>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($lastOrder as $detail): ?>
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
            <?php endif; ?>
            
            <div class="mt-4">
                <p>Un email de confirmation a été envoyé à votre adresse : <?= $_SESSION['user']['email'] ?></p>
                <p>Votre commande sera bientôt disponible.</p>
            </div>
            
            <div class="d-flex justify-content-center gap-3 mt-5">
                <a href="<?= RACINE_SITE ?>index.php" class="btn btn-outline-danger">Retour à l'accueil</a>
                <a href="<?= RACINE_SITE ?>profil.php" class="btn btn-danger">Voir mon profil</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once("../inc/footer.inc.php");
?>