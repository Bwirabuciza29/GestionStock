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

// Fonction pour obtenir les commandes avec les informations des clients et des produits
function getAllOrders($pdo)
{
    $stmt = $pdo->query("SELECT commande.id, client.nomsCli, client.email, client.tel, produit.desProd, commande.qProd, commande.prixUni, (commande.qProd * commande.prixUni) AS prixTotal, commande.dateCmd
                         FROM commande
                         JOIN client ON commande.idCli = client.id
                         JOIN produit ON commande.idProd = produit.id
                         ORDER BY commande.dateCmd DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtenir toutes les commandes
$orders = getAllOrders($pdo);
?>

<?php
// Inclure le header et la barre de navigation
require_once('blade/DashHeader.php');
require_once('blade/AsideUser.php');
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Liste des Commandes</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                <li class="breadcrumb-item active">Commandes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Toutes les Commandes</h5>

                        <!-- Tableau des commandes -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nom du Client</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Téléphone</th>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Quantité</th>
                                    <th scope="col">Prix Unitaire</th>
                                    <th scope="col">Prix Total</th>
                                    <th scope="col">Date de Commande</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $index => $order) : ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($order['nomsCli']) ?></td>
                                        <td><?= htmlspecialchars($order['email']) ?></td>
                                        <td><?= htmlspecialchars($order['tel']) ?></td>
                                        <td><?= htmlspecialchars($order['desProd']) ?></td>
                                        <td><?= htmlspecialchars($order['qProd']) ?></td>
                                        <td><?= htmlspecialchars($order['prixUni']) ?></td>
                                        <td><?= htmlspecialchars($order['prixTotal']) ?></td>
                                        <td><?= htmlspecialchars($order['dateCmd']) ?></td>
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