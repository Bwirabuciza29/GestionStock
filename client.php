<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once('config.php');

// Enregistrement d'un client
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'enregistrer') {
    $nomsCli = $_POST['nomsCli'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $adresse = $_POST['adresse'];

    $stmt = $conn->prepare("INSERT INTO client (nomsCli, email, tel, adresse) VALUES (:nomsCli, :email, :tel, :adresse)");
    $stmt->bindParam(':nomsCli', $nomsCli);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: client.php");
    exit();
}

// Modification d'un client
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'modifier') {
    $id = $_POST['id'];
    $nomsCli = $_POST['nomsCli'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $adresse = $_POST['adresse'];

    $stmt = $conn->prepare("UPDATE client SET nomsCli = :nomsCli, email = :email, tel = :tel, adresse = :adresse WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nomsCli', $nomsCli);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: client.php");
    exit();
}

// Suppression d'un client
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'supprimer') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM client WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: client.php");
    exit();
}

// Récupération des clients
$stmt = $conn->prepare("SELECT * FROM client");
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Comptage des clients
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM client");
$stmt->execute();
$totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<?php
// Lien vers la NavBar
require_once('blade/DashHeader.php');
// Lien vers l'ASIDE
require_once('blade/AsideUser.php');
?>
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Gestion Clients</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
          <li class="breadcrumb-item active">Clients</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-xxl-12 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Clients <span>| Tout</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= $totalClients ?></h6>
                      <span class="text-success small pt-1 fw-bold">Clients</span> <span class="text-muted small pt-2 ps-1">enregistrés</span>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Sales Card -->
          </div>
        </div><!-- End Left side columns -->
      </div>
    </section>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Les Clients enregistrés</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal">Nouveau</button>
              <!-- Formulaire d'enregistrement client -->
              <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="saveModalLabel">Enregistrer</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form method="POST" action="client.php">
                        <input type="hidden" name="action" value="enregistrer">
                        <div class="mb-3">
                          <label for="saveNomsCli" class="form-label">Nom du Client</label>
                          <input type="text" name="nomsCli" class="form-control" id="saveNomsCli" placeholder="Entrez le nom">
                        </div>
                        <div class="mb-3">
                          <label for="saveEmail" class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" id="saveEmail" placeholder="Entrez l'email">
                        </div>
                        <div class="mb-3">
                          <label for="saveTel" class="form-label">Téléphone</label>
                          <input type="text" name="tel" class="form-control" id="saveTel" placeholder="Entrez le téléphone">
                        </div>
                        <div class="mb-3">
                          <label for="saveAdresse" class="form-label">Adresse</label>
                          <input type="text" name="adresse" class="form-control" id="saveAdresse" placeholder="Entrez l'adresse">
                        </div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <!--Fin Formulaire d'enregistrement client -->
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">id</th>
                    <th scope="col">Nom du Client</th>
                    <th scope="col">Email</th>
                    <th scope="col">Téléphone</th>
                    <th scope="col">Adresse</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($clients as $client): ?>
                  <tr>
                    <th scope="row"><?= htmlspecialchars($client['id']) ?></th>
                    <td><?= htmlspecialchars($client['nomsCli']) ?></td>
                    <td><?= htmlspecialchars($client['email']) ?></td>
                    <td><?= htmlspecialchars($client['tel']) ?></td>
                    <td><?= htmlspecialchars($client['adresse']) ?></td>
                    <td>
                      <!-- icone pour modifier le client -->
                      <i type="button" class="bi bi-pencil-square fs-3 text-success" data-bs-toggle="modal" data-bs-target="#modalModification<?= $client['id'] ?>"></i>
                      <!-- icone pour supprimer -->
                      <i type="button" class="bi bi-trash fs-3 text-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression<?= $client['id'] ?>"></i>
                      <!-- modale pour modifier le client -->
                      <div class="modal fade" id="modalModification<?= $client['id'] ?>" tabindex="-1" aria-labelledby="modalModificationLabel<?= $client['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalModificationLabel<?= $client['id'] ?>">Modification</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <form method="POST" action="client.php">
                                <input type="hidden" name="action" value="modifier">
                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                <div class="mb-3">
                                  <label for="modNomsCli<?= $client['id'] ?>" class="form-label">Nom du Client</label>
                                  <input type="text" name="nomsCli" class="form-control" id="modNomsCli<?= $client['id'] ?>" value="<?= htmlspecialchars($client['nomsCli']) ?>">
                                </div>
                                <div class="mb-3">
                                  <label for="modEmail<?= $client['id'] ?>" class="form-label">Email</label>
                                  <input type="email" name="email" class="form-control" id="modEmail<?= $client['id'] ?>" value="<?= htmlspecialchars($client['email']) ?>">
                                </div>
                                <div class="mb-3">
                                  <label for="modTel<?= $client['id'] ?>" class="form-label">Téléphone</label>
                                  <input type="text" name="tel" class="form-control" id="modTel<?= $client['id'] ?>" value="<?= htmlspecialchars($client['tel']) ?>">
                                </div>
                                <div class="mb-3">
                                  <label for="modAdresse<?= $client['id'] ?>" class="form-label">Adresse</label>
                                  <input type="text" name="adresse" class="form-control" id="modAdresse<?= $client['id'] ?>" value="<?= htmlspecialchars($client['adresse']) ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Modifier</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- fin modale de modification -->
                      <!-- modale pour accepter la suppression -->
                      <div class="modal fade" id="modalSuppression<?= $client['id'] ?>" tabindex="-1" aria-labelledby="modalSuppressionLabel<?= $client['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalSuppressionLabel<?= $client['id'] ?>">Suppression</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              Êtes-vous sûr de vouloir supprimer cet élément ?
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                              <form method="POST" action="client.php">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- fin modale de suppression -->
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
<?php
// Lien vers le footer
require_once('blade/DashFooter.php');
?>
