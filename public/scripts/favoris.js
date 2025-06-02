function afficherDossiers(button) {
    const ficheWrapper = button.closest('.fiche-wrapper');
    const dossierContainer = ficheWrapper.querySelector('.dossiers-container');
    
    dossierContainer.classList.toggle('visible');
}