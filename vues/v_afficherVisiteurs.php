<h3>Tout les visiteurs entre <?php echo $letDeb; ?> et <?php echo $letFin; ?>:
    </h3>
    <div class="encadre">
  	<table class="listeLegere">
        <tr>
			      <th> Nom </th>
            <th> Prenom </th>
		    </tr>

        <?php
        foreach ($info as $infoVisiteur)
		    {
          $id = $infoVisiteur['id'];
				  $nom = $infoVisiteur['nom'];
          $prenom = $infoVisiteur['prenom'];
		    ?>
        <tr>
          <td><?php echo $nom?> </td>
          <td><?php echo $prenom?> </td>
          <td><a href=""> supprimer </a></td>
        </tr>
		    <?php
        }
		    ?>
    </table>
  </div>
