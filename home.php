<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

require_once 'config.php';

// Connectez-vous à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_stock', 'root', '');

// Récupérer le nombre total de produits
$stmt = $pdo->query("SELECT COUNT(*) AS total_products FROM produit");
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Récupérer le nombre total d'approvisionnements
$stmt = $pdo->query("SELECT SUM(quantProd) AS total_approvisionnements FROM appro");
$total_approvisionnements = $stmt->fetch(PDO::FETCH_ASSOC)['total_approvisionnements'];

// Récupérer le nombre total de clients
$stmt = $pdo->query("SELECT COUNT(*) AS total_clients FROM client");
$total_clients = $stmt->fetch(PDO::FETCH_ASSOC)['total_clients'];

// Connectez-vous à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_stock', 'root', '');

// Récupérer les informations des produits
$query = "
    SELECT 
        p.id, 
        p.desProd, 
        IFNULL(SUM(a.quantProd), 0) AS quantAppro, 
        IFNULL(SUM(c.qProd), 0) AS quantVendu,
        IFNULL(SUM(a.quantProd), 0) - IFNULL(SUM(c.qProd), 0) AS quantRestant
    FROM produit p
    LEFT JOIN appro a ON p.id = a.idProd
    LEFT JOIN commande c ON p.id = c.idProd
    GROUP BY p.id, p.desProd";

$stmt = $pdo->query($query);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
// Lien vers la NavBar
require_once('blade/DashHeader.php');
// Lien vers l'ASIDE
require_once('blade/AsideUser.php');
?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Tableau de bord</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
        <li class="breadcrumb-item active">Tableau de bord</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">
      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">
          <!-- Sales Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title">Produits <span>| Tout</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart4"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?= htmlspecialchars($total_products) ?></h6>
                    <span class="text-success small pt-1 fw-bold">Produits</span>
                    <span class="text-muted small pt-2 ps-1">dans le stock</span>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Sales Card -->

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">
              <div class="card-body">
                <h5 class="card-title">Approvisionnements</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart-plus"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?= htmlspecialchars($total_approvisionnements) ?></h6>
                    <span class="text-success small pt-1 fw-bold">Produits</span>
                    <span class="text-muted small pt-2 ps-1">Approvisionnés</span>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Revenue Card -->

          <!-- Customers Card -->
          <div class="col-xxl-4 col-xl-12">
            <div class="card info-card customers-card">
              <div class="card-body">
                <h5 class="card-title">Clients <span>| Total</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?= htmlspecialchars($total_clients) ?></h6>
                    <span class="text-danger small pt-1 fw-bold">Clients</span>
                    <span class="text-muted small pt-2 ps-1">enregistrés</span>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- End Customers Card -->
        </div>
      </div><!-- End Left side columns -->
    </div>
  </section>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Les Produits approvisionnés</h5>

            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th scope="col">Produit</th>
                  <th scope="col">Quantité Approvisionnée</th>
                  <th scope="col">Quantité Vendue</th>
                  <th scope="col">Quantité Restante</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($produits as $produit) : ?>
                  <tr>
                    <td><?= htmlspecialchars($produit['desProd']) ?></td>
                    <td><?= htmlspecialchars($produit['quantAppro']) ?></td>
                    <td><?= htmlspecialchars($produit['quantVendu']) ?></td>
                    <td><?= htmlspecialchars($produit['quantRestant']) ?></td>
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