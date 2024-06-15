<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Inclure la configuration de la base de données
require_once 'config.php';

// Définir la connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=gestion_stock', 'root', '');

// Fonction pour obtenir tous les fournisseurs
function getFournisseurs($pdo)
{
    $stmt = $pdo->query("SELECT id, noms FROM fournisseur");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir tous les produits
function getProduits($pdo)
{
    $stmt = $pdo->query("SELECT id, desProd FROM produit");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter un approvisionnement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'ajouter') {
    $idFourn = $_POST['idFourn'];
    $idProd = $_POST['idProd'];
    $quantProd = $_POST['quantProd'];
    $dateApp = $_POST['dateApp'];

    $stmt = $pdo->prepare("INSERT INTO appro (idFourn, idProd, quantProd, dateApp) VALUES (?, ?, ?, ?)");
    $stmt->execute([$idFourn, $idProd, $quantProd, $dateApp]);

    header("Location: appro.php");
    exit();
}

// Modifier un approvisionnement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'modifier') {
    $id = $_POST['id'];
    $idFourn = $_POST['idFourn'];
    $idProd = $_POST['idProd'];
    $quantProd = $_POST['quantProd'];
    $dateApp = $_POST['dateApp'];

    $stmt = $pdo->prepare("UPDATE appro SET idFourn = ?, idProd = ?, quantProd = ?, dateApp = ? WHERE id = ?");
    $stmt->execute([$idFourn, $idProd, $quantProd, $dateApp, $id]);

    header("Location: appro.php");
    exit();
}

// Supprimer un approvisionnement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'supprimer') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM appro WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: appro.php");
    exit();
}

// Obtenir tous les approvisionnements
$stmt = $pdo->query("SELECT appro.id, fournisseur.noms AS nomFournisseur, produit.desProd AS designationProduit, appro.quantProd, appro.dateApp
                     FROM appro
                     INNER JOIN fournisseur ON appro.idFourn = fournisseur.id
                     INNER JOIN produit ON appro.idProd = produit.id");
$approvisionnements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtenir le nombre total d'approvisionnements
function countApprovisionnements($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM appro");
    return $stmt->fetchColumn();
}

$fournisseurs = getFournisseurs($pdo);
$produits = getProduits($pdo);
$nombreApprovisionnements = countApprovisionnements($pdo);
?>

<?php
// Inclure le header et la barre de navigation
require_once('blade/DashHeader.php');
require_once('blade/AsideUser.php');
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Gestion des Approvisionnements</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                <li class="breadcrumb-item active">Approvisionnements</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <!-- Carte des approvisionnements -->
            <div class="col-xxl-12 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Approvisionnements <span>| Tout</span></h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= htmlspecialchars($nombreApprovisionnements) ?></h6>
                                <span class="text-success small pt-1 fw-bold">Approvisionnements</span>
                                <span class="text-muted small pt-2 ps-1"> en stock</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Card -->
        </div>
    </section>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Les Approvisionnements enregistrés</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal">
                            Nouveau
                        </button>
                        <!-- Formulaire d'enregistrement approvisionnement -->
                        <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="saveModalLabel">Enregistrer un approvisionnement</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="appro.php">
                                            <input type="hidden" name="action" value="ajouter">
                                            <div class="mb-3">
                                                <label for="selectFournisseur" class="form-label">Fournisseur</label>
                                                <select class="form-select" name="idFourn" id="selectFournisseur" required>
                                                    <?php foreach ($fournisseurs as $fournisseur) : ?>
                                                        <option value="<?= $fournisseur['id'] ?>"><?= htmlspecialchars($fournisseur['noms']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="selectProduit" class="form-label">Produit</label>
                                                <select class="form-select" name="idProd" id="selectProduit" required>
                                                    <?php foreach ($produits as $produit) : ?>
                                                        <option value="<?= $produit['id'] ?>"><?= htmlspecialchars($produit['desProd']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="quantProd" class="form-label">Quantité</label>
                                                <input type="number" name="quantProd" class="form-control" id="quantProd" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="dateApp" class="form-label">Date d'approvisionnement</label>
                                                <input type="date" name="dateApp" class="form-control" id="dateApp" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Fin Formulaire d'enregistrement approvisionnement -->
                        <!-- Table avec les approvisionnements -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Fournisseur</th>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Quantité</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approvisionnements as $approvisionnement) : ?>
                                    <tr>
                                        <th scope="row"><?= htmlspecialchars($approvisionnement['id']) ?></th>
                                        <td><?= htmlspecialchars($approvisionnement['nomFournisseur']) ?></td>
                                        <td><?= htmlspecialchars($approvisionnement['designationProduit']) ?></td>
                                        <td><?= htmlspecialchars($approvisionnement['quantProd']) ?></td>
                                        <td><?= htmlspecialchars($approvisionnement['dateApp']) ?></td>
                                        <td>
                                            <!-- Bouton pour modifier -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalModification<?= $approvisionnement['id'] ?>">
                                                Modifier
                                            </button>
                                            <!-- Modale pour la modification -->
                                            <div class="modal fade" id="modalModification<?= $approvisionnement['id'] ?>" tabindex="-1" aria-labelledby="modalModificationLabel<?= $approvisionnement['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalModificationLabel<?= $approvisionnement['id'] ?>">Modification de l'Approvisionnement</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="appro.php">
                                                                <input type="hidden" name="action" value="modifier">
                                                                <input type="hidden" name="id" value="<?= $approvisionnement['id'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="selectFournisseurModif<?= $approvisionnement['id'] ?>" class="form-label">Fournisseur</label>
                                                                    <select class="form-select" name="idFourn" id="selectFournisseurModif<?= $approvisionnement['id'] ?>" required>
                                                                        <?php foreach ($fournisseurs as $fournisseur) : ?>
                                                                            <option value="<?= $fournisseur['id'] ?>" <?= ($fournisseur['id'] == $approvisionnement['idFourn']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($fournisseur['noms']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="selectProduitModif<?= $approvisionnement['id'] ?>" class="form-label">Produit</label>
                                                                    <select class="form-select" name="idProd" id="selectProduitModif<?= $approvisionnement['id'] ?>" required>
                                                                        <?php foreach ($produits as $produit) : ?>
                                                                            <option value="<?= $produit['id'] ?>" <?= ($produit['id'] == $approvisionnement['idProd']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($produit['desProd']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="quantProdModif<?= $approvisionnement['id'] ?>" class="form-label">Quantité</label>
                                                                    <input type="number" name="quantProd" class="form-control" id="quantProdModif<?= $approvisionnement['id'] ?>" value="<?= htmlspecialchars($approvisionnement['quantProd']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="dateAppModif<?= $approvisionnement['id'] ?>" class="form-label">Date d'approvisionnement</label>
                                                                    <input type="date" name="dateApp" class="form-control" id="dateAppModif<?= $approvisionnement['id'] ?>" value="<?= htmlspecialchars($approvisionnement['dateApp']) ?>" required>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary">Modifier</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Bouton pour supprimer -->
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression<?= $approvisionnement['id'] ?>">
                                                Supprimer
                                            </button>
                                            <!-- Modale pour la suppression -->
                                            <div class="modal fade" id="modalSuppression<?= $approvisionnement['id'] ?>" tabindex="-1" aria-labelledby="modalSuppressionLabel<?= $approvisionnement['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalSuppressionLabel<?= $approvisionnement['id'] ?>">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer cet approvisionnement ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form method="POST" action="appro.php">
                                                                <input type="hidden" name="action" value="supprimer">
                                                                <input type="hidden" name="id" value="<?= $approvisionnement['id'] ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Fin Tableau des approvisionnements -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
// Inclure le footer
require_once('blade/DashFooter.php');
?>