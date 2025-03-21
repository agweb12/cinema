<?php
$titlePage = "Profil";
$descriptionPage = "Bienvenue sur votre profil";
require_once("inc/functions.inc.php");
// $user = showUser(htmlspecialchars($_SESSION['user']['id']));
// debug($_SESSION['user']['role']);

if(!isset($_SESSION['user'])) {
    header("location:authentification.php");
}else{
    $user = $_SESSION['user'];
}
require_once("inc/header.inc.php");
// debug($_GET);
?>
<div class="mx-auto p-2 row flex-column align-items-center">
    <h2 class="text-center mb-5">Bonjour <?= $_SESSION['user']['firstName'] ?></h2>
    <h3 class="text-center mb-5">Je suis 
        <?php 
        if(isset($_SESSION['user']) && $_SESSION['user']['civility'] == "f") {
            echo "née";
        } else if(isset($_SESSION['user']) && $_SESSION['user']['civility'] == "h") {
            echo "né";
        }
        ?>
        le <?= $user['birthday']?></h3>
    <div class="cardFilm">
        <div class="image">

            <img src="<?php if(isset($_SESSION['user']) && $_SESSION['user']['civility'] == "f") {
                echo RACINE_SITE."assets/img/avatar_f.png";
            } else if(isset($_SESSION['user']) && $_SESSION['user']['civility'] == "h") {
                echo RACINE_SITE."assets/img/avatar_h.png";
            }
            ?>"
             alt="Image avatar de l'utilisateur">


            <div class="details">
                <div class="center ">
                    <form action="" method="get">
                        <table class="table">
                            <tr>
                                <th scope="row" class="fw-bold">Nom</th>
                                <td><input class="form-control" type="text" name="" id="" placeholder="<?= $_SESSION['user']['firstName'] ?>"></td>

                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Prenom</th>
                                <td><input class="form-control" type="text" name="" id="" placeholder="<?= $user['lastName'] ?>"></td>

                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Pseudo</th>
                                <td colspan="2"><input class="form-control" type="text" name="" id="" placeholder="<?= $user['pseudo'] ?>"></td>

                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">email</th>
                                <td colspan="2"><input class="form-control" type="text" name="" id="" placeholder="<?= $user['email'] ?>"></td>

                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Tel</th>
                                <td colspan="2"><input class="form-control" type="text" name="" id="" placeholder="<?= $user['phone'] ?>"></td>

                            </tr>
                            <tr>
                                <th scope="row" class="fw-bold">Adresse</th>
                                <td colspan="2"><input class="form-control" type="text" name="" id="" placeholder="<?= $user['addressComplete'] ." ". $user['zip'] ." ". $user['city'] ." ". $user['country']?>"></td>

                            </tr>

                        </table>
                    </form>
                    
                    <a href="" class="btn mt-5">Modifier vos informations</a>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require_once("inc/footer.inc.php");
?>
