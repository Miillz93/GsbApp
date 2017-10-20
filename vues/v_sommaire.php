    <!-- Division pour le sommaire -->
  <div id="menuGauche">
    <div id="infosUtil">

        <ul id="menuList">
    			<li >
  				  Visiteur :<br>
  				  <?php echo $_SESSION['prenom']."  ".$_SESSION['nom']  ?>
  			  </li>
          <li class="smenu">
            <a href="saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
          </li>
          <li class="smenu">
            <a href="selectionnerMois" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
          </li>
   	      <li class="smenu">
                <a href="deconnecter" title="Se déconnecter">Déconnexion</a>
          </li>
        </ul>
    </div>
  </div>
