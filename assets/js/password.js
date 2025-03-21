// Apparaitre et Disparaitre le mot de passe
let inputPassword = document.querySelector("#mdp");
let inputPasswordConfirm = document.querySelector("#confirmMdp");
let show = document.querySelector("#show");
let show2 = document.querySelector("#show2");

function apparitionMotDePasse(input, showing) {
    show.addEventListener('click', () => {
        if (input.type === "password") {
            input.type = "text";
            showing.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
        } else {
            input.type = "password";
            showing.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
        }
    });
}

apparitionMotDePasse(inputPassword, show);
apparitionMotDePasse(inputPasswordConfirm, show2);