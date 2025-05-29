let etatBoutonDossier = false;

function afficherDossiers(){
    etatBoutonDossier = !etatBoutonDossier;
    if (etatBoutonDossier==true){
        document.getElementById("dossiers-container").style.display = "flex";
    } else {
        document.getElementById("dossiers-container").style.display = "none";
    }
}

let etatFiltres = false;
function afficherFiltres(){
    etatFiltres = !etatFiltres;
    if (etatFiltres==true){
        document.getElementById("form-filtres").style.display = "flex";
    } else {
        document.getElementById("form-filtres").style.display = "none";
    }   
}