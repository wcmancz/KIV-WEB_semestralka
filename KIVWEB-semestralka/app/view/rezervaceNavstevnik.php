<?php
if (!defined('ROOT')) {
    header('Location: ../../index.php?page=rezervace');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rezervace prohlídky – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'rezervace'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-2">Rezervace prohlídky</h1>
    <p class="text-body-secondary">Komentovaná prohlídka zoo. Rezervaci musí schválit obsluha, o výsledku se dozvíte tady. Na jednu prohlídku se vejde nejvýše <?= (int) $kapacita ?> osob.</p>

    <?php /* Zprávy po provedené akci (zelená = povedlo se, červená = nepovedlo se). */ ?>
    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?page=rezervace" class="card shadow-sm mb-5" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="akce" value="vytvorit">
        <div class="card-header"><h2 class="h5 mb-0"><i class="fa-solid fa-calendar-plus me-2"></i>Nová rezervace</h2></div>

        <div class="card-body">
            <?php if ($chyby): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($chyby as $chyba): ?>
                            <li><?= htmlspecialchars($chyba) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="datum" class="form-label">Datum</label>
                    <input type="date" class="form-control<?= isset($chyby['datum']) ? ' is-invalid' : '' ?>" id="datum" name="datum"
                           min="<?= htmlspecialchars($minDatum) ?>" max="<?= htmlspecialchars($maxDatum) ?>" required value="<?= htmlspecialchars($old['datum']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="cas" class="form-label">Začátek prohlídky</label>
                    <select id="cas" name="cas" class="form-select<?= isset($chyby['cas']) ? ' is-invalid' : '' ?>" required>
                        <option value="" disabled<?= $old['cas'] === '' ? ' selected' : '' ?>>Vyberte…</option>
                        <?php foreach ($sloty as $s): ?>
                            <option value="<?= htmlspecialchars($s) ?>"<?= $old['cas'] === $s ? ' selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="osob" class="form-label">Počet osob</label>
                    <input type="number" class="form-control<?= isset($chyby['osob']) ? ' is-invalid' : '' ?>" id="osob" name="osob"
                           min="1" max="<?= (int) $maxOsob ?>" required value="<?= htmlspecialchars($old['osob']) ?>">
                    <div class="form-text">Nejvýše <?= (int) $maxOsob ?> osob na jednu rezervaci.</div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i>Odeslat rezervaci</button>
        </div>
    </form>

    <h2 class="h4 mb-3">Moje rezervace</h2>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col">Termín</th>
                    <th scope="col">Osob</th>
                    <th scope="col">Stav</th>
                    <th scope="col"><span class="visually-hidden">Akce</span></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$rezervace): ?>
                    <tr><td colspan="4" class="text-center text-body-secondary py-4">Zatím nemáte žádnou rezervaci.</td></tr>
                <?php endif; ?>

                <?php foreach ($rezervace as $r): ?>
                    <tr>
                        <td class="text-nowrap"><span class="fw-semibold"><?= htmlspecialchars($r['datum_cz']) ?></span> <span class="text-body-secondary"><?= htmlspecialchars($r['cas_text']) ?></span></td>
                        <td><?= (int) $r['pocet_osob'] ?></td>
                        <td class="lh-sm">
                            <span class="badge text-bg-<?= $r['barva'] ?>"><?= htmlspecialchars($r['stav']) ?></span>
                            <?php if ($r['duvod_zamitnuti']): ?>
                                <small class="d-block text-body-secondary mt-1">Důvod: <?= htmlspecialchars($r['duvod_zamitnuti']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($r['muze_zrusit']): ?>
                                <form method="post" action="index.php?page=rezervace" class="d-inline"
                                      onsubmit="return confirm('Opravdu zrušit rezervaci na <?= htmlspecialchars($r['datum_cz'], ENT_QUOTES) ?> <?= htmlspecialchars($r['cas_text'], ENT_QUOTES) ?>?');">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="akce" value="zrusit">
                                    <input type="hidden" name="id" value="<?= (int) $r['id_rezervace'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Zrušit rezervaci"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require ROOT . 'app/view/components/footer.php'; ?>
</body>
</html>