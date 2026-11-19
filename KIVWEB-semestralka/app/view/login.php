<?php
if (!defined('ROOT')) {
    header('Location: ../../index.php?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přihlášení – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center min-vh-100">
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4 col-xl-3">
            <div class="text-center brand mb-3"><i class="fa-solid fa-paw me-2"></i>Zoo</div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4">Přihlášení</h1>

                    <form method="post" action="index.php?page=login">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <?php if ($uspech): ?>
                            <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
                        <?php endif; ?>

                        <?php if ($chyba): ?>
                            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chyba) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="identifikator" class="form-label">Přihlašovací jméno nebo e-mail</label>
                            <input type="text" class="form-control" id="identifikator" name="user[login]" value="<?= htmlspecialchars($login) ?>" autocomplete="username" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="heslo" class="form-label">Heslo</label>
                            <input type="password" class="form-control" id="heslo" name="user[heslo]" autocomplete="current-password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-right-to-bracket me-2"></i>Přihlásit se</button>
                    </form>
                </div>
            </div>

            <p class="text-center text-body-secondary small mt-3 mb-0">Nemáte účet? <a href="index.php?page=register">Zaregistrujte se</a></p>
        </div>
    </div>
</main>
</body>
</html>