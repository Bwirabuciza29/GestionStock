<?php
session_start();
require_once('config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier si les champs 'email' et 'password' existent dans $_POST
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!empty($email) && !empty($password)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND mot_de_passe = :password");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header("Location: home.php");
                exit();
            } else {
                $error = 'Adresse email ou mot de passe incorrect';
            }
        } else {
            $error = 'Veuillez remplir tous les champs';
        }
    } else {
        $error = 'Adresse email et/ou mot de passe non spécifiés';
    }
}
?>

<?php require_once('blade/LoggerUp.php'); ?>
<main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Authentification</h5>
                                    <p class="text-center small">Entrez votre adresse email et votre mot de passe pour vous connecter</p>
                                </div>

                                <?php if ($error) : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= $error ?>
                                    </div>
                                <?php endif; ?>

                                <form class="row g-3 needs-validation" novalidate method="POST" action="index.php">
                                    <div class="col-12">
                                        <label for="yourEmail" class="form-label">Adresse Email</label>
                                        <input type="email" name="email" class="form-control" id="yourEmail" required>
                                        <div class="invalid-feedback">Veuillez entrer votre adresse email.</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPassword" class="form-label">Mot de passe</label>
                                        <input type="password" name="password" class="form-control" id="yourPassword" required>
                                        <div class="invalid-feedback">Veuillez entrer votre mot de passe.</div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Se Connecter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<?php require_once('blade/LoggerDown.php'); ?>