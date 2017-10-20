<?php
                    /* Définition des routes*/
$app->match('/', "ConnexionControleur::accueil");
$app->match('/verifierUser', "ConnexionControleur::verifierUser");
$app->match('/deconnecter', "ConnexionControleur::deconnecter");

$app->match('/selectionnerMois', "EtatFraisControleur::selectionnerMois");
$app->match('/voirFrais', "EtatFraisControleur::voirFrais");

$app->match('/saisirFrais', "GestionFicheFraisControleur::saisirFrais");
$app->match('/validerFrais', "GestionFicheFraisControleur::validerFrais");

$app->match('/afficherVisiteurs', "GestionnaireVisiteurControleur::afficherVisiteurs");

?>
