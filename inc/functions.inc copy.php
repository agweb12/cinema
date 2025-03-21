<?php

# Constante pour définir le chemin du site
define("RACINE_SITE", "http://localhost/cinema/");

# Fonction pour la connexion à la Base de Données

// On va utiliser l'extension PHP Data Objects (PDO) pour se connecter à la base de données, elle définit une excellente interface pour accéder à une base de données depuis PHP et d'exécuter des requêtes SQL .
// Pour se connecter à la BDD avec PDO il faut créer une instance de cet Objet (PDO) qui représente une connexion à la base,  pour cela il faut se servir du constructeur de la classe
// Ce constructeur demande certains paramètres:
// On déclare des constantes d'environnement qui vont contenir les information à la connexion à la BDD


define("DB_HOST", "localhost");

// // constante de l'utilisateur de la BDD du serveur en local => root
define("DB_USER", "root");

// // constante pour le mot de passe de serveur en local => pas de mot de passe
define("DB_PASS", "");

// // Constante pour le nom de la BDD
define("DB_NAME", "cinema");


function connexionBDD(): object
{
    //DSN (Data Source Name):

    //$dsn = mysql:host=localhost;dbname=entreprise;charset=utf8;
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";

    //Grâce à PDP on peut lever une exception (une erreur) si la connexion à la BDD ne se réalise pas(exp: suite à une faute au niveau du nom de la BDD) et par la suite si elle cette erreur est capté on lui demande d'afficher une erreur

    try {   // dans le try on vas instancier PDO, c'est créer un objet de la classe PDO (un élment de PDO)
        // Sans la variable dsn les constatntes d'environnement
        // $pdo = new PDO('mysql:host=localhost;dbname=entreprise;charset=utf8','root','');
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        //On définit le mode d'erreur de PDO sur Exception
        // Qu'est-ce que setAttribute et pourquoi on l'utilise ?
        /**
         * setAttribute permet de définir un attribut pour l'instance PDO c'est à dire pour la connexion à la BDD
         * PDO::ATTR_ERRMODE : rapport d'erreurs
         * PDO::ERRMODE_EXCEPTION : émet une exception
         * PDO::ATTR_DEFAULT_FETCH_MODE : mode de récupération par défaut
         * PDO::FETCH_ASSOC : retourne un tableau associatif
         */
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // POUR SAHAR:  cet atribut est à rajouter après le premier fetch en bas 
        //On définit le mode de "fetch" par défaut
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // je vérifie la connexion avec ma BDD avec un simple echo
        echo "Je suis connecté à la BDD";
    } catch (PDOException $e) {
        // PDOException est une classe qui représente une erreur émise par PDO et $e c'est l'objetde la clase en question qui vas stocker cette erreur

        die("Erreur : " . $e->getMessage()); // die d'arrêter le PHP et d'afficher une erreur en utilisant la méthode getmessage de l'objet $e
    }
    return $pdo;
}
     //le catch sera exécuter dès lors on aura un problème da le try

// À partir d'ici on est connecté à la BDD et la variable $pdo est l'objet qui représente la connexion à la BDD, cette variable va nous servir à effectuer les requêtes SQL et à interroger la base de données pour récupérer des informations.

// debug($pdo);
//debug(get_class_methods($pdo)); // permet d'afficher la liste des méthodes présentes dans l'objet $pdo.
