<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once('config.php');

// Enregistrement d'un fournisseur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'enregistrer') {
    $noms = $_POST['noms'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $adresse = $_POST['adresse'];

    $stmt = $conn->prepare("INSERT INTO fournisseur (noms, email, tel, adresse) VALUES (:noms, :email, :tel, :adresse)");
    $stmt->bindParam(':noms', $noms);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: fournisseur.php");
    exit();
}

// Modification d'un fournisseur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'modifier') {
    $id = $_POST['id'];
    $noms = $_POST['noms'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $adresse = $_POST['adresse'];

    $stmt = $conn->prepare("UPDATE fournisseur SET noms = :noms, email = :email, tel = :tel, adresse = :adresse WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':noms', $noms);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: fournisseur.php");
    exit();
}

// Suppression d'un fournisseur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'supprimer') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM fournisseur WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: fournisseur.php");
    exit();
}

// Récupération des fournisseurs
$stmt = $conn->prepare("SELECT * FROM fournisseur");
$stmt->execute();
$fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Comptage des fournisseurs
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM fournisseur");
$stmt->execute();
$totalFournisseurs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<?php
// Lien vers la NavBar
require_once('blade/DashHeader.php');
// Lien vers l'ASIDE
require_once('blade/AsideUser.php');
?>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Gestion Fournisseurs</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                <li class="breadcrumb-item active">Fournisseurs</li>
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
                                <h5 class="card-title">Fournisseurs <span>| Tout</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?= $totalFournisseurs ?></h6>
                                        <span class="text-success small pt-1 fw-bold">Fournisseurs</span> <span class="text-muted small pt-2 ps-1">enregistrés</span>
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
                        <h5 class="card-title">Les Fournisseurs enregistrés</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal">Nouveau</button>
                        <!-- Formulaire d'enregistrement fournisseur -->
                        <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="saveModalLabel">Enregistrer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="fournisseur.php">
                                            <input type="hidden" name="action" value="enregistrer">
                                            <div class="mb-3">
                                                <label for="saveNoms" class="form-label">Nom du Fournisseur</label>
                                                <input type="text" name="noms" class="form-control" id="saveNoms" placeholder="Entrez le nom">
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
                        <!--Fin Formulaire d'enregistrement fournisseur -->
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">id</th>
                                    <th scope="col">Nom du Fournisseur</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Téléphone</th>
                                    <th scope="col">Adresse</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fournisseurs as $fournisseur) : ?>
                                    <tr>
                                        <th scope="row"><?= htmlspecialchars($fournisseur['id']) ?></th>
                                        <td><?= htmlspecialchars($fournisseur['noms']) ?></td>
                                        <td><?= htmlspecialchars($fournisseur['email']) ?></td>
                                        <td><?= htmlspecialchars($fournisseur['tel']) ?></td>
                                        <td><?= htmlspecialchars($fournisseur['adresse']) ?></td>
                                        <td>
                                            <!-- icone pour modifier le fournisseur -->
                                            <i type="button" class="bi bi-pencil-square fs-3 text-success" data-bs-toggle="modal" data-bs-target="#modalModification<?= $fournisseur['id'] ?>"></i>
                                            <!-- icone pour supprimer -->
                                            <i type="button" class="bi bi-trash fs-3 text-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression<?= $fournisseur['id'] ?>"></i>
                                            <!-- modale pour modifier le fournisseur -->
                                            <div class="modal fade" id="modalModification<?= $fournisseur['id'] ?>" tabindex="-1" aria-labelledby="modalModificationLabel<?= $fournisseur['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalModificationLabel<?= $fournisseur['id'] ?>">Modification</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="fournisseur.php">
                                                                <input type="hidden" name="action" value="modifier">
                                                                <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="modNoms<?= $fournisseur['id'] ?>" class="form-label">Nom du Fournisseur</label>
                                                                    <input type="text" name="noms" class="form-control" id="modNoms<?= $fournisseur['id'] ?>" value="<?= htmlspecialchars($fournisseur['noms']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="modEmail<?= $fournisseur['id'] ?>" class="form-label">Email</label>
                                                                    <input type="email" name="email" class="form-control" id="modEmail<?= $fournisseur['id'] ?>" value="<?= htmlspecialchars($fournisseur['email']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="modTel<?= $fournisseur['id'] ?>" class="form-label">Téléphone</label>
                                                                    <input type="text" name="tel" class="form-control" id="modTel<?= $fournisseur['id'] ?>" value="<?= htmlspecialchars($fournisseur['tel']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="modAdresse<?= $fournisseur['id'] ?>" class="form-label">Adresse</label>
                                                                    <input type="text" name="adresse" class="form-control" id="modAdresse<?= $fournisseur['id'] ?>" value="<?= htmlspecialchars($fournisseur['adresse']) ?>">
                                                                </div>
                                                                <button type="submit" class="btn btn-primary">Modifier</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- fin modale de modification -->
                                            <!-- modale pour accepter la suppression -->
                                            <div class="modal fade" id="modalSuppression<?= $fournisseur['id'] ?>" tabindex="-1" aria-labelledby="modalSuppressionLabel<?= $fournisseur['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalSuppressionLabel<?= $fournisseur['id'] ?>">Suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer cet élément ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form method="POST" action="fournisseur.php">
                                                                <input type="hidden" name="action" value="supprimer">
                                                                <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
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