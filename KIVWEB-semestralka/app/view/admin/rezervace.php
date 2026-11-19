<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=rezervace');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rezervace prohlídek – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'rezervace'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-2">Rezervace prohlídek</h1>
    <p class="text-body-secondary">
        Schválená rezervace se v den prohlídky zahájí a po skončení dokončí.
        <?= $jeSpravce ? 'Rezervaci můžete i zamítnout (povinný důvod).' : 'Zamítnout rezervaci smí jen správce (zaměstnanec) a admin.' ?>
    </p>

    <?php /* Zprávy po provedené akci (zelená = povedlo se, červená = nepovedlo se). */ ?>
    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <form method="get" action="index.php" class="card shadow-sm mb-3">
        <input type="hidden" name="page" value="rezervace">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-sm-6 col-lg-3">
                    <label for="obdobi" class="form-label mb-1">Období</label>
                    <select id="obdobi" name="obdobi" class="form-select">
                        <?php foreach ($obdobiMoznosti as $klic => $nazev): ?>
                            <option value="<?= $klic ?>"<?= $obdobi === $klic ? ' selected' : '' ?>><?= htmlspecialchars($nazev) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="stav" class="form-label mb-1">Stav</label>
                    <select id="stav" name="stav" class="form-select">
                        <option value="0">Všechny</option>
                        <?php foreach ($stavy as $s): ?>
                            <option value="<?= (int) $s['id_stav'] ?>"<?= $stav === (int) $s['id_stav'] ? ' selected' : '' ?>><?= htmlspecialchars($s['nazev']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Zobrazit</button>
                    <?php if ($jeFiltr): ?>
                        <a href="index.php?page=rezervace" class="btn btn-outline-secondary" title="Zrušit filtr"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-karty align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col">Termín</th>
                    <th scope="col">Návštěvník</th>
                    <th scope="col">Osob</th>
                    <th scope="col">Stav</th>
                    <th scope="col"><span class="visually-hidden">Akce</span></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$rezervace): ?>
                    <tr><td colspan="5" class="text-center text-body-secondary py-4"><?= $jeFiltr ? 'Filtru nevyhovuje žádná rezervace.' : 'Žádné nadcházející rezervace.' ?></td></tr>
                <?php endif; ?>

                <?php foreach ($rezervace as $r): ?>
                    <tr>
                        <td class="text-nowrap" data-label="Termín"><span class="fw-semibold"><?= htmlspecialchars($r['datum_cz']) ?></span> <span class="text-body-secondary"><?= htmlspecialchars($r['cas_text']) ?></span></td>
                        <td class="lh-sm" data-label="Návštěvník">
                            <?= htmlspecialchars($r['navstevnik']) ?>
                            <small class="d-block text-body-secondary"><?= htmlspecialchars($r['email']) ?></small>
                        </td>
                        <td data-label="Osob"><?= (int) $r['pocet_osob'] ?></td>
                        <td class="lh-sm" data-label="Stav">
                            <span class="badge text-bg-<?= $r['barva'] ?>"><?= htmlspecialchars($r['stav']) ?></span>
                            <?php if ($r['duvod_zamitnuti']): ?>
                                <small class="d-block text-body-secondary mt-1">Důvod: <?= htmlspecialchars($r['duvod_zamitnuti']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap cell-actions">
                            <?php foreach ([
                                               ['muze_schvalit', 'schvalit', 'Schválit', 'fa-check', 'btn-outline-success'],
                                               ['muze_zahajit', 'zahajit', 'Zahájit prohlídku', 'fa-play', 'btn-outline-primary'],
                                               ['muze_dokoncit', 'dokoncit', 'Dokončit prohlídku', 'fa-flag-checkered', 'btn-outline-success'],
                                           ] as [$povoleno, $akce, $popis, $ikona, $trida]): ?>
                                <?php if ($r[$povoleno]): ?>
                                    <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="d-inline">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                        <input type="hidden" name="akce" value="<?= $akce ?>">
                                        <input type="hidden" name="id" value="<?= (int) $r['id_rezervace'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $trida ?>" title="<?= htmlspecialchars($popis) ?>"><i class="fa-solid <?= $ikona ?>"></i></button>
                                    </form>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($r['muze_zamitnout']): ?>
                                <?php /* Zamítnutí: tlačítko otevře společné dialogové okno (mimo tabulku), důvod se píše tam. */ ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Zamítnout rezervaci"
                                        data-zamitnout="<?= (int) $r['id_rezervace'] ?>"
                                        data-popis="<?= htmlspecialchars($r['datum_cz'] . ' ' . $r['cas_text'] . ' – ' . $r['navstevnik']) ?>">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<dialog id="dialog-zamitnout" class="border-0 rounded shadow p-0" style="width: min(28rem, 92vw);">
    <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="p-3">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="akce" value="zamitnout">
        <input type="hidden" name="id" id="zamitnout-id" value="">
        <h2 class="h5">Zamítnout rezervaci</h2>
        <p class="small text-body-secondary mb-3" id="zamitnout-popis"></p>
        <label for="zamitnout-duvod" class="form-label">Důvod zamítnutí</label>
        <input type="text" class="form-control mb-3" id="zamitnout-duvod" name="duvod" maxlength="255" required>
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" id="zamitnout-zavrit">Zrušit</button>
            <button type="submit" class="btn btn-danger">Zamítnout</button>
        </div>
    </form>
</dialog>
<script>
    (function () {
        const dlg = document.getElementById('dialog-zamitnout');
        document.querySelectorAll('[data-zamitnout]').forEach(function (tl) {
            tl.addEventListener('click', function () {
                document.getElementById('zamitnout-id').value = tl.dataset.zamitnout;
                document.getElementById('zamitnout-popis').textContent = tl.dataset.popis;
                document.getElementById('zamitnout-duvod').value = '';
                dlg.showModal();
                document.getElementById('zamitnout-duvod').focus();
            });
        });
        document.getElementById('zamitnout-zavrit').addEventListener('click', function () { dlg.close(); });
        dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
    })();
</script>

<?php require ROOT . 'app/view/components/footer.php'; ?>
</body>
</html>