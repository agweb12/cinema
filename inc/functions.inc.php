<?php
session_start(); // session_start() est une fonction qui permet de démarrer une session PHP. Une session est une manière de stocker des informations (variables) pour être utilisées sur plusieurs pages. Les informations stockées dans une session sont accessibles à chaque page du site pendant la durée de la session.
// session_start() doit être appelée avant tout code HTML ou espace blanc dans votre script PHP. Si vous avez des erreurs de session, assurez-vous que vous n'avez pas d'espaces ou de lignes vides avant session_start() ou après session_start().


#### Condition pour se déconnecter
if (isset($_GET['action']) && $_GET['action'] === "deconnexion") {
    // Soit on supprime la clé "user" de la session
    // unset($_SESSION['user']);
    // Soit on détruit la session $_SESSION
    // session_destroy();
    // La fonction session_destroy détruit toutes les données de la session déjà établie. Cette focntion détruit la sessio  sur le serveur.

    //ca dépend de l'objectif du site. Dnas notre cas, c'est un site e-commerce qui va gérer des paniers utilisateur, donc on supprime la clé "user" de la session
    unset($_SESSION['user']); // On supprime l'indice 'user' de la session pour s edéconnecter, cette fonction détruit les élément du tableau $_SESSION['user']
    header("location:" . RACINE_SITE . "index.php");
}

#### Constante pour définir le chemin du site
define("RACINE_SITE", "http://localhost/cinema/");

#### Création d'une fonction alerte
function alert(string $message, string $type = "danger"): string
{
    return "<div class='alert alert-$type alert-dismissible fade show text-center w-50 m-auto mb-5' role='alert'>
    $message
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
}

#### Fonction pour debuger
function debug($var): void
{
    echo "<pre class='border border-dark bg-light text-danger fw-bold w-50 p-5 mt-5'>";
    var_dump($var);
    echo "</pre>";
}


#### Fonction pour la connexion à la Base de Données

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "cinema");

function connexionBDD(): object
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";

    try {
        // C'est quoi PDO ? Pourquoi on l'utilise ?
        // PDO est une extension PHP qui définit une interface pour accéder à une base de données depuis PHP
        $pdo = new PDO($dsn, DB_USER, DB_PASS); // il crée une instance de la classe PDO (d'un objet) qui est une classe prédéfinie en PHP, elle implémente des interfaces pour accéder à une base de données tels que MySQL, PostgreSQL, etc.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // echo "Je suis connecté à la BDD";
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
    return $pdo;
}


#### Fonction pour créer la table catégories
function createTableCategories(): void
{
    $pdo = connexionBDD();
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        descriptif TEXT DEFAULT NULL)";
    $request = $pdo->exec($sql);
}
// createTableCategories();

#### Fonction pour créer la table films
function createTableFilms(): void
{
    $pdo = connexionBDD();
    // à quoi sert le UNSIGNED ?
    // UNSIGNED est un attribut qui permet de spécifier que la colonne ne peut pas contenir de valeurs négatives
    $sql = "CREATE TABLE IF NOT EXISTS films (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        categorie_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        director VARCHAR(100) NOT NULL,
        actors VARCHAR(100) NOT NULL,
        ageLimit VARCHAR(5) NULL,
        duration TIME NOT NULL,
        synopsis TEXT NOT NULL,
        date DATE NOT NULL,
        image VARCHAR(255) NOT NULL,
        price FLOAT NOT NULL,
        stock BIGINT NOT NULL,
        FOREIGN KEY (categorie_id) REFERENCES categories(id))";
    $pdo->exec($sql);
}
// createTableFilms();

#### Fonction pour créer la table utilisateurs
function createTableUsers(): void
{
    $pdo = connexionBDD();

    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(50) NOT NULL,
        lastName VARCHAR(50) NOT NULL,
        pseudo VARCHAR(50) NOT NULL,
        mdp VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        civility ENUM('f', 'h') NOT NULL,
        birthday DATE NOT NULL,
        addressComplete VARCHAR(50) NOT NULL,
        zip VARCHAR(10) NOT NULL,
        city VARCHAR(50) NOT NULL,
        country VARCHAR(50) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user'
        )";

    $pdo->exec($sql);
}
//createTableUsers();

#### Fonction pour créer la table commandes
function createTableOrders(): void
{
    $pdo = connexionBDD();
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        price float NOT NULL,
        created_at DATETIME NOT NULL,
        is_paid ENUM('en attente', 'annulé', 'payé') NOT NULL DEFAULT 'en attente')";
        // FOREIGN KEY (user_id) REFERENCES users(id))";
    $pdo->exec($sql);
}
// createTableOrders();

function createTableDetailOrders(): void
{
    $pdo = connexionBDD();
    $sql = "CREATE TABLE IF NOT EXISTS order_details (
        order_id INT NOT NULL,
        film_id INT NOT NULL,
        price_film FLOAT NOT NULL,
        quantity INT NOT NULL)";
        //FOREIGN KEY (order_id) REFERENCES orders(id),
        // FOREIGN KEY (film_id) REFERENCES films(id))";
    $pdo->exec($sql);
}
// createTableDetailOrders();


function foreignKey(string $tableFK, string $keyFK, string $tablePK, string $keyPK): void
{
    $pdo = connexionBDD();
    $sql = "ALTER TABLE $tableFK ADD FOREIGN KEY ($keyFK) REFERENCES $tablePK($keyPK)";
    $pdo->exec($sql);
}
// foreignKey("order_details", "order_id", "orders", "id");
// foreignKey("order_details", "film_id", "films", "id");
/**
{
    $pdo = connexionBDD();
    $sql = "ALTER TABLE films ADD FOREIGN KEY (categorie_id) REFERENCES categories(id)";
    $pdo->exec($sql);
}
 */


/*
        ╔═════════════════════════════════════════════╗
        ║                                             ║
        ║                UTILISATEURS                 ║
        ║                                             ║
        ╚═════════════════════════════════════════════╝ 
*/

#### Fonction pour ajouter un utilisateur
function addUser(string $nom, string $firstName, string $pseudo, string $email, string $phone, string $mdp, string $civility, string $birthday, string $address, string $zip, string $city, string $country): void
{
    $data = [
        //Key => Value
        'nom' => $nom,
        'prenom' => $firstName,
        'pseudo' => $pseudo,
        'email' => $email,
        'phone' => $phone,
        'mdp' => $mdp,
        'civility' => $civility,
        'birthday' => $birthday,
        'addressComplete' => $address,
        'zip' => $zip,
        'city' => $city,
        'country' => $country
    ];

    #Echapper les données et les traiter contre les failles JS

    foreach ($data as $key => $value) {
        //htmlspecialchars() convertit les caractères spéciaux en entités HTML
        // Exemple : < devient &lt; et > devient &gt;
        // $data['lastName'] = htmlspecialchars($lastName);
        $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); //ENT_QUOTES est une constante en PHP qui convertie les guillemets doubles et les guillemets simples en entités HTML. Exemple : la guillement simple se convertit en &#039; et la guillemet double se convertit en &quot;

        /* 
            htmlspecialchars est une fonction qui convertit les caractères spéciaux en entités HTML, cela est utilisé afin d'empêcher l'exécution de code HTML ou JavaScript : les attaques XSS (Cross-Site Scripting) injecté par un utilisateur malveillant en échappant les caractères HTML /////////////potentiellement dangereux . Par défaut, htmlspecialchars échappe les caractères suivants :

            & (ampersand) devient &amp;
            < (inférieur) devient &lt;
            > (supérieur) devient &gt;
            " (guillemet double) devient &quot;

        */
    }

    $pdo = connexionBDD();
    $sql = "INSERT INTO users (lastName, firstName, pseudo, email, phone, mdp, civility, birthday, addressComplete, zip, city, country) VALUES (:n, :p, :pseudo, :email, :phone, :mdp, :civility, :birthday, :addressComplete, :zip, :city, :country)";
    /* Les requêtes préparées sont préconisées si vous exécutez plusieurs fois la même requête. Ainsi vous évitez au SGBD de répéter toutes les phases analyse/ interpretation / exécution de la requête (gain de performance). Les requêtes préparées sont aussi utilisées pour nettoyer les données et se prémunir des injections de type SQL.

        1- On prépare la requête
        2- On lie le marqueur à la requête
        3- On exécute la requête 

    */
    $request = $pdo->prepare($sql); //prepare() est une méthode qui permet de préparer la requête sans l'exécuter. Elle contient un marqueur :firstName qui est vide et attend une valeur.
    $request->execute(array(
        ':n' => $data['nom'],
        ':p' => $data['prenom'],
        ':pseudo' => $data['pseudo'],
        ':email' => $data['email'],
        ':phone' => $data['phone'],
        ':mdp' => $data['mdp'],
        ':civility' => $data['civility'],
        ':birthday' => $data['birthday'],
        ':addressComplete' => $data['addressComplete'],
        ':zip' => $data['zip'],
        ':city' => $data['city'],
        ':country' => $data['country']
    )); //execute() est une méthode qui permet d'exécuter la requête préparée. Elle prend en paramètre un tableau associatif qui contient les valeurs à injecter dans la requête.

    // $request->execute($data); // cela ne fonctionne pas car les clés du tableau $data doivent être identiques aux marqueurs de la requête préparée
}

#### Fonction pour vérifier si l'email existe déjà
function checkEmailUser(string $email): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT email FROM users WHERE email = :email";
    $request = $pdo->prepare($sql); // La flèche représente l'opérateur de résolution de portée qui permet d'accéder à une méthode ou une propriété d'un objet. Dans notre cas, on accède à la méthode prepare() de l'objet $pdo.
    $request->bindValue(':email', $email, PDO::PARAM_STR);
    $request->execute();
    $result = $request->fetch();
    return $result;

    // $request = $pdo->query($sql); // query() est une méthode qui permet d'exécuter une requête SQL. Elle prend en paramètre la requête SQL à exécuter.
    // $result = $request->fetch(); // fetch() est une méthode qui permet de récupérer le résultat de la requête sous forme de tableau associatif (clé => valeur)
    // return $result;
}

#### Fonction pour vérifier si le pseudo existe déjà
function checkPseudoUser(string $pseudo): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT pseudo FROM users WHERE pseudo = :pseudo";
    $request = $pdo->prepare($sql);
    $request->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

#### Fonction pour vérifier si l'email et le pseudo existent déjà
function checkEmailPseudoUser($email, $pseudo): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM users WHERE email = :email AND pseudo = :pseudo";
    $request = $pdo->prepare($sql);
    $request->bindValue(':email', $email, PDO::PARAM_STR); // bindValue permet de lier une valeur à un marqueur de requête préparée (marqueur :email) et de spécifier le type de données à lier (PDO::PARAM_STR) et de sécuriser les données.
    $request->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

#### Afficher tous les utilisateurs dans le dashboard
function allUsers(): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM users";
    $request = $pdo->query($sql); // La méthode query permet d'exécuter une requête SQL. Elle prend en paramètre la requête SQL à exécuter.
    $result = $request->fetchAll(); // La méthode fetchAll récupère toutes les lignes à la fois et les retourne sous forme de tableau associatif.
    return $result;
}

#### Fonction pour mettre à jour le rôle de l'utilisateur

function updateUserRole(int $id, string $role): void
{
    $pdo = connexionBDD();
    $sql = "UPDATE users SET role = :role WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->bindValue(':role', $role, PDO::PARAM_STR);
    $request->bindValue(':id', $id, PDO::PARAM_INT);
    $request->execute();
}

function updateUserDetail(int $id): void{
    $pdo = connexionBDD();
    $sql = "UPDATE users SET lastName = :lastName, firstName = :firstName, pseudo = :pseudo, email = :email, phone = :phone, civility = :civility, birthday = :birthday, addressComplete = :addressComplete, zip = :zip, city = :city, country = :country WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(
        // key => value
        // ":lastName" => $_POST['lastName']
        ":lastName" => $_POST['lastName'],
        ":firstName" => $_POST['firstName'],
        ":pseudo" => $_POST['pseudo'],
        ":email" => $_POST['email'],
        ":phone" => $_POST['phone'],
        ":civility" => $_POST['civility'],
        ":birthday" => $_POST['birthday'],
        ":addressComplete" => $_POST['addressComplete'],
        ":zip" => $_POST['zip'],
        ":city" => $_POST['city'],
        ":country" => $_POST['country'],
        ":id" => $id
    ));
    // $result = $request->fetch();
    // return $result;
}

function showUser(int $id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM users WHERE id = :id";
    $request = $pdo->prepare($sql);
    // $request->bindValue(':id', $id, PDO::PARAM_INT);
    // $request->execute();
    $request->execute(array(':id' => $id));
    $result = $request->fetch();
    return $result;
}

function deleteUser(int $id): void
{
    $pdo = connexionBDD();
    $sql = "DELETE FROM users WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(
        ":id" => $id
    ));
    // $result = $request->fetch();
    // return $result;
}
/*
        ╔═════════════════════════════════════════════╗
        ║                                             ║
        ║                CATEGORIES                   ║
        ║                                             ║
        ╚═════════════════════════════════════════════╝ 
*/

function showCategorie(string $nom): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM categories WHERE nom = :nom";
    $request = $pdo->prepare($sql);
    $request->execute(array(':nom' => $nom));
    $result = $request->fetch();
    return $result;
}

#### vérifier si la catégorie existe
function categorieExist(string $nom): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT nom FROM categories WHERE nom = :nom";
    $request = $pdo->prepare($sql);
    // $request->bindValue(':nom', $nom);
    $request->execute(array(
        ':nom' => $nom
    ));
    $result = $request->fetch();
    return $result;
}

#### Créer une catégorie
function createCategorie(string $nom, string $descriptif): void{
    $data = [
        //Key => Value
        'nom' => $nom,
        'descriptif' => $descriptif
    ];

    foreach ($data as $key => $value) {
        $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); 
    }

    $pdo = connexionBDD();
    $sql = "INSERT INTO categories (nom, descriptif) VALUES (:nom, :descriptif)";
    $request = $pdo->prepare($sql);
    $request->execute(array(
        ":nom" => $data['nom'],
        ":descriptif" => $data['descriptif']
    ));
    // return $pdo->lastInsertId(); // lastInsertId() est une méthode qui retourne l'identifiant de la dernière ligne insérée dans la table.
}

#### Afficher toutes les catégories
function allCategories(): mixed // $categories
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM categories ORDER BY nom ASC";
    $request = $pdo->query($sql);
    $result = $request->fetchAll();
    return $result;
}

#### Supprimer une catégorie
function deleteCategorie(string $nom): void
{
    $pdo = connexionBDD();
    $sql = "DELETE FROM categories WHERE nom = :nom";
    $request = $pdo->prepare($sql);
    $request->execute(array(
        ":nom" => $nom
    ));
}

#### Mettre à jour une catégorie
function updateCategorie(int $id, string $nom, string $descriptif): bool
{
    $pdo = connexionBDD();
    $sql = "UPDATE categories SET nom = :nom, descriptif = :descriptif WHERE id = :id";
    $request = $pdo->prepare($sql);
    $result = $request->execute(array(
        ":nom" => $nom,
        ":descriptif" => $descriptif,
        ":id" => $id
    ));

    if (!$result) {
        error_log("Erreur lors de la mise à jour : " . json_encode($request->errorInfo()));
    }

    return $result;
}

/*
        ╔═════════════════════════════════════════════╗
        ║                                             ║
        ║                FILMS                        ║
        ║                                             ║
        ╚═════════════════════════════════════════════╝ 
*/

#### Créer une catégorie
function addFilm(int $idCategorie, string $title, string $director, string $actors, string $ageLimit, string $duration, string $synopsis, string $date, string $image, float $price, int $stock): void{
    $data = [
        //Key => Value
        'idCategorie' => $idCategorie,
        't' => $title,
        'dir' => $director,
        'act' => $actors,
        'ageLimit' => $ageLimit,
        'd' => $duration,
        's' => $synopsis,
        'date' => $date,
        'image' => $image,
        'price' => $price,
        'stock' => $stock
    ];

    foreach ($data as $key => $value) {
        $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); 
    }

    $pdo = connexionBDD();
    $sql = "INSERT INTO films (categorie_id ,title, director, actors, ageLimit, duration, synopsis, date, image, price, stock) VALUES (:idCategorie, :t, :dir, :act, :ageLimit, :d, :s, :date, :image, :price, :stock)";
    $request = $pdo->prepare($sql);
    $request->bindValue(':idCategorie', $data['idCategorie'], PDO::PARAM_INT);
    $request->bindValue(':t', $data['t'], PDO::PARAM_STR);
    $request->bindValue(':dir', $data['dir'], PDO::PARAM_STR);
    $request->bindValue(':act', $data['act'], PDO::PARAM_STR);
    $request->bindValue(':ageLimit', $data['ageLimit'], PDO::PARAM_STR);
    $request->bindValue(':d', $data['d'], PDO::PARAM_STR);
    $request->bindValue(':s', $data['s'], PDO::PARAM_STR);
    $request->bindValue(':date', $data['date'], PDO::PARAM_STR);
    $request->bindValue(':image', $data['image'], PDO::PARAM_STR);
    $request->bindValue(':price', $data['price']);
    $request->bindValue(':stock', $data['stock'], PDO::PARAM_INT);
    $request->execute();
}

#### Afficher tous les films
function allFilms(): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT
    films.id AS id, ## changer en films.id_film et Enlever le AS id
    categories.nom AS genre, ## changer par categories.name AS genre
    title,
    director,
    actors,
    ageLimit,
    duration,
    synopsis,
    date,
    image,
    price,
    stock
    FROM films
    INNER JOIN categories ON films.categorie_id = categories.id ## changer par categories.categorie_id
    ORDER BY title ASC";
    $request = $pdo->query($sql);
    $result = $request->fetchAll();
    return $result;
}

#### Afficher 6 films
function sixFilms(): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT
    films.id AS id, 
    categories.nom AS genre, 
    title,
    director,
    actors,
    ageLimit,
    duration,
    synopsis,
    date,
    image,
    price,
    stock
    FROM films
    INNER JOIN categories ON films.categorie_id = categories.id 
    ORDER BY title ASC
    LIMIT 6"; // LIMIT 6 permet de limiter le nombre de résultats à 6
    $request = $pdo->query($sql);
    $result = $request->fetchAll();
    return $result;
}

#### Vérifier si le film existe déjà
function filmExist(string $title): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT title FROM films WHERE title = :title";
    $request = $pdo->prepare($sql);
    $request->bindValue(':title', $title, PDO::PARAM_STR);
    $request->execute();
    $result = $request->fetch();
    return $result;
}

#### Afficher un film
function showFilm(int $id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM films WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':id' => $id));
    $result = $request->fetch();
    return $result;
}
#### Supprimer un film
function deleteFilm(int $id): void
{
    $pdo = connexionBDD();
    $sql = "DELETE FROM films WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(
        ":id" => $id
    ));
}

#afficher un film avec sa catégorie
function showFilmWithCategory(int $id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT films.*, categories.nom AS genre
    FROM films
    INNER JOIN categories ON films.categorie_id = categories.id
    WHERE films.id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':id' => $id));
    $result = $request->fetch();
    return $result;
}

#### Mettre à jour un film
function updateFilm(int $id, int $idCategorie, string $title, string $director, string $actors, string $ageLimit, string $duration, string $synopsis, string $date, string $image, float $price, int $stock): void
{
    $pdo = connexionBDD();
    $sql = "UPDATE films SET categorie_id = :idCategorie, title = :title, director = :director, actors = :actors, ageLimit = :ageLimit, duration = :duration, synopsis = :synopsis, date = :date, image = :image, price = :price, stock = :stock WHERE id = :id"; ## changer en id_film et categorie_id par category_id
    $request = $pdo->prepare($sql);
    // $request->execute(array(
    //     ":idCategorie" => $idCategorie,
    //     ":title" => $title,
    //     ":director" => $director,
    //     ":actors" => $actors,
    //     ":ageLimit" => $ageLimit,
    //     ":duration" => $duration,
    //     ":synopsis" => $synopsis,
    //     ":date" => $date,
    //     ":image" => $image,
    //     ":price" => $price,
    //     ":stock" => $stock,
    //     ":id" => $id
    // ));
    $request->bindValue(':idCategorie', $idCategorie, PDO::PARAM_INT);
    $request->bindValue(':title', $title, PDO::PARAM_STR);
    $request->bindValue(':director', $director, PDO::PARAM_STR);
    $request->bindValue(':actors', $actors, PDO::PARAM_STR);
    $request->bindValue(':ageLimit', $ageLimit, PDO::PARAM_STR);
    $request->bindValue(':duration', $duration, PDO::PARAM_STR);
    $request->bindValue(':synopsis', $synopsis, PDO::PARAM_STR);
    $request->bindValue(':date', $date, PDO::PARAM_STR);
    $request->bindValue(':image', $image, PDO::PARAM_STR);
    $request->bindValue(':price', $price);
    $request->bindValue(':stock', $stock, PDO::PARAM_INT);
    $request->bindValue(':id', $id, PDO::PARAM_INT);
    $request->execute();
}

/*
        ╔═════════════════════════════════════════════╗
        ║                                             ║
        ║                COMMANDES                    ║
        ║                                             ║
        ╚═════════════════════════════════════════════╝ 
*/

// Fonction pour ajouter une commande
// La fonction addOrder prend en paramètres l'identifiant de l'utilisateur et le prix de la commande. Elle insère une nouvelle commande dans la table orders avec l'identifiant de l'utilisateur, le prix et la date de création de la commande (NOW()).
function addOrder(int $user_id, float $price): int
{
    $pdo = connexionBDD();
    $sql = "INSERT INTO orders (user_id, price, created_at, is_paid) VALUES (:user_id, :price, NOW(), 'en attente')";
    $request = $pdo->prepare($sql);
    $request->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $request->bindValue(':price', $price, PDO::PARAM_STR);
    $request->execute();

    // Récupérer et retourner l'ID de la commande créée
    $lastId = $pdo->lastInsertId();
    
    // Vérifier si l'ID est bien numérique
    if (!is_numeric($lastId) || $lastId == 0) {
        throw new Exception("Erreur lors de la récupération de l'ID de commande");
    }
    
    return (int)$lastId;
}

// Fonction pour ajouter les détails de la commande
// La fonction addOrderDetails prend en paramètres l'identifiant de la commande, l'identifiant du film, le prix du film et la quantité. Elle insère les détails de la commande dans la table order_details avec l'identifiant de la commande, l'identifiant du film, le prix du film et la quantité.
function addOrderDetails(int $order_id, int $film_id, float $price_film, int $quantity): void
{
    $pdo = connexionBDD();
    $sql = "INSERT INTO order_details (order_id, film_id, price_film, quantity) VALUES (:order_id, :film_id, :price_film, :quantity)";
    $request = $pdo->prepare($sql);
    $request->bindValue(':order_id', $order_id, PDO::PARAM_INT);
    $request->bindValue(':film_id', $film_id, PDO::PARAM_INT);
    $request->bindValue(':price_film', $price_film, PDO::PARAM_STR);
    $request->bindValue(':quantity', $quantity, PDO::PARAM_INT);
    $request->execute();
}

// Fonction pour afficher les commandes d'un utilisateur
// La fonction showUserOrders prend en paramètre l'identifiant de l'utilisateur et retourne toutes les commandes de cet utilisateur. Elle utilise une requête préparée pour éviter les injections SQL.
function showUserOrders(int $user_id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM orders WHERE user_id = :user_id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':user_id' => $user_id));
    $result = $request->fetchAll();
    return $result;
}

// Fonction pour afficher les détails d'une commande d'un utilisateur
// La fonction showUserOrderDetails prend en paramètre l'identifiant de la commande et retourne tous les détails de cette commande. Elle utilise une requête préparée pour éviter les injections SQL.
// Elle sélectionne toutes les colonnes de la table order_details où l'identifiant de la commande est égal à :order_id. La méthode fetchAll() retourne toutes les lignes sous forme de tableau associatif.
function showUserOrderDetails(int $order_id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM order_details WHERE order_id = :order_id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':order_id' => $order_id));
    $result = $request->fetchAll();
    return $result;
}


// Fonction pour annuler une commande d'un utilisateur
// La fonction cancelOrder prend en paramètre l'identifiant de la commande et met à jour le statut de la commande dans la table orders. Elle utilise une requête préparée pour éviter les injections SQL.
function cancelOrder(int $order_id): void
{
    $pdo = connexionBDD();
    $sql = "UPDATE orders SET is_paid = 'annulé' WHERE id = :order_id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':order_id' => $order_id));
}

// Fonction pour payer une commande d'un utilisateur
// La fonction payOrder prend en paramètre l'identifiant de la commande et met à jour le statut de la commande dans la table orders. Elle utilise une requête préparée pour éviter les injections SQL.
function payOrder(int $order_id): void
{
    $pdo = connexionBDD();
    $sql = "UPDATE orders SET is_paid = 'payé' WHERE id = :order_id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':order_id' => $order_id));
}

// Récupérer les informations de la commande depuis la base de données
// La fonction getOrderDetails prend en paramètre l'identifiant de la commande et retourne les détails de cette commande. Elle utilise une requête préparée pour éviter les injections SQL.
function getOrder(int $id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM orders WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':id' => $id));
    $result = $request->fetch();
    return $result;
}


function getOrderDetails(int $order_id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT order_details.*, films.title, films.image
    FROM order_details
    INNER JOIN films ON order_details.film_id = films.id
    WHERE order_details.order_id = :order_id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':order_id' => $order_id));
    $result = $request->fetchAll();
    return $result;
}

// ADMINISTRATION DES COMMANDES
function allOrders(): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM orders";
    $request = $pdo->query($sql);
    $result = $request->fetchAll();
    return $result;
}

function showOrder(int $id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM orders WHERE id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':id' => $id));
    $result = $request->fetch();
    return $result;
}

function showOrderDetails(int $id): mixed
{
    $pdo = connexionBDD();
    $sql = "SELECT * FROM order_details WHERE order_id = :id";
    $request = $pdo->prepare($sql);
    $request->execute(array(':id' => $id));
    $result = $request->fetchAll();
    return $result;
}