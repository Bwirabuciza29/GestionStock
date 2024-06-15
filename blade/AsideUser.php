<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link collapsed" href="home.php">
                <i class="bi bi-grid"></i>
                <span>Tableau de bord</span>
            </a>
        </li><!-- Vente page Nav -->
        <li class="nav-heading">Gestion Produit || Clients</li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="produit.php">
                <i class='bx bx-data'></i>
                <span>Produits</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="client.php">
                <i class='bx bx-user-check'></i>
                <span>Client</span>
            </a>
        </li>
        <li class="nav-heading">Gestion Des Entrées</li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="fournisseur.php">
                <i class='bx bxs-user-detail'></i>
                <span>Fournisseur</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="appro.php">
                <i class='bx bxs-cart-add'></i>
                <span>Approvisionnement</span>
            </a>
        </li>
        <!-- Commande Page Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="commande.php">
                <i class="bi bi-cart4"></i>
                <span>Commandes</span>
            </a>
            <!-- Stock Page Nav -->
        </li>
        <li class="nav-heading">Gestion Stock</li>
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Stock</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="stockProduit.php">
                        <i class="bi bi-cart-plus-fill"></i><span>Produits</span>
                    </a>
                </li>
                <li>
                    <a href="sortie.php">
                        <i class="bi bi-plus-circle-dotted"></i><span>Approvisionnements</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Données Extérieures Nav -->
        </li>
    </ul>
</aside><!-- Fin Sidebar-->