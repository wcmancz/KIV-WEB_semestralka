<?php
/*
 * login.php – VIEW: šablona přihlášení, jen HTML.
 *
 * Žádné SQL ani kontrola hesla – jen výpis proměnných, které připravil LoginController:
 * $chyba, $uspech, $typ a $login. Načítá ji LoginController (index.php?page=login).
 */

// Ochrana: když někdo otevře soubor napřímo (ROOT zná jen index.php), pošleme ho přes rozcestník.
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

                    <?php /* method="post" odešle data na server; action míří zpátky na tuto stránku (zpracuje ji PHP nahoře). */ ?>
                    <form method="post" action="index.php?page=login">
                        <?php /* Přepínač typu účtu: radio tlačítka „přestylovaná“ třídami btn-check a btn-outline-primary. Bez JavaScriptu.
                         Odeslaná hodnota je v $_POST['typ'] (navstevnik / zamestnanec); PHP nahoře podle ní vybírá, kde se uživatel hledá.
                         Po chybě se volba vrací zpět: tlačítko, které bylo vybrané, dostane atribut checked. */ ?>
                        <div class="btn-group w-100 mb-4" role="group" aria-label="Typ účtu">
                            <input type="radio" class="btn-check" name="typ" id="t-nav" value="navstevnik" <?= $typ !== 'zamestnanec' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="t-nav">Návštěvník</label>
                            <input type="radio" class="btn-check" name="typ" id="t-zam" value="zamestnanec" <?= $typ === 'zamestnanec' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="t-zam">Zaměstnanec</label>
                        </div>

                        <?php /* Zelená zpráva „Účet byl vytvořen“ – zobrazí se jednou po úspěšné registraci. */ ?>
                        <?php if ($uspech): ?>
                            <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
                        <?php endif; ?>

                        <?php /* Chybová hláška se zobrazí jen tehdy, když PHP nahoře nastavilo $chyba. */ ?>
                        <?php if ($chyba): ?>
                            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chyba) ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="identifikator" class="form-label">E-mail / přihlašovací jméno</label>
                            <input type="text" class="form-control" id="identifikator" name="user[login]" value="<?= htmlspecialchars($login) ?>" autocomplete="username" required autofocus>
                            <div class="form-text">Návštěvník zadává e-mail, zaměstnanec přihlašovací jméno.</div>
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