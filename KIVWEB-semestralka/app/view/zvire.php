<?php
if (!defined('ROOT')) {
    header('Location: ../../index.php');
    exit;
}

$cz = fn(?string $d): string => $d ? date('j. n. Y H:i', strtotime($d)) : '';

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($zvire['jmeno_zvirete']) ?> – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'prehled'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1" style="max-width: 52rem;">
    <p class="mb-2"><a href="index.php"><i class="fa-solid fa-arrow-left me-1"></i>Zpět na přehled</a></p>
    <h1 class="mb-1"><?= htmlspecialchars($zvire['jmeno_zvirete']) ?></h1>
    <p class="text-body-secondary mb-4">
        <?= htmlspecialchars($zvire['jmeno_pavilonu'] ?? 'Bez pavilonu') ?><?= $zvire['lokace'] ? ', ' . htmlspecialchars($zvire['lokace']) : '' ?>
        · <?= htmlspecialchars($zvire['typ_potravy'] ?? 'Potrava neuvedena') ?>
    </p>

    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <section class="card shadow-sm mb-4 border-warning">
        <div class="card-header d-flex justify-content-between align-items-baseline">
            <h2 class="h5 mb-0"><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Problémy (jen pro zaměstnance)</h2>
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach ($otevrene as $p): ?>
                <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-semibold"><?= htmlspecialchars($p['nazev']) ?></span>
                        <small class="d-block text-body-secondary">
                            Nahlásil(a) <?= $p['jmeno'] !== null ? htmlspecialchars($p['jmeno'] . ' ' . $p['prijmeni']) : 'bývalý zaměstnanec' ?>
                            · <?= htmlspecialchars($cz($p['nahlaseno'])) ?>
                        </small>
                    </div>
                    <?php if ($muzeVyresit): ?>
                        <form method="post" action="index.php?page=zvire&amp;id=<?= (int) $zvire['id_zvirata'] ?>">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="akce" value="vyresit_problem">
                            <input type="hidden" name="id_problemu" value="<?= (int) $p['id_problemu'] ?>">
                            <button type="submit" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-check me-1"></i>Vyřešeno</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <?php if (!$otevrene): ?>
                <li class="list-group-item text-body-secondary">Žádný neřešený problém.</li>
            <?php endif; ?>
        </ul>
        <div class="card-body border-top">
            <form method="post" action="index.php?page=zvire&amp;id=<?= (int) $zvire['id_zvirata'] ?>" class="row g-2 align-items-end">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="akce" value="pridat_problem">
                <div class="col-sm">
                    <label for="id_typ_problemu" class="form-label mb-1">Nahlásit problém</label>
                    <select class="form-select" id="id_typ_problemu" name="id_typ_problemu" required>
                        <option value="">– vyberte –</option>
                        <?php foreach ($typyProblemu as $t): ?>
                            <option value="<?= (int) $t['id_typ_problemu'] ?>"><?= htmlspecialchars($t['nazev']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary">Nahlásit</button>
                </div>
            </form>
            <?php if ($vyresene): ?>
                <p class="small text-body-secondary mt-3 mb-1">Naposledy vyřešeno:</p>
                <ul class="small text-body-secondary mb-0">
                    <?php foreach ($vyresene as $p): ?>
                        <li><?= htmlspecialchars($p['nazev']) ?> (vyřešeno <?= htmlspecialchars($cz($p['vyreseno'])) ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require ROOT . 'app/view/components/footer.php'; ?>

</body>
</html>