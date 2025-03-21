// Ajouter cette fonction en haut du fichier
function showAlert(message, type = 'success') {
    const alertElement = document.getElementById('alertMessage');
    const alertText = document.getElementById('alertText');
    const progressBar = document.getElementById('alertProgress');

    // Définir le message
    alertText.textContent = message;

    // Définir le type d'alerte et la barre de progression
    alertElement.className = `alert alert-${type} alert-dismissible fade show`;
    progressBar.className = `progress-bar bg-${type}`;

    // Scroll vers l'alerte avec animation
    // alertElement.scrollIntoView({
    //     behavior: 'smooth',
    //     block: 'start',
    //     inline: 'nearest'
    // });

    const duration = 10000; // Durée de l'alerte en millisecondes
    const startTime = performance.now(); // Temps de début de l'animation. performance est une API JavaScript qui fournit des mesures de performance et de temps d'exécution
    // La méthode performance.now() renvoie un timestamp en millisecondes avec une précision de microsecondes, utile pour mesurer le temps écoulé entre deux événements.


    function updateProgress(currentTime) {
        const elapsedTime = currentTime - startTime; // La constante elapsedTime est une mesure du "temps écoulé" depuis le début de l'animation.
        /* 
        startTime : C'est le moment initial capturé avec performance.now() quand l'alerte démarre
        currentTime : C'est le timestamp actuel fourni automatiquement par requestAnimationFrame
        elapsedTime : C'est la différence entre ces deux valeurs, donnant le temps écoulé en millisecondes
        */
        const progress = 100 - (elapsedTime / duration * 100); // Calculer le pourcentage restant
        // La formule (elapsedTime / duration * 100) calcule le pourcentage du temps écoulé par rapport à la durée totale de l'alerte.
        // En d'autres termes, elle détermine combien de temps s'est écoulé par rapport à la durée totale de l'alerte et le convertit en pourcentage.
        // La constante progress est le pourcentage restant avant que l'alerte ne disparaisse.
        // La barre de progression est remplie à l'envers, donc on soustrait le pourcentage écoulé de 100.
        if (progress > 0) {
            progressBar.style.width = `${progress}%`;
            // La méthode requestAnimationFrame() permet de créer des animations fluides en synchronisant le rendu avec le taux de rafraîchissement de l'écran.
            requestAnimationFrame(updateProgress);
        } else {
            alertElement.classList.remove('show');
            progressBar.style.width = '100%'; // Réinitialiser la barre de progression
        }
    }

    // Démarrer l'animation de la barre de progression
    requestAnimationFrame(updateProgress);



    // // Afficher l'alerte

    // // Auto-masquer après 3 secondes
    // setTimeout(() => {
    //     alertElement.classList.remove('show');
    // }, 10000);
}

function createButtonModify(id) {
    const inputNom = document.querySelector(`#nom${id}`);
    const textAreaDescription = document.querySelector(`#descriptif${id}`);
    const iconActions = document.querySelector(`tr[data-id="${id}"] .bi-pen-fill`); //

    // Activer les champs pour modification
    inputNom.removeAttribute("disabled");
    textAreaDescription.removeAttribute("disabled");

    // Remplacer l'icône par un bouton de validation
    iconActions.classList.replace("bi-pen-fill", "bi-check-lg");
    iconActions.setAttribute("onclick", `saveCategory(${id})`);
}

function saveCategory(id) {
    // console.log(`saveCategory appelée avec l'id : ${id}`);

    const inputNom = document.querySelector(`#nom${id}`);
    const textareaDescription = document.querySelector(`#descriptif${id}`);
    const iconActions = document.querySelector(`tr[data-id="${id}"] .bi-check-lg`);

    if (!inputNom || !textareaDescription || !iconActions) {
        // console.log("un ou plusieurs éléments HTML sont introuvables.");
        return;
    }

    //Récupérer les valeurs modifiées
    const nom = inputNom.value.trim();
    const descriptif = textareaDescription.value.trim();

    if (nom === "" || descriptif === "") {
        showAlert("Tous les champs doivent être remplis.", "info");
        return;
    }

    // Création de la fonction capitalize
    // qui met la première lettre en majuscule et le reste en minuscule
    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }


    // Création de l'objet FormData
    const formData = new FormData();
    formData.append("id", id);
    // création d'un champ nom ayant une majuscule
    formData.append("nom", capitalize(nom));
    formData.append("descriptif", descriptif);
    formData.append("action", "updateCategory");
    // console.log("FormData créé avec succès");

    // Envoie des données via la méthode Fetch
    fetch("categories.php", {
        method: "POST",
        body: formData // utilisation de formdata au lieu de json
    })
        .then(response => {
            // console.log("Réponse reçue du serveur:", response);
            if (!response.ok) {
                throw new Error("Erreur réseau");
            }
            return response.text();
        })
        .then(text => {
            // console.log("Text reçu du serveur:", text);
            try {
                const data = JSON.parse(text);
                // console.log("Données JSON reçues:", data);
                if (data.success) {
                    showAlert(data.message, "success");

                    // Désactiver les champs après modification
                    inputNom.setAttribute("disabled", "disabled");
                    textareaDescription.setAttribute("disabled", "disabled");

                    // Remettre l'icône de modification
                    iconActions.classList.replace("bi-check-lg", "bi-pen-fill");
                    iconActions.setAttribute("onclick", `createButtonModify(${id})`);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                // console.error("Erreur lors de la conversion Parsing JSON:", error);
                console.log("Réponse NON-JSON reçue:", text);
            }
        })
        .catch(error => {
            console.error("Erreur lors de la requête : ", error);
            alert("Une erreur s'est produite lors de la mise à jour de la catégorie.");
        });



    //Envoyer les données via AJAX
    // fetch("categories.php", {
    //     method: "POST",
    //     headers: {
    //         "Content-Type": "application/json",
    //     },
    //     body: JSON.stringify({
    //         action: "updateCategory",
    //         id: id,
    //         nom: nom,
    //         descriptif: descriptif
    //     }),
    // })

    //     // .then((response) => response.json())
    //     .then((response) => {
    //         console.log("Réponse reçue du serveur:", response);
    //         // Vérifier si la réponse est au format JSON
    //         if (!response.ok) {
    //             throw new Error("Erreur réseau");
    //         }
    //         return response.json();
    //     })
    //     .then((data) => {
    //         if (data.success) {
    //             alert(data.message);

    //             //Désactiver les champs après modification
    //             inputNom.setAttribute("disabled", "disabled");
    //             textareaDescription.setAttribute("disabled", "disabled");

    //             //Remettre l'icône de modification
    //             iconActions.classList.replace("bi-check-lg", "bi-pen-fill");
    //             iconActions.setAttribute("onclick", `createButtonModify(${id})`);
    //         } else {
    //             alert(data.message);
    //         }
    //     })
    //     .catch((error) => console.error("Erreur lors de la requête : ", error));
}
