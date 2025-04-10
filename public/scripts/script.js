let etat=false;

function menuBurger(){
    etat=!etat;
    if (etat==true){
        document.getElementById("mobile-navbar-container").style.display = "block";
    } else {
        document.getElementById("mobile-navbar-container").style.display = "none";
    }
}


jQuery(function(){
    $(function () {
        // Afficher ou masquer le bouton "scrollUp" en fonction de la position du défilement
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100 ) {
                $('#scrollUp').css('right','10px');
            } else {
                $('#scrollUp').removeAttr('style');
            }
        });

        // Ajouter l'effet de défilement en douceur lors du clic sur "scrollUp"
        $('#scrollUp').click(function () {
            $('html, body').animate({
                scrollTop: 0
            }, 1000); // 800 ms pour l'effet de défilement en douceur
            return false; // Empêcher le comportement par défaut du clic
        });
    });
});




jQuery(function() {
    $(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#scrollUp').addClass('visible'); // Ajout de la classe "visible" lorsque l'on défile plus de 100px
            } else {
                $('#scrollUp').removeClass('visible'); // Retirer la classe "visible" si l'on revient en haut
            }
        });
    });
});


