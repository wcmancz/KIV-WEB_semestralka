<?php

if(!defined('ROOT')) {
    header('Location: ../../index.php?page=login');
    exit;
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo – přehled zvířat</title>
    <?php /* Styly.
       1) bootstrap.min.css – hotové styly (karty, tlačítka, navigace, mřížka…); HTML je jen používá přes názvy tříd
       2) all.min.css – ikony Font Awesome (<i class="fa-solid fa-paw">)
       3) style.css – naše úpravy (zelená barva, drobnosti) */ ?>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php /* Navigační lišta. „navbar“, „bg-primary“ apod. jsou třídy Bootstrapu – jejich vzhled je definovaný v bootstrap.min.css. */ ?>
<nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i>Zoo</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <?php /* Podmínka: přihlášený vidí jméno + Odhlásit se, nepřihlášený tlačítko Přihlásit se.
                     htmlspecialchars() zabrání vložení cizího HTML (ochrana před XSS) */ ?>
            <?php if ($user): ?>
                <span class="text-end lh-sm">
                    <?= htmlspecialchars($user['jmeno']) ?>
                    <?php if ($user['typ'] === 'zamestnanec'): ?>
                        <small class="d-block text-white-50"><?= htmlspecialchars($user['role']) ?></small>
                    <?php endif; ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="index.php?page=logout"><i class="fa-solid fa-right-from-bracket me-1"></i>Odhlásit se</a>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="index.php?page=login"><i class="fa-solid fa-right-to-bracket me-1"></i>Přihlásit se</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container py-4" style="max-width: 52rem;">
    <h1 class="mb-4">Naše zvířata</h1>

    <?php /* Pro každý pavilon jedna karta z Bootstrapu, uvnitř seznam jeho zvířat. */ ?>
    <?php foreach ($pavilony as $pav): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
                <h2 class="h5 mb-0"><?= htmlspecialchars($pav['info']['jmeno_pavilonu']) ?></h2>
                <small class="text-body-secondary"><?= htmlspecialchars($pav['info']['lokace']) ?>, <?= number_format($pav['info']['rozloha'], 0, ',', ' ') ?> m²</small>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($pav['zvirata'] ?? [] as $z): ?>
                    <li class="list-group-item">
                        <span class="fw-semibold"><?= htmlspecialchars($z['jmeno_zvirete']) ?></span>
                        <small class="d-block text-body-secondary"><?= htmlspecialchars($z['typ_potravy'] ?? 'Potrava neuvedena') ?></small>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($pav['zvirata'])): ?>
                    <li class="list-group-item text-body-secondary">Zatím tu nejsou žádná zvířata.</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</main>
</body>
</html>
