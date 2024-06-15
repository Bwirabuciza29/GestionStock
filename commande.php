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

// Fonction pour obtenir tous les clients
function getClients($pdo) {
    $stmt = $pdo->query("SELECT id, nomsCli FROM client");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir tous les produits
function getProduits($pdo) {
    $stmt = $pdo->query("SELECT id, desProd, prix FROM produit"); // Inclure le prix dans la requête
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour générer un nouveau numéro de commande
function generateNumCmd($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM commande");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'] + 1;
    return 'CMD' . str_pad($count, 3, '0', STR_PAD_LEFT);
}

// Ajouter une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'ajouter') {
    $idCli = $_POST['idCli'];
    $idProd = $_POST['idProd'];
    $qProd = $_POST['qProd'];
    $prixUni = $_POST['prixUni'];
    $dateCmd = $_POST['dateCmd'];
    $numCmd = $_POST['numCmd'];

    $stmt = $pdo->prepare("INSERT INTO commande (idCli, idProd, qProd, prixUni, dateCmd, numCmd) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idCli, $idProd, $qProd, $prixUni, $dateCmd, $numCmd]);

    header("Location: commande.php");
    exit();
}

// Modifier une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'modifier') {
    $id = $_POST['id'];
    $idCli = $_POST['idCli'];
    $idProd = $_POST['idProd'];
    $qProd = $_POST['qProd'];
    $prixUni = $_POST['prixUni'];
    $dateCmd = $_POST['dateCmd'];

    $stmt = $pdo->prepare("UPDATE commande SET idCli = ?, idProd = ?, qProd = ?, prixUni = ?, dateCmd = ? WHERE id = ?");
    $stmt->execute([$idCli, $idProd, $qProd, $prixUni, $dateCmd, $id]);

    header("Location: commande.php");
    exit();
}

// Supprimer une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'supprimer') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM commande WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: commande.php");
    exit();
}

// Obtenir toutes les commandes
$stmt = $pdo->query("SELECT commande.id, client.nomsCli AS nomClient, produit.desProd AS designationProduit, commande.qProd, commande.prixUni, commande.dateCmd, commande.numCmd, commande.idCli, commande.idProd
                     FROM commande
                     INNER JOIN client ON commande.idCli = client.id
                     INNER JOIN produit ON commande.idProd = produit.id");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtenir le nombre total de commandes
function countCommandes($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM commande");
    return $stmt->fetchColumn();
}

$clients = getClients($pdo);
$produits = getProduits($pdo);
$nombreCommandes = countCommandes($pdo);
?>

<?php
// Inclure le header et la barre de navigation
require_once('blade/DashHeader.php');
require_once('blade/AsideUser.php');
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Gestion des Commandes</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                <li class="breadcrumb-item active">Commandes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <!-- Carte des commandes -->
            <div class="col-xxl-12 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Commandes <span>| Tout</span></h5>

                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="ps-3">
                                <h6><?= htmlspecialchars($nombreCommandes) ?></h6>
                                <span class="text-success small pt-1 fw-bold">Commandes</span>
                                <span class="text-muted small pt-2 ps-1"> enregistrées</span>
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
                        <h5 class="card-title">Les Commandes enregistrées</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal">
                            Nouvelle Commande
                        </button>
                        <!-- Formulaire d'enregistrement commande -->
                        <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="saveModalLabel">Enregistrer une commande</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="commande.php">
                                            <input type="hidden" name="action" value="ajouter">
                                            <input type="hidden" name="numCmd" value="<?= generateNumCmd($pdo) ?>"> <!-- Générer le numéro de commande ici -->
                                            <div class="mb-3">
                                                <label for="selectClient" class="form-label">Client</label>
                                                <select class="form-select" name="idCli" id="selectClient" required>
                                                    <?php foreach ($clients as $client): ?>
                                                        <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nomsCli']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="selectProduit" class="form-label">Produit</label>
                                                <select class="form-select" name="idProd" id="selectProduit" required>
                                                    <?php foreach ($produits as $produit): ?>
                                                        <option value="<?= $produit['id'] ?>" data-prix="<?= $produit['prix'] ?>">
                                                            <?= htmlspecialchars($produit['desProd']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="qProd" class="form-label">Quantité</label>
                                                <input type="number" name="qProd" class="form-control" id="qProd" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="prixUni" class="form-label">Prix Unitaire</label>
                                                <input type="number" step="0.01" name="prixUni" class="form-control" id="prixUni" required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="dateCmd" class="form-label">Date de Commande</label>
                                                <input type="date" name="dateCmd" class="form-control" id="dateCmd" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Fin Formulaire d'enregistrement commande -->

                        <!-- Tableau des commandes -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Quantité</th>
                                    <th scope="col">Prix Unitaire</th>
                                    <th scope="col">Date de Commande</th>
                                    <th scope="col">Numéro de Commande</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commandes as $commande): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($commande['id']) ?></td>
                                        <td><?= htmlspecialchars($commande['nomClient']) ?></td>
                                        <td><?= htmlspecialchars($commande['designationProduit']) ?></td>
                                        <td><?= htmlspecialchars($commande['qProd']) ?></td>
                                        <td><?= htmlspecialchars($commande['prixUni']) ?></td>
                                        <td><?= htmlspecialchars($commande['dateCmd']) ?></td>
                                        <td><?= htmlspecialchars($commande['numCmd']) ?></td>
                                        <td>
                                            <!-- Bouton pour modifier -->
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalModification<?= $commande['id'] ?>">
                                                Modifier
                                            </button>
                                            <!-- Modale pour la modification -->
                                            <div class="modal fade" id="modalModification<?= $commande['id'] ?>" tabindex="-1" aria-labelledby="modalModificationLabel<?= $commande['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalModificationLabel<?= $commande['id'] ?>">Modification de la Commande</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="commande.php">
                                                                <input type="hidden" name="action" value="modifier">
                                                                <input type="hidden" name="id" value="<?= $commande['id'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="selectClientModif<?= $commande['id'] ?>" class="form-label">Client</label>
                                                                    <select class="form-select" name="idCli" id="selectClientModif<?= $commande['id'] ?>" required>
                                                                        <?php foreach ($clients as $client): ?>
                                                                            <option value="<?= $client['id'] ?>" <?= ($client['id'] == $commande['idCli']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($client['nomsCli']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="selectProduitModif<?= $commande['id'] ?>" class="form-label">Produit</label>
                                                                    <select class="form-select" name="idProd" id="selectProduitModif<?= $commande['id'] ?>" required>
                                                                        <?php foreach ($produits as $produit): ?>
                                                                            <option value="<?= $produit['id'] ?>" <?= ($produit['id'] == $commande['idProd']) ? 'selected' : '' ?> data-prix="<?= $produit['prix'] ?>">
                                                                                <?= htmlspecialchars($produit['desProd']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="qProdModif<?= $commande['id'] ?>" class="form-label">Quantité</label>
                                                                    <input type="number" name="qProd" class="form-control" id="qProdModif<?= $commande['id'] ?>" value="<?= htmlspecialchars($commande['qProd']) ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="prixUniModif<?= $commande['id'] ?>" class="form-label">Prix Unitaire</label>
                                                                    <input type="number" step="0.01" name="prixUni" class="form-control" id="prixUniModif<?= $commande['id'] ?>" value="<?= htmlspecialchars($commande['prixUni']) ?>" required readonly>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="dateCmdModif<?= $commande['id'] ?>" class="form-label">Date de Commande</label>
                                                                    <input type="date" name="dateCmd" class="form-control" id="dateCmdModif<?= $commande['id'] ?>" value="<?= htmlspecialchars($commande['dateCmd']) ?>" required>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary">Modifier</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Bouton pour supprimer -->
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression<?= $commande['id'] ?>">
                                                Supprimer
                                            </button>
                                            <!-- Modale pour la suppression -->
                                            <div class="modal fade" id="modalSuppression<?= $commande['id'] ?>" tabindex="-1" aria-labelledby="modalSuppressionLabel<?= $commande['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalSuppressionLabel<?= $commande['id'] ?>">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer cette commande ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form method="POST" action="commande.php">
                                                                <input type="hidden" name="action" value="supprimer">
                                                                <input type="hidden" name="id" value="<?= $commande['id'] ?>">
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
                        <!-- Fin Tableau des commandes -->
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

<script>
// Remplir automatiquement le prix unitaire en fonction du produit sélectionné
document.getElementById('selectProduit').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var prix = selectedOption.getAttribute('data-prix');
    document.getElementById('prixUni').value = prix;
});

// Faire de même pour les formulaires de modification
<?php foreach ($commandes as $commande): ?>
    document.getElementById('selectProduitModif<?= $commande['id'] ?>').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var prix = selectedOption.getAttribute('data-prix');
        document.getElementById('prixUniModif<?= $commande['id'] ?>').value = prix;
    });
<?php endforeach; ?>
</script>
