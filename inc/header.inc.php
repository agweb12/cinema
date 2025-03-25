<!doctype html>
<html lang="fr">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="<?= $descriptionPage ?>">
    <meta name="author" content="Alexandre Graziani">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--  icone bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!--  Line vers google font  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="<?= RACINE_SITE; ?>assets/css/root.css">
    <link rel="stylesheet" href="<?= RACINE_SITE; ?>assets/css/style.css">

    <title><?= $titlePage ?></title>
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid">
                <h1><a class="navbar-brand" href="">M <img src="<?= RACINE_SITE; ?>assets/img/logo.png" alt=""> VIES</a></h1>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav w-100 d-flex justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link " aria-current="page" href="<?= RACINE_SITE; ?>index.php">Accueil</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Catégories
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach($categories as $categorie): ?>
                                    <li><a class="dropdown-item fs-4" href="<?= RACINE_SITE; ?>categorie.php?id=<?= $categorie['id']; ?>"><?= $categorie['nom']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <?php
                        // Si l'utilisateur n'est pas connecté, on affiche les liens d'inscription et de connexion
                        if (!isset($_SESSION['user'])):
                        ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RACINE_SITE; ?>register.php">Inscription</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RACINE_SITE; ?>authentification.php">Connexion</a>
                            </li>
                        <?php
                        endif;
                        ?>
                        <?php if (isset($_SESSION['user'])): ?><?php endif; ?>

                        <?php if (isset($_SESSION['user'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= RACINE_SITE; ?>profil.php">Compte <sup class="badge rounded-pill text-bg-danger"></sup></a>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user'])): ?><?php endif; ?>
                        
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false">Backoffice</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item fs-4" href="<?= RACINE_SITE; ?>admin/categories.php">Catégories</a></li>
                                    <li><a class="dropdown-item fs-4" href="<?= RACINE_SITE; ?>admin/films.php">Films</a></li>
                                    <li><a class="dropdown-item fs-4" href="<?= RACINE_SITE; ?>admin/filmForm.php">Gestion films</a></li>
                                    <li><a class="dropdown-item fs-4" href="<?= RACINE_SITE; ?>admin/users.php">Utilisateurs</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="?action=deconnexion">Déconnexion</a>
                            </li>
                        <?php endif; ?>
                        <?php if (!isset($_SESSION['user'])): ?><?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= RACINE_SITE; ?>boutique/cart.php"><i class="bi bi-cart fs-2">
                                <?php if(isset($_SESSION['panier'])): ?>
                                    <sup>
                                        <?= count($_SESSION['panier']) ?>
                                    </sup>
                                <?php else: ?>
                                    <sup></sup>
                                <?php endif; ?>
                            </i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>