<?php
require_once __DIR__.'/../modele/class.pdogsb.php';
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

//********************************************Contrôleur connexion*****************//
class ConnexionControleur{

    public function __construct(){
        ob_start();             // démarre le flux de sortie
        require_once __DIR__.'/../vues/v_entete.php';
    }
    public function accueil(){
        require_once __DIR__.'/../vues/v_connexion.php';
        require_once __DIR__.'/../vues/v_pied.php';
        $view = ob_get_clean(); // récupère le contenu du flux et le vide
        return $view;     // retourne le flux
    }
    public function verifierUser(Request $request, Application $app){
        session_start();
        $login = $request->get('login');
	$mdp = $request->get('mdp');
        $pdo = PdoGsb::getPdoGsb();
	$visiteur = $pdo->getInfosVisiteur($login,$mdp);
	if(!is_array( $visiteur)){
            $app['couteauSuisse']->ajouterErreur("Login ou mot de passe incorrect");
            require_once __DIR__.'/../vues/v_erreurs.php';
            require_once __DIR__.'/../vues/v_connexion.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
        }
  else if ($visiteur['isAdmin']==1){
            $id = $visiteur['id'];
            $nom =  $visiteur['nom'];
            $prenom = $visiteur['prenom'];
            $app['couteauSuisse']->connecter($id,$nom,$prenom);
            require_once __DIR__.'/../vues/v_sommaireGestionnaire.php';
            require_once __DIR__.'/../vues/v_gestionnaire.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
  }
	else{
            $id = $visiteur['id'];
            $nom =  $visiteur['nom'];
            $prenom = $visiteur['prenom'];
            $app['couteauSuisse']->connecter($id,$nom,$prenom);
            require_once __DIR__.'/../vues/v_sommaire.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
        }
        return $view;
    }
    public function deconnecter(Application $app){
        $app['couteauSuisse']->deconnecter();
       return $app->redirect('../public');
    }
}
//**************************************Contrôleur EtatFrais**********************

class EtatFraisControleur {
     private $idVisiteur;
     private $pdo;
     public function init(){
        $this->idVisiteur = $_SESSION['idVisiteur'];
        $this->pdo = PdoGsb::getPdoGsb();
        ob_start();             // démarre le flux de sortie
        require_once __DIR__.'/../vues/v_entete.php';
        require_once __DIR__.'/../vues/v_sommaire.php';

    }
    public function selectionnerMois(Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init();
            $lesMois = $this->pdo->getLesMoisDisponibles($this->idVisiteur);
            // Afin de sélectionner par défaut le dernier mois dans la zone de liste
            // on demande toutes les clés, et on prend la première,
            // les mois étant triés décroissants
            $lesCles = array_keys( $lesMois );
            $moisASelectionner = $lesCles[0];
            require_once __DIR__.'/../vues/v_listeMois.php';
             require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
        }
        else{
            return Response::HTTP_NOT_FOUND;
        }
    }
    public function voirFrais(Request $request,Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init();
            $leMois = $request->get('lstMois');
            $this->pdo = PdoGsb::getPdoGsb();
            $lesMois = $this->pdo->getLesMoisDisponibles($this->idVisiteur);
            $moisASelectionner = $leMois;
            $lesFraisForfait= $this->pdo->getLesFraisForfait($this->idVisiteur,$leMois);
            $lesInfosFicheFrais = $this->pdo->getLesInfosFicheFrais($this->idVisiteur,$leMois);
            $numeroAnnee = substr( $leMois,0,4);
            $numeroMois = substr( $leMois,4,2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif =  $lesInfosFicheFrais['dateModif'];
            $dateModif =  $app['couteauSuisse']->dateAnglaisVersFrancais($dateModif);
            require_once __DIR__.'/../vues/v_listeMois.php';
            require_once __DIR__.'/../vues/v_etatFrais.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
         }
        else {
            $response = new Response();
            $response->setContent('Connexion nécessaire');
            return $response;
        }
    }
}
//************************************Controleur GererFicheFrais********************

Class GestionFicheFraisControleur{
    private $pdo;
    private $mois;
    private $idVisiteur;
    private $numAnnee;
    private $numMois;

    public function init(Application $app){
            $this->idVisiteur = $_SESSION['idVisiteur'];
            ob_start();
            require_once __DIR__.'/../vues/v_entete.php';
            require_once __DIR__.'/../vues/v_sommaire.php';
            $this->mois = $app['couteauSuisse']->getMois(date("d/m/Y"));
            $this->numAnnee =substr($this->mois,0,4);
            $this->numMois =substr( $this->mois,4,2);
            $this->pdo = PdoGsb::getPdoGsb();

     }

    public function saisirFrais(Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init($app);
            if($this->pdo->estPremierFraisMois($this->idVisiteur,$this->mois)){
                $this->pdo->creeNouvellesLignesFrais($this->idVisiteur,$this->mois);
            }
            $lesFraisForfait = $this->pdo->getLesFraisForfait($this->idVisiteur,$this->mois);
            $numMois = $this->numMois;
            $numAnnee = $this->numAnnee;
            require_once __DIR__.'/../vues/v_listeFraisForfait.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
        }
         else {
            $response = new Response();
            $response->setContent('Connexion nécessaire');
            return $response;
        }
    }
    public function validerFrais(Request $request,Application $app){
        session_start();
        if($app['couteauSuisse']->estConnecte()){
            $this->init($app);
            $lesFrais = $request->get('lesFrais');
            if($app['couteauSuisse']->lesQteFraisValides($lesFrais)){
                $this->pdo->majFraisForfait($this->idVisiteur,$this->mois,$lesFrais);
            }
            else{
                $app['couteauSuisse']->ajouterErreur("Les valeurs des frais doivent être numériques");
                require_once __DIR__.'/../vues/v_erreurs.php';
                require_once __DIR__.'/../vues/v_pied.php';
            }
            $lesFraisForfait= $this->pdo->getLesFraisForfait($this->idVisiteur,$this->mois);
            $numMois = $this->numMois;
            $numAnnee = $this->numAnnee;
            require_once __DIR__.'/../vues/v_listeFraisForfait.php';
            require_once __DIR__.'/../vues/v_pied.php';
            $view = ob_get_clean();
            return $view;
        }
         else {
            $response = new Response();
            $response->setContent('Connexion nécessaire');
            return $response;
        }

    }
}

//************************************Controleur GestionnaireVisiteur********************

Class GestionnaireVisiteurControleur{
    private $pdo;
    private $idVisiteur;

    public function init(Application $app){
            $this->idVisiteur = $_SESSION['idVisiteur'];
            ob_start();
            require_once __DIR__.'/../vues/v_entete.php';
            require_once __DIR__.'/../vues/v_sommaireGestionnaire.php';
            $this->pdo = PdoGsb::getPdoGsb();
     }

     public function afficherVisiteurs(Application $app)
     {
       session_start();

       if($app['couteauSuisse']->estConnecte()){
           $this->init($app);
           require_once __DIR__.'/../vues/v_gestionnaire.php';
           $lettres = $_POST['lettres'];
           $letDeb = substr($lettres,0,1);
           $letFin = substr($lettres,-1);
           $info = $this->pdo->listeVisiteur($letDeb,$letFin);
           require_once __DIR__.'/../vues/v_afficherVisiteurs.php';
           require_once __DIR__.'/../vues/v_pied.php';
           $view = ob_get_clean();
           return $view;
       }
        else {
           $response = new Response();
           $response->setContent('Connexion en tant qu\'administrateur nécessaire');
           return $response;
       }
     }
}

?>
