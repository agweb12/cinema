function createButtonModify(id) {
    // let formChangeCategory = document.querySelector(`#form${id}`);
    let textAreaDescription = document.querySelector(`#textarea${id}`);
    let inputNom = document.querySelector(`#input${id}`);
    // console.log(inputNom);
    let iconActions = document.querySelector(`#iconActions${id}`);
    let iconModify = document.querySelector(`.showInput${id}`);
    let nameCategory = document.querySelector(`#nom${id}`);
    let descriptionCategory = document.querySelector(`#description${id}`);
    console.log(nameCategory);
    console.log(descriptionCategory);

    let btnModifyNom = document.createElement('input');
    btnModifyNom.setAttribute("type", "submit");
    btnModifyNom.setAttribute("value", `Modifier la catégorie ${id}`);
    btnModifyNom.setAttribute("id", `modify${id}`);
    btnModifyNom.setAttribute("class", "btn btn-warning");
    btnModifyNom.setAttribute("name", `form${id}`);

    let iconCancel = document.createElement('i');
    iconCancel.setAttribute("class", "bi bi-x-lg");
    iconCancel.setAttribute("id", `iconCancel${id}`);
    iconCancel.setAttribute("onclick", `deleteButtonModify(${id})`);


    // afficher le bouton de modification Nom dans le formulaire input nom
    // afficher le bouton de modification Description dans le formulaire textarea description
    // console.log(btnModifyDescription);
    // console.log(textAreaDescription);
    // textAreaDescription.append(btnModifyDescription);
    inputNom.append(btnModifyNom);
    iconActions.append(iconCancel);
    nameCategory.removeAttribute("disabled");
    descriptionCategory.removeAttribute("disabled");

    if (iconActions.contains(iconCancel)) {
        iconModify.remove();
    } else {
        iconActions.append(iconModify);
        iconModify.setAttribute("onclick", `createButtonModify(${id})`);
        iconModify.setAttribute("class", `bi bi-pen-fill showInput${id}`);
        iconModify.setAttribute("id", `iconModify${id}`);
    }
    // console.log(inputNom);

}

function deleteButtonModify(id) {
    let iconActions = document.querySelector(`#iconActions${id}`);
    let iconModify = document.querySelector(`#iconModify${id}`);
    let iconCancel = document.querySelector(`#iconCancel${id}`);
    let btnModifyNom = document.querySelector(`#modify${id}`);
    let inputNom = document.querySelector(`#input${id}`);
    let textAreaDescription = document.querySelector(`#textarea${id}`);
    let nameCategory = document.querySelector(`#nom${id}`);
    let descriptionCategory = document.querySelector(`#description${id}`);

    inputNom.removeChild(btnModifyNom);
    nameCategory.setAttribute("disabled", "disabled");
    descriptionCategory.setAttribute("disabled", "disabled");

    if (iconActions.contains(iconCancel)) {
        console.log("iconCancel");
        iconCancel.remove();
        iconActions.append(iconModify);
        console.log(iconModify);
        iconModify.setAttribute("onclick", `createButtonModify(${id})`);
        iconModify.setAttribute("class", `bi bi-pen-fill showInput${id}`);
        iconModify.setAttribute("id", `iconModify${id}`);
    }
}
