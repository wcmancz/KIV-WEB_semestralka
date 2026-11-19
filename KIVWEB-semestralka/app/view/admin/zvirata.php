<?php
if (!defined('ROOT')) {
    header('Location: ../../../index.php?page=zvirata');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zvířata – Zoo</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php $aktivni = 'zvirata'; require ROOT . 'app/view/components/navbar.php'; ?>

<main class="container py-4 flex-grow-1">
    <h1 class="mb-4">Zvířata</h1>

    <?php if ($uspech): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($uspech) ?></div>
    <?php endif; ?>
    <?php if ($chybaAkce): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($chybaAkce) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="card shadow-sm mb-5" id="formular" novalidate style="scroll-margin-top: 5rem;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="akce" value="<?= $upravovane > 0 ? 'upravit_zvire' : 'nove_zvire' ?>">
        <?php if ($upravovane > 0): ?>
            <input type="hidden" name="id" value="<?= (int) $upravovane ?>">
        <?php endif; ?>

        <div class="card-header">
            <h2 class="h5 mb-0">
                <i class="fa-solid <?= $upravovane > 0 ? 'fa-pen' : 'fa-plus' ?> me-2"></i><?= $upravovane > 0 ? 'Upravit zvíře' : 'Nové zvíře' ?>
            </h2>
        </div>

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

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="mb-3">
                        <label for="jmeno-zvirete" class="form-label">Název zvířete</label>
                        <input type="text" class="form-control<?= isset($chyby['jmeno']) ? ' is-invalid' : '' ?>" id="jmeno-zvirete" name="jmeno_zvirete"
                               maxlength="50" autocomplete="off" required value="<?= htmlspecialchars($old['jmeno']) ?>">
                        <div class="form-text">Například „Lev pustinný“.</div>
                    </div>
                    <div>
                        <label for="potrava" class="form-label">Potrava</label>
                        <select id="potrava" name="potrava" class="form-select<?= isset($chyby['potrava']) ? ' is-invalid' : '' ?>">
                            <option value="0">Neuvedeno</option>
                            <?php foreach ($potravy as $p): ?>
                                <option value="<?= (int) $p['ID_potrava'] ?>"<?= $old['potrava'] === (int) $p['ID_potrava'] ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($p['typ_potravy']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-7">
                    <span class="form-label d-block">Pavilon</span>
                    <div class="list-group list-pick">
                        <?php foreach ($pavilony as $pav): ?>
                            <label class="list-group-item d-flex align-items-center gap-2">
                                <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="pavilon"
                                       value="<?= (int) $pav['ID_pavilon'] ?>"
                                        <?= $old['pavilon'] === (int) $pav['ID_pavilon'] ? 'checked' : '' ?>>
                                <span class="lh-sm flex-grow-1">
                                    <span class="fw-semibold"><?= htmlspecialchars($pav['jmeno_pavilonu']) ?></span>
                                    <small class="d-block text-body-secondary"><?= htmlspecialchars($pav['lokace']) ?> · <?= number_format((float) $pav['rozloha'], 0, ',', ' ') ?> m²</small>
                                </span>
                                <span class="badge rounded-pill text-bg-light border" title="Počet zvířat"><?= (int) $pav['pocet'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <?php if ($upravovane > 0): ?>
                <a href="<?= htmlspecialchars($akceUrl) ?>" class="btn btn-outline-secondary me-2">Zrušit úpravu</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Uložit změny</button>
            <?php else: ?>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Přidat zvíře</button>
            <?php endif; ?>
        </div>
    </form>

    <h2 class="h4 mb-3">Seznam zvířat</h2>

    <form method="get" action="index.php" class="card shadow-sm mb-3">
        <input type="hidden" name="page" value="zvirata">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-lg-6">
                    <label for="hledat" class="form-label mb-1">Hledat</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" class="form-control" id="hledat" name="q" maxlength="100"
                               placeholder="Zvíře, pavilon nebo potrava" value="<?= htmlspecialchars($q) ?>">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="filtr-pavilon" class="form-label mb-1">Pavilon</label>
                    <select id="filtr-pavilon" name="pavilon" class="form-select">
                        <option value="0">Všechny</option>
                        <?php foreach ($pavilony as $pav): ?>
                            <option value="<?= (int) $pav['ID_pavilon'] ?>" <?= $idPavilonuFiltr === (int) $pav['ID_pavilon'] ? 'selected' : '' ?>><?= htmlspecialchars($pav['jmeno_pavilonu']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Hledat</button>
                    <?php if ($jeFiltr): ?>
                        <a href="index.php?page=zvirata" class="btn btn-outline-secondary" title="Zrušit filtr"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-karty table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col">Zvíře</th>
                    <th scope="col">Pavilon</th>
                    <th scope="col">Potrava</th>
                    <th scope="col">Péče</th>
                    <th scope="col"><span class="visually-hidden">Akce</span></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$seznam): ?>
                    <tr><td colspan="5" class="text-center text-body-secondary py-4"><?= $jeFiltr ? 'Filtru nevyhovuje žádné zvíře.' : 'Zatím není přidáno žádné zvíře.' ?></td></tr>
                <?php endif; ?>

                <?php foreach ($seznam as $z): ?>
                    <tr<?= (int) $z['id_zvirata'] === $upravovane ? ' class="table-active"' : '' ?>>
                        <td class="karta-nadpis"><a class="fw-semibold text-decoration-none" href="index.php?page=zvire&amp;id=<?= (int) $z['id_zvirata'] ?>"><?= htmlspecialchars($z['jmeno_zvirete']) ?></a></td>
                        <td data-label="Pavilon"><?= htmlspecialchars($z['jmeno_pavilonu'] ?? 'bez pavilonu') ?></td>
                        <td data-label="Potrava"><?= htmlspecialchars($z['typ_potravy'] ?? '–') ?></td>
                        <td data-label="Péče"><?= (int) $z['pocet_pece'] ?></td>
                        <td class="cell-actions text-end text-nowrap">
                            <a href="<?= htmlspecialchars($akceUrl . '&upravit=' . (int) $z['id_zvirata'] . '#formular') ?>" class="btn btn-sm btn-outline-primary" title="Upravit zvíře"><i class="fa-solid fa-pen"></i></a>

                            <?php if ($z['duvod'] === null): ?>
                                <form method="post" action="<?= htmlspecialchars($akceUrl) ?>" class="d-inline"
                                      onsubmit="return confirm('Opravdu odebrat zvíře <?= htmlspecialchars(addslashes($z['jmeno_zvirete']), ENT_QUOTES) ?>? Nelze to vrátit.');">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="akce" value="smazat_zvire">
                                    <input type="hidden" name="id" value="<?= (int) $z['id_zvirata'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Odebrat zvíře"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php else: ?>
                                <?php /* Tlačítko je vypnuté a důvod je vidět po najetí myší. Server pravidlo kontroluje i tak. */ ?>
                                <span title="<?= htmlspecialchars($z['duvod']) ?>">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled><i class="fa-solid fa-trash"></i></button>
                                </span>
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