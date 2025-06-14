function afficherDossiers(button) {
    const ficheWrapper = button.closest('.sous-item-2');
    const dossierContainer = ficheWrapper.querySelector('.dossiers-container');
    
    dossierContainer.classList.toggle('visible');
}

let etatFiltres = false;
function afficherFiltres(event){
    if(event) event.preventDefault();  // empêche la soumission du formulaire
    etatFiltres = !etatFiltres;
    if (etatFiltres==true){
        document.getElementById("form-filtres").style.display = "flex";
    } else {
        document.getElementById("form-filtres").style.display = "none";
    }   
}