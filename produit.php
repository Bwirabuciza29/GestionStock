<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once('config.php');

// Enregistrement d'un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'enregistrer') {
    $desProd = $_POST['desProd'];
    $catProd = $_POST['catProd'];
    $prixUni = $_POST['prixUni'];

    $stmt = $conn->prepare("INSERT INTO produit (desProd, catProd, prixUni) VALUES (:desProd, :catProd, :prixUni)");
    $stmt->bindParam(':desProd', $desProd);
    $stmt->bindParam(':catProd', $catProd);
    $stmt->bindParam(':prixUni', $prixUni);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: produit.php");
    exit();
}

// Modification d'un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'modifier') {
    $id = $_POST['id'];
    $desProd = $_POST['desProd'];
    $catProd = $_POST['catProd'];
    $prixUni = $_POST['prixUni'];

    $stmt = $conn->prepare("UPDATE produit SET desProd = :desProd, catProd = :catProd, prixUni = :prixUni WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':desProd', $desProd);
    $stmt->bindParam(':catProd', $catProd);
    $stmt->bindParam(':prixUni', $prixUni);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: produit.php");
    exit();
}

// Suppression d'un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'supprimer') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM produit WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: produit.php");
    exit();
}

// Récupération des produits
$stmt = $conn->prepare("SELECT * FROM produit");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Comptage des produits
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM produit");
$stmt->execute();
$totalProduits = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<?php
// Lien vers la NavBar
require_once('blade/DashHeader.php');
// Lien vers l'ASIDE
require_once('blade/AsideUser.php');
?>
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Gestion Produits</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
          <li class="breadcrumb-item active">Produits</li>
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
                  <h5 class="card-title">Produits <span>| Tout</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart4"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= $totalProduits ?></h6>
                      <span class="text-success small pt-1 fw-bold">Produits</span> <span class="text-muted small pt-2 ps-1">dans le stock</span>
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
              <h5 class="card-title">Les Produits enregistrés</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal">Nouveau</button>
              <!-- Formulaire d'enregistrement produit -->
              <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="saveModalLabel">Enregistrer</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form method="POST" action="produit.php">
                        <input type="hidden" name="action" value="enregistrer">
                        <div class="mb-3">
                          <label for="saveDesProd" class="form-label">Désignation Produit</label>
                          <input type="text" name="desProd" class="form-control" id="saveDesProd" placeholder="Entrez la désignation">
                        </div>
                        <div class="mb-3">
                          <label for="saveCatProd" class="form-label">Catégorie Produit</label>
                          <input type="text" name="catProd" class="form-control" id="saveCatProd" placeholder="Entrez la catégorie">
                        </div>
                        <div class="mb-3">
                          <label for="savePrixUni" class="form-label">Prix</label>
                          <input type="text" name="prixUni" class="form-control" id="savePrixUni" placeholder="Entrez le prix">
                        </div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <!--Fin Formulaire d'enregistrement produit -->
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">Désignation du Produit</th>
                    <th scope="col">Catégorie</th>
                    <th scope="col">Prix Unitaire</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($produits as $produit): ?>
                  <tr>
                    <td><?= htmlspecialchars($produit['desProd']) ?></td>
                    <td><?= htmlspecialchars($produit['catProd']) ?></td>
                    <td><?= htmlspecialchars($produit['prixUni']) ?></td>
                    <td>
                      <!-- icone pour modifier le produit -->
                      <i type="button" class="bi bi-pencil-square fs-3 text-success" data-bs-toggle="modal" data-bs-target="#modalModification<?= $produit['id'] ?>"></i>
                      <!-- icone pour supprimer -->
                      <i type="button" class="bi bi-trash fs-3 text-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression<?= $produit['id'] ?>"></i>
                      <!-- modale pour modifier le produit -->
                      <div class="modal fade" id="modalModification<?= $produit['id'] ?>" tabindex="-1" aria-labelledby="modalModificationLabel<?= $produit['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalModificationLabel<?= $produit['id'] ?>">Modification</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <form method="POST" action="produit.php">
                                <input type="hidden" name="action" value="modifier">
                                <input type="hidden" name="id" value="<?= $produit['id'] ?>">
                                <div class="mb-3">
                                  <label for="modDesProd<?= $produit['id'] ?>" class="form-label">Désignation Produit</label>
                                  <input type="text" name="desProd" class="form-control" id="modDesProd<?= $produit['id'] ?>" value="<?= htmlspecialchars($produit['desProd']) ?>">
                                </div>
                                <div class="mb-3">
                                  <label for="modCatProd<?= $produit['id'] ?>" class="form-label">Catégorie Produit</label>
                                  <input type="text" name="catProd" class="form-control" id="modCatProd<?= $produit['id'] ?>" value="<?= htmlspecialchars($produit['catProd']) ?>">
                                </div>
                                <div class="mb-3">
                                  <label for="modPrixUni<?= $produit['id'] ?>" class="form-label">Prix</label>
                                  <input type="text" name="prixUni" class="form-control" id="modPrixUni<?= $produit['id'] ?>" value="<?= htmlspecialchars($produit['prixUni']) ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Modifier</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- fin modale de modification -->
                      <!-- modale pour accepter la suppression -->
                      <div class="modal fade" id="modalSuppression<?= $produit['id'] ?>" tabindex="-1" aria-labelledby="modalSuppressionLabel<?= $produit['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalSuppressionLabel<?= $produit['id'] ?>">Suppression</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              Êtes-vous sûr de vouloir supprimer cet élément ?
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                              <form method="POST" action="produit.php">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= $produit['id'] ?>">
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
