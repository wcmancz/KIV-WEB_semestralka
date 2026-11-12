<?php
/*
 * 403.PHP – VIEW (šablona) stránky „Přístup odepřen“.
 *
 * Zobrazí ji BaseController::requireRole(), když je uživatel přihlášený, ale nemá právo na danou stránku.
 * Odpověď se odesílá s kódem 403 (Forbidden).
 */

// Ochrana: když někdo otevře soubor napřímo (ROOT zná jen index.php), pošleme ho na úvod.
if (!defined('ROOT')) {
    header('Location: ../../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přístup odepřen – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center min-vh-100">
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="display-5 text-body-secondary mb-2"><i class="fa-solid fa-lock"></i></div>
                    <h1 class="h4">Přístup odepřen</h1>
                    <p class="text-body-secondary">K této stránce nemáte oprávnění. Je určená pro správce.</p>
                    <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-house me-1"></i>Zpět na úvod</a>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>