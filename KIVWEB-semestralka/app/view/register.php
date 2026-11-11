<?php
/*
 * proměnné připravil RegisterController: $chyby, $jmeno, $email.
 * Načítá ji RegisterController (index.php?page=register).
 */

// Ochrana: když někdo otevře soubor napřímo (ROOT zná jen index.php), pošleme ho přes rozcestník.
if (!defined('ROOT')) {
    header('Location: ../../index.php?page=register');
    exit;
}

// Pomocná funkce jen pro vzhled: třída is-invalid obarví políčko s chybou červeně (Bootstrap).
$neplatne = fn(string $pole): string => isset($chyby[$pole]) ? ' is-invalid' : '';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrace – Zoo</title>
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
                    <h1 class="h4 mb-4">Registrace</h1>

                    <?php /* novalidate vypne kontrolu prohlížeče, aby se vždy ukázaly chyby ze serveru (ty jsou rozhodující). */ ?>
                    <form method="post" action="index.php?page=register" novalidate>
                        <div class="mb-3">
                            <label for="jmeno" class="form-label">Jméno</label>
                            <input type="text" class="form-control<?= $neplatne('jmeno') ?>" id="jmeno" name="jmeno" value="<?= htmlspecialchars($jmeno) ?>" maxlength="50" autocomplete="name" required autofocus>
                            <?php if (isset($chyby['jmeno'])): ?><div class="invalid-feedback"><?= htmlspecialchars($chyby['jmeno']) ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control<?= $neplatne('email') ?>" id="email" name="email" value="<?= htmlspecialchars($email) ?>" maxlength="100" autocomplete="email" required>
                            <?php if (isset($chyby['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($chyby['email']) ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="heslo" class="form-label">Heslo</label>
                            <input type="password" class="form-control<?= $neplatne('heslo') ?>" id="heslo" name="heslo" minlength="8" autocomplete="new-password" required>
                            <?php if (isset($chyby['heslo'])): ?><div class="invalid-feedback"><?= htmlspecialchars($chyby['heslo']) ?></div><?php endif; ?>
                            <div class="form-text">Alespoň 8 znaků.</div>
                        </div>
                        <div class="mb-4">
                            <label for="heslo2" class="form-label">Heslo znovu</label>
                            <input type="password" class="form-control<?= $neplatne('heslo2') ?>" id="heslo2" name="heslo2" minlength="8" autocomplete="new-password" required>
                            <?php if (isset($chyby['heslo2'])): ?><div class="invalid-feedback"><?= htmlspecialchars($chyby['heslo2']) ?></div><?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-user-plus me-2"></i>Vytvořit účet</button>
                    </form>
                </div>
            </div>

            <p class="text-center text-body-secondary small mt-3 mb-0">Už máte účet? <a href="index.php?page=login">Přihlaste se</a></p>
        </div>
    </div>
</main>
</body>
</html>