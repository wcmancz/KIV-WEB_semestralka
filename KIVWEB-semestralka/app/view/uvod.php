<?php
if (!defined('ROOT')) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo – přehled zvířat</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php $aktivni = 'prehled'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4" style="max-width: 52rem;">
    <h1 class="mb-4">Naše zvířata</h1>

    <?php if ($user && $user['typ'] === 'zamestnanec'): ?>
        <div class="alert alert-warning" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i>
            <?php $n = count($sluzby); ?>
            <?= $n > 0
                    ? 'Máte aktivní službu u ' . $n . ' ' . ($n === 1 ? 'zvířete' : 'zvířat') . '. Najdete je označené v seznamu.'
                    : 'Nemáte teď žádnou aktivní službu.' ?>
        </div>
    <?php endif; ?>

    <?php foreach ($pavilony as $pav): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-baseline gap-1">
                <h2 class="h5 mb-0"><?= htmlspecialchars($pav['info']['jmeno_pavilonu']) ?></h2>
                <small class="text-body-secondary"><?= htmlspecialchars($pav['info']['lokace']) ?>, <?= number_format($pav['info']['rozloha'], 0, ',', ' ') ?> m²</small>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($pav['zvirata'] ?? [] as $z): ?>
                    <?php /* Služby přihlášeného zaměstnance u tohoto zvířete (prázdné pole = žádné). */ ?>
                    <?php $moje = $sluzby[$z['id_zvirata']] ?? []; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center gap-2<?= $moje ? ' list-group-item-warning' : '' ?>">
                        <div>
                            <span class="fw-semibold"><?= htmlspecialchars($z['jmeno_zvirete']) ?></span>
                            <small class="d-block text-body-secondary"><?= htmlspecialchars($z['typ_potravy'] ?? 'Potrava neuvedena') ?></small>
                        </div>
                        <?php foreach ($moje as $sluzba): ?>
                            <span class="badge text-bg-warning">Vaše služba: <?= htmlspecialchars($sluzba['nazev_typu']) ?></span>
                        <?php endforeach; ?>
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