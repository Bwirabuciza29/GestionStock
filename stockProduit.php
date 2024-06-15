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

// Fonction pour obtenir tous les produits avec leur stock
function getStock($pdo)
{
    $stmt = $pdo->query("SELECT produit.id, produit.desProd, COALESCE(SUM(appro.quantProd), 0) AS totalApprovisionne, COALESCE(SUM(commande.qProd), 0) AS totalCommande
                         FROM produit
                         LEFT JOIN appro ON produit.id = appro.idProd
                         LEFT JOIN commande ON produit.id = commande.idProd
                         GROUP BY produit.id, produit.desProd");
    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculer la quantité actuelle en stock et les produits commandés
    foreach ($stocks as &$produit) {
        $produit['quantiteActuelle'] = $produit['totalApprovisionne'] - $produit['totalCommande'];
        $produit['produitsCommandes'] = $produit['totalCommande'];
    }

    return $stocks;
}

// Obtenir le stock actuel
$stock = getStock($pdo);

// Gestion de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'commander') {
    $idProd = $_POST['idProd'];
    $qProd = $_POST['qProd'];

    // Vérifier si la quantité commandée ne dépasse pas la quantité en stock
    foreach ($stock as $produit) {
        if ($produit['id'] == $idProd) {
            if ($produit['quantiteActuelle'] >= $qProd) {
                // Diminuer la quantité en stock
                $stmt = $pdo->prepare("UPDATE produit SET quantiteStock = quantiteStock - ? WHERE id = ?");
                $stmt->execute([$qProd, $idProd]);

                // Rediriger avec un message de succès
                header("Location: stock.php?commande=success");
                exit();
            } else {
                // Rediriger avec un message d'erreur
                header("Location: stock.php?commande=failed");
                exit();
            }
        }
    }
}
?>

<?php
// Inclure le header et la barre de navigation
require_once('blade/DashHeader.php');
require_once('blade/AsideUser.php');
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Gestion du Stock</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                <li class="breadcrumb-item active">Stock</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Produits en Stock</h5>

                        <!-- Tableau des produits en stock -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Désignation du Produit</th>
                                    <th scope="col">Quantité Totale Approvisionnée</th>
                                    <th scope="col">Quantité Commandée</th>
                                    <th scope="col">Quantité en Stock</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock as $index => $produit) : ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($produit['desProd']) ?></td>
                                        <td><?= htmlspecialchars($produit['totalApprovisionne']) ?></td>
                                        <td><?= htmlspecialchars($produit['produitsCommandes']) ?></td>
                                        <td><?= htmlspecialchars($produit['quantiteActuelle']) ?></td>
                                        <td>
                                            <!-- Formulaire pour commander -->
                                            <form method="POST" action="stock.php">
                                                <input type="hidden" name="action" value="commander">
                                                <input type="hidden" name="idProd" value="<?= $produit['id'] ?>">
                                                <div class="input-group">
                                                    <input type="number" name="qProd" class="form-control" placeholder="Quantité" min="1" max="<?= $produit['quantiteActuelle'] ?>" required>
                                                    <button type="submit" class="btn btn-primary">Commander</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Fin Tableau des produits en stock -->
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

<?php
// Affichage des messages de succès ou d'échec de commande
if (isset($_GET['commande'])) {
    if ($_GET['commande'] === 'success') {
        echo '<script>alert("Commande passée avec succès !")</script>';
    } elseif ($_GET['commande'] === 'failed') {
        echo '<script>alert("La commande a échoué : quantité en stock insuffisante.")</script>';
    }
}
?>